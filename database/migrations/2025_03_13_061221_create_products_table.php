<?php

use App\Models\Brand;
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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Brand::class)->nullable()->constrained()->nullOnDelete();
            $table->string('prod_name');
            $table->string('prod_slug')->nullable()->unique()->index();
            $table->string('prod_sku')->nullable()->unique();
            $table->string('prod_barcode')->nullable()->unique();
            $table->string('prod_ft_image')->nullable();
            $table->text('prod_short_desc')->nullable();
            $table->longText('prod_long_desc')->nullable();
            $table->unsignedBigInteger('prod_qty')->default(0);
            $table->unsignedBigInteger('prod_security_stock')->default(0);
            $table->boolean('is_featured')->default(0);
            $table->boolean('is_visible')->default(0);
            $table->decimal('prod_price', 10, 2)->nullable();
            $table->decimal('discounted_price', 10, 2)->nullable();
            $table->decimal('prod_cost', 10, 2)->nullable();
            $table->string('prod_color')->nullable();
            $table->enum('prod_type', ['deliverable', 'downloadable'])->nullable();
            $table->boolean('prod_requires_shipping')->default(false);
            $table->date('prod_published_at')->nullable();
            $table->string('prod_seo_title', 60)->nullable();
            $table->string('prod_seo_description', 160)->nullable();
            $table->json('prod_dimensions')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->index(['prod_name', 'prod_slug', 'prod_sku']);
            $table->index(['is_visible', 'is_featured']); // For featured products query
            $table->index(['is_visible', 'created_at']); // For new products query
            $table->index(['brand_id', 'is_visible']); // For brand filtering
            $table->index('is_visible'); // Most important - used in all queries
            $table->index('is_featured');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
