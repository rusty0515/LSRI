<?php

namespace App\Livewire\Pages;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Service;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Enums\VehicleTypeEnum;
use App\Models\ServiceRequest;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Illuminate\Support\Facades\DB;

class CustomerDashboard extends Component
{
    public $orders;
    public $order_status = 'new';
    public $activeSegment = 'orders';
    public $activeOrderTab = 'in_progress';
    public $activeRequestTab = 'pending';
    public $showRequestModal = false;
    public $showOrderDetailsModal = false;
    public $selectedOrder;

    // Request form properties
    public $selectedVehicleType = 'car';
    public $selectedMechanic;
    public $requestedDate;
    public $scheduledDate;
    public $message = '';
    public $selectedServices = [];
    public $vehicleBrand = '';
    public $vehicleModel = '';

    public $mechanics;
    public $services;
    public $serviceRequests;
    public $vehicles;

    public function updatedOrderStatus()
    {
        $this->getOrders();
    }

    protected function getOrders()
    {
        $this->orders = Order::query()
            ->with(['orderItems'])
            ->where('user_id', auth()->user()->id)
            ->where('order_status', $this->order_status)
            ->latest()
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
    }

    protected $rules = [
        'selectedVehicleType' => 'required|in:car,motorcycle,other',
        'vehicleBrand' => 'required_if:selectedVehicleType,other',
        'vehicleModel' => 'required_if:selectedVehicleType,other',
        'selectedMechanic' => 'required|exists:users,id',
        'requestedDate' => 'required|date|after_or_equal:today',
        'scheduledDate' => 'required|date|after_or_equal:requestedDate',
        'selectedServices' => 'required|array|min:1',
        'selectedServices.*' => 'exists:services,id',
        'message' => 'required|string|max:500',
    ];

    public function mount()
    {
        $this->loadInitialData();
        $this->setDefaultDates();
    }

    private function loadInitialData()
    {
        $this->loadMechanics();
        $this->loadServices();
        $this->loadServiceRequests();
        $this->loadOrders();
        $this->loadVehicles();
    }

    private function setDefaultDates()
    {
        $this->requestedDate = Carbon::today()->format('Y-m-d');
        $this->scheduledDate = Carbon::tomorrow()->format('Y-m-d');
    }

    public function loadMechanics()
    {
        $this->mechanics = User::whereHas('roles', fn($q) => $q->where('name', 'mechanic'))->get();

        if ($this->mechanics->isEmpty()) {
            $this->mechanics = User::where('id', '!=', auth()->id())->limit(5)->get();
        }
    }

    public function loadServices()
    {
        $this->services = Service::all();
    }

    public function loadServiceRequests()
    {
        $this->serviceRequests = ServiceRequest::where('user_id', auth()->id())
            ->when($this->activeRequestTab !== 'all', fn($q) => $q->where('status', $this->activeRequestTab))
            ->take(10)
            ->latest()
            ->get();
    }

    public function loadOrders()
    {
        $this->orders = Order::where('user_id', auth()->id())
            ->when($this->activeOrderTab !== 'all', function ($q) {
                $statusMap = [
                    'in_progress' => ['new', 'processing'],
                    'shipped' => ['shipped'],
                    'delivered' => ['delivered'],
                    'cancelled' => ['cancelled']
                ];

                if (isset($statusMap[$this->activeOrderTab])) {
                    $q->whereIn('order_status', $statusMap[$this->activeOrderTab]);
                }
            })
            ->take(10)
            ->latest()
            ->get();
    }

    public function loadVehicles()
    {
        $this->vehicles = ServiceRequest::where('user_id', auth()->id())
            ->select('vehicle_type', 'remarks')
            ->selectRaw('MIN(created_at) as first_used')
            ->selectRaw('MAX(created_at) as last_used')
            ->selectRaw('COUNT(*) as service_count')
            ->groupBy('vehicle_type', 'remarks')
            ->latest('last_used')
            ->get()
            ->map(function ($request) {
                return [
                    'type' => $request->vehicle_type,
                    'type_label' => VehicleTypeEnum::from($request->vehicle_type)->getLabel(),
                    'brand_model' => $this->extractBrandModel($request->remarks),
                    'service_count' => $request->service_count,
                    'first_used' => Carbon::parse($request->first_used),
                    'last_used' => Carbon::parse($request->last_used),
                    'remarks' => $request->remarks
                ];
            });
    }

    private function extractBrandModel($remarks)
    {
        if (!$remarks) return 'Not specified';

        if (str_contains($remarks, 'Vehicle Details:')) {
            $lines = explode("\n", $remarks);
            foreach ($lines as $line) {
                if (str_contains($line, 'Brand:') && str_contains($line, 'Model:')) {
                    preg_match('/Brand:\s*(.*?),?\s*Model:\s*(.*)/', $line, $matches);
                    if (count($matches) >= 3) {
                        return trim($matches[1]) . ' ' . trim($matches[2]);
                    }
                }
            }
        }

        return 'Not specified';
    }

    // Order Details Modal
    public function openOrderDetails($orderId)
    {
        $this->selectedOrder = Order::with('orderItems')->find($orderId);
        $this->showOrderDetailsModal = true;
    }

    public function closeOrderDetailsModal()
    {
        $this->showOrderDetailsModal = false;
        $this->selectedOrder = null;
    }

