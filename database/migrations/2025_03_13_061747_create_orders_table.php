<?php

use App\Models\User;
use App\Models\Courier;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class, 'user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignIdFor(Courier::class, 'courier_id')->nullable()->constrained('couriers')->nullOnDelete(); // Added
            $table->string('order_number')->unique()->index();
            $table->decimal('order_total_price', 12, 2)->nullable();
            $table->enum('order_status', ['new', 'processing', 'shipped', 'delivered', 'cancelled'])->default('new');
            $table->decimal('shipping_price', 10, 2)->nullable();
            $table->decimal('distance_in_km', 8, 2)->nullable();
            $table->enum('payment_method', ['cod', 'bank_transfer', 'credit_card'])->default('cod');
            $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending');
            $table->string('payment_reference')->nullable();
            $table->text('order_notes')->nullable();
            $table->string('payment_intent_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
