<?php

namespace App\Livewire\Pages;

use App\Models\Product;
use App\Models\Rating;
use App\Models\Review;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class ShopPageSingle extends Component
{
    use WithFileUploads, WithPagination;

    public Product $product;
    public $related_products;

    
    public $ratingStar;
    public $reviewText;
    public $ratingImages = [];

    public function mount($prod_slug)
    {
        $this->product = Product::withSingleProductRelations()
            ->bySlug($prod_slug)
            ->visible()
            ->firstOrFail();

        $this->getRelatedProducts();
    }

    private function getRelatedProducts()
    {
        $categoryIds = $this->product->productCategories->pluck('id')->toArray();

        if (empty($categoryIds)) {
            // If no categories, get random visible products
            $this->related_products = Product::withBasicRelations()
                ->visible()
                ->where('id', '!=', $this->product->id)
                ->inRandomOrder()
                ->limit(6)
                ->get();
        } else {
            $this->related_products = Product::withBasicRelations()
                ->relatedByCategories($categoryIds, $this->product->id)
                ->inRandomOrder()
                ->limit(6)
                ->get();
        }
    }

    public function submitReview()
{
    $this->validate([
        'ratingStar' => 'required|integer|min:1|max:5',
        'reviewText' => 'required|string|min:10|max:1000',
        'ratingImages.*' => 'nullable|image|max:5120',
    ]);

    if (!Auth::check()) {
        session()->flash('error', 'Please login to submit a review.');
        return;
    }

    // Use updateOrCreate to handle both new and existing ratings
    $review = Rating::updateOrCreate(
        [
            'user_id' => Auth::id(),
            'product_id' => $this->product->id,
        ],
        [
            'rating' => $this->ratingStar,
            'review' => $this->reviewText,
        ]
    );

    // Handle image uploads
    if ($this->ratingImages) {
        // Delete existing images when updating (optional)
        if ($review->wasRecentlyCreated === false && method_exists($review, 'ratingImages')) {
            $review->ratingImages()->delete();
        }
        
        foreach ($this->ratingImages as $image) {
            $path = $image->store('review-images', 'public');
            $review->ratingImages()->create(['url' => $path]);
        }
    }

    $this->reset(['ratingStar', 'reviewText', 'ratingImages']);
    
    session()->flash('success', $review->wasRecentlyCreated 
        ? 'Thank you for your review!' 
        : 'Your review has been updated!');
}

    #[Layout('layouts.app')]
    #[Title('Shop')]
    public function render()
    {
        // Get reviews with pagination in the render method
        $reviews = Rating::with(['user', 'ratingImages'])
            ->where('product_id', $this->product->id)
            ->latest()
            ->paginate(5);

        return view('livewire.pages.shop-page-single', [
            'product' => $this->product,
            'related_products' => $this->related_products,
            'reviews' => $reviews,
        ]);
    }
}