    public function cancelOrder($orderId)
    {
        try {
            DB::transaction(function () use ($orderId) {
                $order = Order::where('user_id', auth()->id())
                    ->with(['orderItems', 'orderItems.product'])
                    ->findOrFail($orderId);

                $this->restoreProductStock($order);

                // Update order status to cancelled
                $order->update(['order_status' => 'cancelled']);

                $this->loadOrders();
            });
        } catch (\Throwable $e) {
            session()->flash('error', 'Failed to cancel order: ' . $e->getMessage());
        }
    }

    // Segment and tab management
    public function setActiveSegment($segment)
    {
        $this->activeSegment = $segment;
    }

    public function setActiveOrderTab($tab)
    {
        $this->activeOrderTab = $tab;
        $this->loadOrders();
    }

    public function setActiveRequestTab($tab)
    {
        $this->activeRequestTab = $tab;
        $this->loadServiceRequests();
    }

    // Modal management
    public function openRequestModal()
    {
        if ($this->mechanics->isEmpty()) {
            session()->flash('error', 'No mechanics available right now.');
            return;
        }

        $this->showRequestModal = true;
        $this->resetValidation();

        if (!$this->selectedMechanic && $this->mechanics->isNotEmpty()) {
            $this->selectedMechanic = $this->mechanics->first()->id;
        }
    }

    public function closeRequestModal()
    {
        $this->showRequestModal = false;
        $this->resetRequestForm();
    }


    public function resetRequestForm()
    {
        $this->reset([
            'selectedVehicleType',
            'selectedMechanic',
            'requestedDate',
            'scheduledDate',
            'message',
            'selectedServices',
            'vehicleBrand',
            'vehicleModel',
        ]);

        $this->setDefaultDates();
        $this->selectedVehicleType = 'car';
        $this->resetValidation();
    }

    // This will automatically update when selectedVehicleType changes
    public function updatedSelectedVehicleType($value)
    {
        // Reset brand and model when switching away from "other"
        if ($value !== 'other') {
            $this->vehicleBrand = '';
            $this->vehicleModel = '';
        }
    }

    public function saveRequest()
    {
        $this->validate();

        try {
            DB::transaction(function () {
                $serviceNumber = 'SR-' . now()->format('Ymd') . '-' . str_pad(ServiceRequest::count() + 1, 4, '0', STR_PAD_LEFT);

                $remarks = $this->message;
                if ($this->selectedVehicleType === 'other' && ($this->vehicleBrand || $this->vehicleModel)) {
                    $details = "Vehicle Details: Brand: {$this->vehicleBrand}, Model: {$this->vehicleModel}";
                    $remarks = $remarks ? "{$remarks}\n\n{$details}" : $details;
                }

                $serviceRequest = ServiceRequest::create([
                    'service_number' => $serviceNumber,
                    'user_id' => auth()->id(),
                    'mechanic_id' => $this->selectedMechanic,
                    'vehicle_type' => $this->selectedVehicleType,
                    'requested_date' => $this->requestedDate,
                    'scheduled_date' => $this->scheduledDate,
                    'remarks' => $remarks,
                    'status' => 'pending',
                ]);

                foreach ($this->selectedServices as $serviceId) {
                    $serviceRequest->items()->create([
                        'service_id' => $serviceId,
                        'quantity' => 1,
                        'subtotal_price' => Service::find($serviceId)->service_price ?? 0,
                    ]);
                }
            });

           // $this->showRequestModal = false;
            $this->resetRequestForm();
            $this->loadServiceRequests();
            $this->loadVehicles();

            session()->flash('success', 'Service request submitted successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to submit service request: ' . $e->getMessage());
        }
    }

    public function cancelRequest($id)
    {
        try {
            $req = ServiceRequest::where('user_id', auth()->id())->findOrFail($id);

            if (in_array($req->status, ['pending', 'in_progress'])) {
                $req->update(['status' => 'cancelled']);
                $this->loadServiceRequests();
                session()->flash('success', 'Request cancelled.');
            } else {
                session()->flash('error', 'Cannot cancel this request.');
            }
        } catch (\Throwable $e) {
            session()->flash('error', 'Failed to cancel: ' . $e->getMessage());
        }
    }

    public function deleteVehicle($vehicleType, $remarks)
    {
        try {
            $deletedCount = ServiceRequest::where('user_id', auth()->id())
                ->where('vehicle_type', $vehicleType)
                ->where('remarks', $remarks)
                ->delete();

            $this->loadVehicles();
            $this->loadServiceRequests();
            session()->flash('success', "Vehicle history deleted successfully. Removed {$deletedCount} service requests.");
        } catch (\Throwable $e) {
            session()->flash('error', 'Failed to delete vehicle: ' . $e->getMessage());
        }
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.pages.customer-dashboard', [
            'orders' => $this->orders
        ]);
    }

    /**
     * Restore product stock when order is cancelled
     */
    protected function restoreProductStock(Order $order): void
    {
        foreach ($order->orderItems as $orderItem) {
            $product = Product::find($orderItem->product_id);
            if ($product) {
                // Restore the stock that was taken when the order was placed
                $product->increment('prod_security_stock', $orderItem->quantity);
            }
        }
    }
}
