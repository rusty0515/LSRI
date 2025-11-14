<?php

namespace App\Livewire\Pages;

use App\Models\Order;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;

class CustomerDashboard extends Component
{

    public $orders;
    public $order_status = 'new';

    public function mount()
    {
        $this->getOrders();
    }

    public function updatedOrderStatus(){
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


    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.pages.customer-dashboard', [
            'orders' => $this->orders
        ]);
    }
}
