<?php

namespace App\Livewire\Pages;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\Product;
use Livewire\Component;
use App\Models\OrderItem;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use App\Enums\OrderStatusEnum;
use Livewire\Attributes\Layout;
use App\Enums\PaymentMethodEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Woenel\Prpcmblmts\Models\PhilippineCity;
use Woenel\Prpcmblmts\Models\PhilippineRegion;
use Luigel\Paymongo\Facades\Paymongo;
use Woenel\Prpcmblmts\Models\PhilippineBarangay;
use App\Livewire\Traits\HasRemoveItem;
use Woenel\Prpcmblmts\Models\PhilippineProvince;
use App\Livewire\Traits\HasAlertNotification;

class Checkout extends Component
{
    use HasAlertNotification, HasRemoveItem;

    public array $cart = [];
    public float $total = 0;
    public float $sub_total = 0;
    public float $tax = 0;

    // Address properties
    public $selectedRegion;
    public $selectedProvince;
    public $selectedCity;
    public $selectedBarangay;
    public $street;
    public $fullAddress;

    // Collections for dropdowns
    public $regions;
    public $provinces = [];
    public $cities = [];
    public $barangays = [];

    public $customer_amount = 100;
    public $customer_payment_method = '';

    // Card-related properties
    public $card_name = '';
    public $card_number = '';
    public $expiration_month = '';
    public $expiration_year = '';
    public $cvv = '';

    // Remove complex objects from public properties
    public $paymentIntent_id = '';

    // Make these protected instead of public
    protected $paymentIntent;
    protected $paymentMethod;
    protected $createPaymentIntent;

    public $shipping_price = 0;
    public $distance_in_km = 0;
    public $is_calculating_shipping = false;

    #[On('checkout-updated')]
    public function mount()
    {
        $this->cart = session()->get('cart', []);
        $this->calculateSubTotal();
        $this->calculateTax();
        $this->calculateTotal();

        // $this->calculateDistanceAndShipping();

        // Load initial regions
        $this->regions = PhilippineRegion::all();
    }

    // Address related methods
    public function updatedSelectedRegion($regionId)
    {
        if (!empty($regionId)) {
            // Use the ID for querying, not the region_code
            $this->provinces = PhilippineProvince::where('region_code', $regionId)->get();
            // dd($regionId);
        } else {
            $this->provinces = collect();
            $this->selectedProvince = '';
            $this->cities = collect();
            $this->selectedCity = '';
            $this->barangays = collect();
            $this->selectedBarangay = '';
        }
        $this->updateFullAddress();
        $this->calculateDistanceAndShipping();
    }

    public function updatedSelectedProvince($provinceId)
    {
        if (!empty($provinceId)) {
            // Use the ID for querying, not the province_code
            // dd($provinceId);
            $this->cities = PhilippineCity::where('province_code', $provinceId)->get();
        } else {
            $this->cities = collect();
            $this->selectedCity = '';
            $this->barangays = collect();
            $this->selectedBarangay = '';
        }

        $this->updateFullAddress();
        $this->calculateDistanceAndShipping();
    }

    public function updatedSelectedCity($cityId)
    {
        if (!empty($cityId)) {
            // Use the ID for querying, not the city_code
            $this->barangays = PhilippineBarangay::where('city_code', $cityId)->get();
        } else {
            $this->barangays = collect();
            $this->selectedBarangay = '';
        }

        $this->updateFullAddress();
        $this->calculateDistanceAndShipping();
    }

    public function updatedSelectedBarangay()
    {;

        $this->updateFullAddress();
        $this->calculateDistanceAndShipping();
    }

    public function updatedStreet()
    {
        $this->updateFullAddress();
    }

