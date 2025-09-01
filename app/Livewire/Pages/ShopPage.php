<?php

namespace App\Livewire\Pages;

use Carbon\Carbon;
use App\Models\Brand;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use App\Models\ProductCategory;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;

class ShopPage extends Component
{
    // use WithPagination;
    // public function prodCategories()
    // {

    // }

    // #[Layout('layouts.app')]
    // #[Title('Shop')]
    // public function render()
    // {
    //     return view('livewire.pages.shop-page', [
    //         'products' => Product::forShop()->paginate(12),
    //     ]);
    // }


    use WithPagination;

    // Public properties for filters (keep minimal)
    public array $selectedCategories = [];
    public array $selectedBrands = [];
    public string $sortBy = 'latest';
    public int $perPage = 12;
    public string $priceRange = '1000'; // Default max price
    public string $brandSearch = '';

    #[Layout('layouts.app')]
    #[Title('Shop')]
    // Use computed property instead of passing data directly to view
    #[Computed]
    public function products()
    {
        $query = Product::query()
            ->with(['brand:id,brand_name', 'productCategories:id,prod_cat_name'])
            ->select([
                'id', 'prod_name', 'prod_slug', 'prod_sku', 'prod_ft_image',
                'prod_price', 'discounted_price', 'brand_id', 'created_at',
                'is_featured', 'prod_published_at'
            ])
            ->where('is_visible', true)
            ->when($this->selectedCategories, function ($query) {
                $query->whereHas('productCategories', function ($q) {
                    $q->whereIn('product_categories.id', $this->selectedCategories);
                });
            })
            ->when($this->selectedBrands, function ($query) {
                $query->whereIn('brand_id', $this->selectedBrands);
            })
            ->when($this->priceRange, function ($query) {
                $query->where('prod_price', '<=', $this->priceRange);
            });

        // Apply sorting based on sortBy value
        match ($this->sortBy) {
            'latest' => $query->orderBy('created_at', 'desc'),
            'oldest' => $query->orderBy('created_at', 'asc'),
            'popular' => $query->orderBy('is_featured', 'desc')->orderBy('created_at', 'desc'),
            'cheaper' => $query->orderBy('prod_price', 'asc'),
            default => $query->orderBy('created_at', 'desc')
        };

        return $query->paginate($this->perPage);
    }

    // Use computed property for categories with counts (cached)
    #[Computed(persist: true)]
    public function categories()
    {
        return \App\Models\ProductCategory::query()
            ->select('id', 'prod_cat_name')
            ->where('is_visible', true)
            ->withCount(['products' => function ($query) {
                $query->where('is_visible', true);
            }])
            ->having('products_count', '>', 0)
            ->orderBy('prod_cat_name')
            ->get();
    }

    // Use computed property for brands with search and counts (cached when no search)
    #[Computed(persist: false)] // Don't persist because of search functionality
    public function brands()
    {
        return Brand::query()
            ->select('id', 'brand_name')
            ->where('is_visible', true)
            ->when($this->brandSearch, function ($query) {
                $query->where('brand_name', 'like', '%' . $this->brandSearch . '%');
            })
            ->withCount(['products' => function ($query) {
                $query->where('is_visible', true);
            }])
            ->having('products_count', '>', 0)
            ->orderBy('brand_name')
            ->get();
    }

    // Use computed property for statistics (cached with persist)
    #[Computed(persist: true)]
    public function stats()
    {
        return [
            'total_products' => Product::where('is_visible', true)->count(),
            'categories_count' => ProductCategory::where('is_visible', true)->count(),
            'brands_count' => Brand::where('is_visible', true)->count(),
        ];
    }

    public function updatedSelectedCategories()
    {
        $this->resetPage();
    }

    public function updatedSelectedBrands()
    {
        $this->resetPage();
    }

    public function updatedPriceRange()
    {
        $this->resetPage();
    }

    public function updatedBrandSearch()
    {
        // Don't reset page for search, just filter brands
        unset($this->brands); // Clear computed property cache
    }

    public function clearFilters()
    {
        $this->reset(['selectedCategories', 'selectedBrands', 'sortBy', 'priceRange', 'brandSearch']);
        $this->resetPage();
    }

    public function setSortBy($sortType)
    {
        $this->sortBy = $sortType;
        $this->resetPage();
    }

    // Get current sort label for display
    public function getCurrentSortLabel()
    {
        return match ($this->sortBy) {
            'latest' => 'Latest',
            'oldest' => 'Oldest',
            'popular' => 'Popular',
            'cheaper' => 'Cheaper',
            default => 'Latest'
        };
    }

    #[Layout('layouts.app')]
    #[Title('Shop')]
    public function render()
    {
        dd($this->products, $this->stats);
        return view('livewire.pages.shop-page');
    }
}
