<?php

use App\Models\Product;
use App\Models\Discount;
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
        Schema::create('discount_product', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Discount::class, 'discount_id')->nullable()->constrained('discounts', 'id')->cascadeOnDelete();
            $table->foreignIdFor(Product::class, 'product_id')->nullable()->constrained('products', 'id')->cascadeOnDelete();
            $table->string('discount_code')->nullable()->unique()->index();
            $table->enum('discount_type', ['fixed', 'percentage'])->default('percentage');
            $table->decimal('discount_value', 10, 2)->nullable();
            $table->timestamps();
            $table->index('product_id'); // For discount relationships
            $table->index('discount_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount_product');
    }
};
