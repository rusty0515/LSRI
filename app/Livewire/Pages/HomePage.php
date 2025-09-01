<?php

namespace App\Livewire\Pages;

// use Carbon\Carbon;
use Carbon\Carbon;
use App\Models\Brand;
use App\Models\Product;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;

class HomePage extends Component
{
     #[Computed(persist: true)] // Cache until component is destroyed
    public function getFtProds()
    {
        return Product::select([
                'id',
                'prod_name',
                'prod_slug',
                'prod_price',
                'prod_ft_image',
                'brand_id'
            ])
            ->with([
                'productImages:id,product_id,url,alt_text', // Only select needed columns
                'brand:id,brand_name,brand_slug', // Only select needed brand columns
                'productCategories:id,prod_cat_name,prod_cat_slug' // Only select needed category columns
            ])
            ->where('is_visible', true)
            ->where('is_featured', true)
            ->get();
    }


    #[Computed(persist: true)] // Cache this expensive query
    public function newProducts()
    {
        return Product::select([
                'id',
                'prod_sku',
                'prod_qty',
                'prod_name',
                'prod_slug',
                'brand_id',
                'prod_price',
                'prod_ft_image',
                'created_at'
            ])
            ->with([
                'brand:id,brand_name',
                'discounts' => function ($query) {
                    $query->select('discounts.id', 'discount_name', 'starts_at', 'ends_at');
                }
            ])
            ->where('is_visible', true)
            ->whereDate('created_at', '>=', Carbon::today()->subMonths(1))
            ->orderBy('created_at', 'desc')
            ->get();
    }


    #[Computed(persist: true)] // Cache brands query
    public function getBrands()
    {
        return Brand::select(['id', 'brand_name', 'brand_slug', 'brand_image'])
            ->where('is_visible', true)
            ->limit(7)
            ->get();
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.pages.home-page');
    }
}
