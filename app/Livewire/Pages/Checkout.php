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
use App\Livewire\Traits\HasRemoveItem;
use App\Livewire\Traits\HasAlertNotification;

class Checkout extends Component
{
    use HasAlertNotification, HasRemoveItem;

    public array $cart = [];
    public float $total = 0;
    public float $sub_total = 0;
    public float $tax = 0;

    public $customer_amount = 100;
    public $customer_payment_method = '';

    // Card-related properties
    public $card_name = '';
    public $card_number = '';
    public $expiration_month = '';
    public $expiration_year = '';
    public $cvv = '';

    public $paymentIntent = null;
    public $paymentIntent_id;
    public $paymentMethod;
    public $createPaymentIntent;

    #[On('checkout-updated')]
    public function mount()
    {
        $this->cart = session()->get('cart', []);
        $this->calculateSubTotal();
        $this->calculateTax();
        $this->calculateTotal();
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
        $this->calculateSubTotal();
    }

    public function removeItem($id)
    {
        return $this->removeCartItem($id);
    }

    public function checkout()
    {
        // Validate required fields
        if (empty($this->cart)) {
            $this->notify('Your cart is empty', 'error', 3000);
            return;
        }

        if (empty($this->customer_payment_method)) {
            $this->notify('Please select a payment method', 'error', 3000);
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
            DB::transaction(function () use ($paymentMethod) {
                // Generate unique order number
                $orderNumber = '#ORDER-'. date('His-') . strtoupper(Str::random(6));

                // Create the order
                $order = Order::create([
                    'user_id' => Auth::id(),
                    'order_number' => $orderNumber,
                    'order_total_price' => $this->total,
                    'order_status' => OrderStatusEnum::New->value,
                    'shipping_price' => 0, 
                    'distance_in_km' => 0, 
                    'payment_method' => $paymentMethod->value,
                    'payment_status' => 'pending',
                    'payment_reference' => $this->generatePaymentReference($paymentMethod),
                    'order_notes' => '',
                ]);

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

                        // Update product stock if you have stock management
                        // $product->decrement('stock', $item['quantity']);
                    }
                }

                // Clear the cart after successful order
                session()->forget('cart');
                $this->cart = [];
            });

            // Show success message
            $this->notify('Order placed successfully!', 'success', 5000);

            // Redirect to order confirmation or dashboard
            return redirect()->route('page.customer-dashboard')->with('success', 'Order placed successfully!');
        } catch (\Exception $e) {
            \Log::error('Checkout error: ' . $e->getMessage());
            $this->notify('Failed to place order. Please try again.', 'error', 5000);
            return;
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
        $this->total = $this->sub_total + $this->tax;
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

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.pages.checkout');
    }
}