    protected function updateFullAddress()
    {
        $addressParts = [];

        if (!empty($this->street)) {
            $addressParts[] = $this->street;
        }

        if (!empty($this->selectedBarangay)) {
            // Query the specific selected barangay using its code
            $barangay = PhilippineBarangay::where('id', $this->selectedBarangay)->first();
            if ($barangay) {
                $addressParts[] = $barangay->name;
            }
        }

        if (!empty($this->selectedCity)) {
            // Query the specific selected city using its code
            $city = PhilippineCity::where('code', $this->selectedCity)->first();
            if ($city) {
                $addressParts[] = $city->name;
            }
        }

        if (!empty($this->selectedProvince)) {
            // Query the specific selected province using its code
            $province = PhilippineProvince::where('code', $this->selectedProvince)->first();
            if ($province) {
                $addressParts[] = $province->name;
            }
        }

        if (!empty($this->selectedRegion)) {
            // Query the specific selected region using its code
            $region = PhilippineRegion::where('code', $this->selectedRegion)->first();
            if ($region) {
                $addressParts[] = $region->name;
            }
        }

        $this->fullAddress = implode(', ', $addressParts);
    }
    public function updateQuantity($productId, $quantity)
    {
        if ($quantity < 1) {
            $this->notify('Minimum quantity is 1', 'error', 3000);
            return;
        }

        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] = (int) $quantity;
            session()->put('cart', $cart);
        }

        $this->cart = $cart;

        $this->dispatch('cart-updated');
        $this->dispatch('checkout-updated');

        $this->calculateTotal();
        $this->calculateTax();
        // $this->calculateShipping();
        $this->calculateSubTotal();
    }

    public function removeItem($id)
    {
        return $this->removeCartItem($id);
    }

    public function checkout()
    {
        // Validate required fields including address
        if (empty($this->cart)) {
            $this->notify('Your cart is empty', 'error', 3000);
            return;
        }

        if (empty($this->customer_payment_method)) {
            $this->notify('Please select a payment method', 'error', 3000);
            return;
        }

        // Validate address
        $addressValidation = $this->validateAddress();
        if (!$addressValidation) {
            return;
        }

        // Map frontend payment methods to enum values
        $paymentMethod = $this->mapPaymentMethodToEnum();

        // Validate card details if payment method is credit card
        if ($paymentMethod === PaymentMethodEnum::CREDIT_CARD) {
            $cardValidation = $this->validateCardDetails();
            if (!$cardValidation) {
                return;
            }
        }

        try {
            // For COD payments - handle separately without transaction
            if ($this->customer_payment_method === 'cod') {
                return $this->handleCODOrder($paymentMethod);
            }

            // For online payments - use transaction
            DB::transaction(function () use ($paymentMethod) {
                // Generate unique order number
                $orderNumber = '#ORDER-' . date('His-') . strtoupper(Str::random(6));

                // Create the order first with pending payment status
                $order = Order::create([
                    'user_id' => Auth::id(),
                    'order_number' => $orderNumber,
                    'order_total_price' => $this->total,
                    'order_status' => OrderStatusEnum::New->value,
                    'shipping_price' => $this->shipping_price,
                    'distance_in_km' => $this->distance_in_km,
                    'payment_method' => $paymentMethod->value,
                    'payment_status' => 'pending',
                    'payment_reference' => $this->generatePaymentReference($paymentMethod),
                    'order_notes' => '',
                ]);

                // Create address record - FIXED: Ensure we're using valid IDs
                $address = \App\Models\Address::create([
                    'region_id' => $this->getValidRegionId($this->selectedRegion),
                    'province_id' => $this->getValidProvinceId($this->selectedProvince),
                    'city_id' => $this->getValidCityId($this->selectedCity),
                    'barangay_id' => $this->getValidBarangayId($this->selectedBarangay),
                    'street' => $this->street,
                    'full_address' => $this->fullAddress,
                    'address_type' => 'shipping',
                ]);

                // Attach address to order
                $order->addresses()->attach($address->id);

                // Create order items
                foreach ($this->cart as $productId => $item) {
                    $product = Product::find($productId);

                    if ($product) {
                        OrderItem::create([
                            'order_id' => $order->id,
                            'product_id' => $productId,
                            'quantity' => $item['quantity'],
                            'unit_price' => $item['price'],
                            'subtotal' => $this->sub_total,
                        ]);
                    }
                    $this->reduceProductStock($product, $item['quantity']);
                }

                // Handle online payments
                if (in_array($this->customer_payment_method, ['gcash', 'paymaya', 'grab_pay', 'card'])) {
                    // Create payment intent
                    $this->paymentCreateIntent($this->total);

                    // Create payment method
                    $this->paymentCreateMethod();

                    // Update order with payment intent ID
                    $order->update([
                        'payment_intent_id' => $this->paymentIntent_id
                    ]);

                    // Attach payment method to payment intent
                    $attachedPaymentIntent = $this->createPaymentIntent->attach($this->paymentMethod->id, route('callback'));

                    // For redirect-based payments (gcash, paymaya, grab_pay)
                    if (isset($attachedPaymentIntent->next_action['redirect']['url'])) {
                        DB::commit();

                        // Clear the cart before redirect
                        session()->forget('cart');
                        $this->cart = [];

                        // Redirect to Paymongo payment page
                        return redirect()->away($attachedPaymentIntent->next_action['redirect']['url']);
                    }

                    // For direct card payments that succeed immediately
                    if ($attachedPaymentIntent->status === 'succeeded') {
                        $order->update(['payment_status' => 'paid']);

                        // Clear the cart after successful payment
                        session()->forget('cart');
                        $this->cart = [];

                        DB::commit();

                        $this->notify('Order placed successfully!', 'success', 5000);
                        return redirect()->route('page.customer-dashboard')->with('success', 'Order placed successfully!');
                    } else {
                        // Payment failed or requires authentication
                        throw new \Exception('Payment processing failed with status: ' . $attachedPaymentIntent->status);
                    }
                }
            });
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Checkout error: ' . $e->getMessage());
            
            // dd($e->getMessage());
            $this->notify('Failed to place order. Please try again.', 'error', 5000);
            return;
        }
    }

    /**
     * Helper methods to ensure valid IDs are used
     */
    protected function getValidRegionId($regionValue)
    {
        // If it's already a valid ID, return it
        if (is_numeric($regionValue)) {
            $region = PhilippineRegion::find($regionValue);
            if ($region) {
                //dd($region);
                return $region->id;
            }
        }

        // If it's a code, find the region by code and return its ID
        $region = PhilippineRegion::where('code', $regionValue)->first();
        return $region ? $region->id : null;
    }

    protected function getValidProvinceId($provinceValue)
    {
        // If it's already a valid ID, return it
        if (is_numeric($provinceValue)) {
            $province = PhilippineProvince::find($provinceValue);
            if ($province) {
                return $province->id;
            }
        }

        // If it's a code, find the province by code and return its ID
        $province = PhilippineProvince::where('code', $provinceValue)->first();
        return $province ? $province->id : null;
    }

    protected function getValidCityId($cityValue)
    {
        // If it's already a valid ID, return it
        if (is_numeric($cityValue)) {
            $city = PhilippineCity::find($cityValue);
            if ($city) {
                return $city->id;
            }
        }

        // If it's a code, find the city by code and return its ID
        $city = PhilippineCity::where('code', $cityValue)->first();
        return $city ? $city->id : null;
    }

    protected function getValidBarangayId($barangayValue)
    {
        // If it's already a valid ID, return it
        if (is_numeric($barangayValue)) {
            $barangay = PhilippineBarangay::find($barangayValue);
            if ($barangay) {
                return $barangay->id;
            }
        }

        // If it's a code, find the barangay by code and return its ID
        $barangay = PhilippineBarangay::where('city_code', $barangayValue)->first();
        return $barangay ? $barangay->id : null;
    }

    protected function validateAddress()
    {
        try {
            $this->validate([
                'selectedRegion' => 'required',
                'selectedProvince' => 'required',
                'selectedCity' => 'required',
                'selectedBarangay' => 'required',
                'street' => 'required|string|max:255',
            ], [
                'selectedRegion.required' => 'Please select a region',
                'selectedProvince.required' => 'Please select a province',
                'selectedCity.required' => 'Please select a city/municipality',
                'selectedBarangay.required' => 'Please select a barangay',
                'street.required' => 'Street address is required',
            ]);

            // Additional validation to ensure the IDs exist in the database
            if (!$this->getValidRegionId($this->selectedRegion)) {
                throw new \Exception('Invalid region selected');
            }
            if (!$this->getValidProvinceId($this->selectedProvince)) {
                throw new \Exception('Invalid province selected');
            }
            if (!$this->getValidCityId($this->selectedCity)) {
                throw new \Exception('Invalid city selected');
            }
            if (!$this->getValidBarangayId($this->selectedBarangay)) {
                throw new \Exception('Invalid barangay selected');
            }

            return true;
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->notify('Please complete your delivery address', 'error', 3000);
            return false;
        } catch (\Exception $e) {
            //dd($e->getMessage());
            $this->notify('Invalid address selected. Please try again.', 'error', 3000);
            return false;
        }
    }

    protected function mapPaymentMethodToEnum(): PaymentMethodEnum
    {
        return match ($this->customer_payment_method) {
            'gcash', 'paymaya', 'grab_pay' => PaymentMethodEnum::BANK_TRANSFER,
            'card' => PaymentMethodEnum::CREDIT_CARD,
            default => PaymentMethodEnum::COD,
        };
    }

    public function paymentCreateMethod()
    {
        if (in_array($this->customer_payment_method, ['gcash', 'paymaya', 'grab_pay'])) {
            $this->paymentMethod = Paymongo::paymentMethod()->create([
                'type' => $this->customer_payment_method,
                'amount' => $this->total * 100, // Convert to centavos
                'currency' => 'PHP',
            ]);
        }

        // Card logic
        if ($this->customer_payment_method === 'card') {
            $cardNumber = preg_replace('/[^0-9]/', '', $this->card_number);
            $this->paymentMethod = Paymongo::paymentMethod()->create([
                'type' => 'card',
                'details' => [
                    'card_number' => (string)$cardNumber,
                    'exp_month' => (int)$this->expiration_month,
                    'exp_year' => (int)$this->expiration_year,
                    'cvc' => $this->cvv,
                ],
                'billing' => [
                    'email' => Auth::user()->email,
                    'name' => $this->card_name,
                ],
            ]);
        }
    }

    // Update paymentCreateIntent to use the correct amount format
    public function paymentCreateIntent($amount)
    {
        $paymentIntent = Paymongo::paymentIntent()->create([
            'amount' => $amount,
            'payment_method_allowed' => [
                'card',
                'paymaya',
                'grab_pay',
                'gcash',
            ],
            'currency' => 'PHP',
            'description' => 'LSRI Shopping Order',
            'statement_descriptor' => 'LSRI SHOPPING',
        ]);

        $this->paymentIntent_id = $paymentIntent->id;
        $this->createPaymentIntent = $paymentIntent;
    }

    protected function validateCardDetails()
    {
        try {
            $this->validate([
                'card_name' => 'required|string|max:255',
                'card_number' => 'required|string|size:16',
                'expiration_month' => 'required|numeric|between:1,12',
                'expiration_year' => 'required|numeric|min:' . date('Y'),
                'cvv' => 'required|string|size:3',
            ], [
                'card_name.required' => 'Cardholder name is required',
                'card_number.required' => 'Card number is required',
                'card_number.size' => 'Card number must be 16 digits',
                'expiration_month.required' => 'Expiration month is required',
                'expiration_month.between' => 'Invalid expiration month',
                'expiration_year.required' => 'Expiration year is required',
                'expiration_year.min' => 'Card has expired',
                'cvv.required' => 'CVV is required',
                'cvv.size' => 'CVV must be 3 digits',
            ]);

            // Additional validation for card expiration
            $currentYear = date('Y');
            $currentMonth = date('m');

            if ($this->expiration_year == $currentYear && $this->expiration_month < $currentMonth) {
                $this->addError('expiration_month', 'Card has expired');
                return false;
            }

            return true;
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->notify('Please check your card details', 'error', 3000);
            return false;
        }
    }

    protected function generatePaymentReference(PaymentMethodEnum $paymentMethod): string
    {
        $prefix = match ($paymentMethod) {
            PaymentMethodEnum::COD => 'COD',
            PaymentMethodEnum::BANK_TRANSFER => 'BANK',
            PaymentMethodEnum::CREDIT_CARD => 'CARD',
        };

        return $prefix . '-' . time() . '-' . strtoupper(uniqid());
    }

    public function checkAmount()
    {
        if ($this->customer_amount < $this->total) {
            $this->dispatch('checkout-updated');
            return $this->notify('Insufficient amount', 'error', 3000);
        }
    }

    protected function calculateTotal()
    {
        $this->total = $this->sub_total + $this->tax + $this->shipping_price;
    }

    protected function calculateSubTotal()
    {
        $this->sub_total = 0;

        foreach ($this->cart as $productId => $item) {
            $product = Product::with('discounts')->find($productId);
            if (!$product) continue;

            $quantity = $item['quantity'];
            $originalPrice = $product->prod_price;
            $discountedPrice = $this->getDiscountedPrice($product);

            $this->cart[$productId]['original_price'] = $originalPrice;
            $this->cart[$productId]['price'] = $discountedPrice;
            $this->cart[$productId]['has_discount'] = $originalPrice != $discountedPrice;
            $this->cart[$productId]['discount_label'] = $product->discounts->first()?->discount_name;

            $this->sub_total += $discountedPrice * $quantity;
        }

        return $this->sub_total;
    }

    protected function calculateTax()
    {
        $this->tax = 0;

        foreach ($this->cart as $productId => $item) {
            $quantity = $item['quantity'];
            $price = $item['price'];
            $this->tax += ($price * $quantity) * 0.12;
        }

        return $this->tax;
    }

    private function getDiscountedPrice($product)
    {
        $now = Carbon::now();

        $activeDiscount = $product->discounts->first(function ($discount) use ($now) {
            return $now->between($discount->starts_at, $discount->ends_at);
        });

        $originalPrice = $product->prod_price;

        if (!$activeDiscount) {
            return $originalPrice;
        }

        $type = $activeDiscount->pivot->discount_type;
        $value = $activeDiscount->pivot->discount_value;

        if ($type === 'percentage') {
            return max(0, $originalPrice - ($originalPrice * $value / 100));
        }

        if ($type === 'fixed') {
            return max(0, $originalPrice - $value);
        }

        return $originalPrice;
    }

    protected function handleCODOrder($paymentMethod)
    {
        try {
            DB::transaction(function () use ($paymentMethod) {
                // Generate unique order number
                $orderNumber = '#ORDER-' . date('His-') . strtoupper(Str::random(6));

                // Create the order
                $order = Order::create([
                    'user_id' => Auth::id(),
                    'order_number' => $orderNumber,
                    'order_total_price' => $this->total,
                    'order_status' => OrderStatusEnum::New->value,
                    'shipping_price' => $this->shipping_price,
                    'distance_in_km' => $this->distance_in_km,
                    'payment_method' => $paymentMethod->value,
                    'payment_status' => 'pending',
                    'payment_reference' => $this->generatePaymentReference($paymentMethod),
                    'order_notes' => '',
                ]);

                // Create address record - FIXED: Use the helper methods
                $address = \App\Models\Address::create([
                    'region_id' => $this->getValidRegionId($this->selectedRegion),
                    'province_id' => $this->getValidProvinceId($this->selectedProvince),
                    'city_id' => $this->getValidCityId($this->selectedCity),
                    'barangay_id' => $this->getValidBarangayId($this->selectedBarangay),
                    'street' => $this->street,
                    'full_address' => $this->fullAddress,
                    'address_type' => 'shipping',
                ]);

                // Attach address to order
                $order->addresses()->attach($address->id);

                // Create order items
                foreach ($this->cart as $productId => $item) {
                    $product = Product::find($productId);

                    if ($product) {
                        OrderItem::create([
                            'order_id' => $order->id,
                            'product_id' => $productId,
                            'quantity' => $item['quantity'],
                            'unit_price' => $item['price'],
                            'subtotal' => $item['price'] * $item['quantity'],
                        ]);
                    }
                    $this->reduceProductStock($product, $item['quantity']);
                }

                // Clear the cart after successful order
                session()->forget('cart');
                $this->cart = [];
            });

            // Show success message for COD
            $this->notify('Order placed successfully!', 'success', 5000);
            return redirect()->route('page.customer-dashboard')->with('success', 'Order placed successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('COD Order error: ' . $e->getMessage());
           
            $this->notify('Failed to place order. Please try again.', 'error', 5000);
            return;
        }
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.pages.checkout');
    }



    protected function calculateDistanceAndShipping()
    {
        $this->is_calculating_shipping = true;

        try {
            if (!empty($this->selectedRegion)) {
                $region = PhilippineRegion::where('code', $this->selectedRegion)->first();

                if ($region) {
                    // $shippingData = $this->getShippingByRegion($region->name);
                    if ($region->name === 'REGION VI (WESTERN VISAYAS)' && !empty($this->selectedCity)) {
                       // dd($this->selectedCity);
                        $shippingData = $this->getShippingByCity($this->selectedCity);
                    } else {
                        $shippingData = $this->getShippingByRegion($region->name);
                    }
                    $this->distance_in_km = $shippingData['distance'];
                    $this->shipping_price = $shippingData['price'];
                } else {
                    $this->shipping_price = 50;
                    $this->distance_in_km = 0;
                }
            } else {
                $this->shipping_price = 50;
                $this->distance_in_km = 0;
            }
        } catch (\Exception $e) {
            $this->shipping_price = 50;
            $this->distance_in_km = 0;
        }

        $this->is_calculating_shipping = false;
        $this->calculateTotal();
    }

    protected function getShippingByRegion($regionName)
    {
        $shippingRates = [
            // National Capital Region (NCR) - Manila area
            'NATIONAL CAPITAL REGION (NCR)' => ['distance' => 580, 'price' => 150],

            // Region I - Ilocos Region
            'REGION I (ILOCOS REGION)' => ['distance' => 750, 'price' => 150],

            // Region II - Cagayan Valley
            'REGION II (CAGAYAN VALLEY)' => ['distance' => 950, 'price' => 150],

            // Region III - Central Luzon
            'REGION III (CENTRAL LUZON)' => ['distance' => 650, 'price' => 150],

            // Region IV-A - CALABARZON
            'REGION IV-A (CALABARZON)' => ['distance' => 620, 'price' => 150],

            // Region IV-B - MIMAROPA
            'REGION IV-B (MINAROPA)' => ['distance' => 450, 'price' => 150],

            // Region V - Bicol Region
            'REGION V (BICOL REGION)' => ['distance' => 550, 'price' => 150],

            // Region VI - Western Visayas (SAME REGION - Negros Occidental is here)
            'REGION VI (WESTERN VISAYAS)' => ['distance' => 80, 'price' => 80],

            // Region VII - Central Visayas (Cebu, Bohol, etc.)
            'REGION VII (CENTRAL VISAYAS)' => ['distance' => 150, 'price' => 100],

            // Region VIII - Eastern Visayas
            'REGION VIII (EASTERN VISAYAS)' => ['distance' => 350, 'price' => 120],

            // Region IX - Zamboanga Peninsula
            'REGION IX (ZAMBOANGA PENINSULA)' => ['distance' => 420, 'price' => 130],

            // Region X - Northern Mindanao
            'REGION X (NORTHERN MINDANAO)' => ['distance' => 320, 'price' => 120],

            // Region XI - Davao Region
            'REGION XI (DAVAO REGION)' => ['distance' => 480, 'price' => 140],

            // Region XII - SOCCSKSARGEN
            'REGION XII (SOCCSKSARGEN)' => ['distance' => 380, 'price' => 130],

            // Region XIII - Caraga
            'REGION XIII (CARAGA)' => ['distance' => 520, 'price' => 140],

            // Cordillera Administrative Region (CAR)
            'CORDILLERA ADMINISTRATIVE REGION (CAR)' => ['distance' => 780, 'price' => 150],

            // Bangsamoro Autonomous Region in Muslim Mindanao (BARMM)
            'BANGSAMORO AUTONOMOUS REGION IN MUSLIM MINDANAO (BARMM)' => ['distance' => 550, 'price' => 150],
        ];

        return $shippingRates[$regionName] ?? ['distance' => 0, 'price' => 80];
    }


    protected function getShippingByCity($cityCode)
    {
        // Distances from Cadiz City, Negros Occidental to various cities in Western Visayas
        $cityDistances = [
            // Cities in Negros Occidental (distances from Cadiz City)
            'BACOLOD CITY (Capital)' => ['distance' => 40, 'price' => 60],
            'BAGO CITY' => ['distance' => 55, 'price' => 70],
            'BINALBAGAN' => ['distance' => 90, 'price' => 85],
            'CADIZ CITY' => ['distance' => 0, 'price' => 0],    // Same city!
            'CALATRAVA' => ['distance' => 45, 'price' => 65],
            'CANDONI' => ['distance' => 110, 'price' => 95],
            'CAUAYAN' => ['distance' => 120, 'price' => 100],
            'ENRIQUE B. MAGALONA (SARAVIA)' => ['distance' => 35, 'price' => 55],
            'CITY OF ESCALANTE' => ['distance' => 25, 'price' => 50],
            'CITY OF HIMAMAYLAN' => ['distance' => 130, 'price' => 105],
            'HINIGARAN' => ['distance' => 70, 'price' => 75],
            'HINOBA-AN (ASIA)' => ['distance' => 140, 'price' => 110],
            'ILOG' => ['distance' => 95, 'price' => 85],
            'ISABELA' => ['distance' => 80, 'price' => 80],
            'CITY OF KABANKALAN' => ['distance' => 85, 'price' => 80],
            'LA CARLOTA CITY' => ['distance' => 65, 'price' => 70],
            'LA CASTELLANA' => ['distance' => 100, 'price' => 90],
            'MANAPLA' => ['distance' => 20, 'price' => 45],
            'MOISES PADILLA (MAGALLON)' => ['distance' => 75, 'price' => 75],
            'MURCIA' => ['distance' => 50, 'price' => 65],
            'PONTEVEDRA' => ['distance' => 60, 'price' => 70],
            'PULUPANDAN' => ['distance' => 55, 'price' => 70],
            'SAGAY CITY' => ['distance' => 15, 'price' => 40],
            'SAN CARLOS CITY' => ['distance' => 70, 'price' => 75],
            'SAN ENRIQUE' => ['distance' => 85, 'price' => 80],
            'SILAY CITY' => ['distance' => 35, 'price' => 55],
            'CITY OF SIPALAY' => ['distance' => 150, 'price' => 115],
            'CITY OF TALISAY' => ['distance' => 45, 'price' => 65],
            'TOBOSO' => ['distance' => 30, 'price' => 55],
            'VALLADOLID' => ['distance' => 65, 'price' => 70],
            'CITY OF VICTORIAS' => ['distance' => 30, 'price' => 55],
            'SALVADOR BENEDICTO' => ['distance' => 60, 'price' => 70],
        ];

        // Get the city name from the city code
        $city = PhilippineCity::where('code', $cityCode)->first();

        if ($city && isset($cityDistances[$city->name])) {
            return $cityDistances[$city->name];
        }

        // Fallback: if city not found in our list, use default Region VI pricing
        return ['distance' => 80, 'price' => 80];
    }


     /**
     * Reduce product stock after successful order
     */
    protected function reduceProductStock(Product $product, int $quantity): void
    {
        $product->decrement('prod_security_stock', $quantity);
        
    }
    
}
