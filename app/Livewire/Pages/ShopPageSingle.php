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
    public $newRatingImages = [];
    public $uploadProgress = 0;
    public $maxImages = 5;

    // Add these properties and methods to your ShopPageSingle component

    public function getAverageRatingProperty()
    {
        return Rating::where('product_id', $this->product->id)
            ->average('rating') ?? 0;
    }

    public function getTotalReviewsProperty()
    {
        return Rating::where('product_id', $this->product->id)->count();
    }

    public function getRatingDistributionProperty()
    {
        $distribution = [];
        $total = $this->totalReviews;
        
        if ($total > 0) {
            for ($i = 5; $i >= 1; $i--) {
                $count = Rating::where('product_id', $this->product->id)
                    ->where('rating', $i)
                    ->count();
                $percentage = round(($count / $total) * 100);
                $distribution[$i] = $percentage;
            }
        } else {
            // If no reviews, set all to 0%
            for ($i = 5; $i >= 1; $i--) {
                $distribution[$i] = 0;
            }
        }
        
        return $distribution;
    }

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
        ], [
            'ratingStar.required' => 'Please select a rating',
            'reviewText.required' => 'Please write a review',
            'reviewText.min' => 'Review must be at least 10 characters long',
            'ratingImages.*.image' => 'Each file must be an image (jpeg, png, jpg, gif)',
            'ratingImages.*.max' => 'Each image must not be larger than 5MB',
        ]);

        // Merge any new images first
        $this->mergeNewImages();

        // Check if user has reached maximum image limit
        if (count($this->ratingImages) > $this->maxImages) {
            session()->flash('error', "You can only upload up to {$this->maxImages} images.");
            return;
        }

        if (!Auth::check()) {
            session()->flash('error', 'Please login to submit a review.');
            return;
        }

        try {
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
            if (!empty($this->ratingImages)) {
                // Delete existing images when updating
                if (!$review->wasRecentlyCreated && method_exists($review, 'ratingImages')) {
                    $review->ratingImages()->delete();
                }
                
                foreach ($this->ratingImages as $image) {
                    $path = $image->store('review-images', 'public');
                    $review->ratingImages()->create(['url' => $path]);
                }
            }

            $this->resetForm();
            
            session()->flash('success', $review->wasRecentlyCreated 
                ? 'Thank you for your review!' 
                : 'Your review has been updated!');

            $this->resetPage();

        } catch (\Exception $e) {
            session()->flash('error', 'There was an error submitting your review. Please try again.');
        }
    }

    public function removeImage($index)
    {
        if (isset($this->ratingImages[$index])) {
            unset($this->ratingImages[$index]);
            $this->ratingImages = array_values($this->ratingImages);
        }
    }

    public function updatedNewRatingImages()
    {
        $this->validate([
            'newRatingImages.*' => 'image|max:5120',
        ]);

        $this->mergeNewImages();
    }

    private function mergeNewImages()
    {
        if (empty($this->newRatingImages)) {
            return;
        }

        $totalImages = count($this->ratingImages) + count($this->newRatingImages);
        
        if ($totalImages > $this->maxImages) {
            $allowedNewImages = $this->maxImages - count($this->ratingImages);
            
            if ($allowedNewImages > 0) {
                $this->newRatingImages = array_slice($this->newRatingImages, 0, $allowedNewImages);
                $this->ratingImages = array_merge($this->ratingImages, $this->newRatingImages);
                session()->flash('info', "Added {$allowedNewImages} new images. Maximum {$this->maxImages} images allowed.");
            } else {
                session()->flash('error', "Maximum {$this->maxImages} images reached. Remove some images to add new ones.");
            }
        } else {
            $this->ratingImages = array_merge($this->ratingImages, $this->newRatingImages);
        }

        $this->newRatingImages = [];
    }

    public function clearAllImages()
    {
        $this->reset('ratingImages', 'newRatingImages');
    }

    public function resetForm()
    {
        $this->reset(['ratingStar', 'reviewText', 'ratingImages', 'newRatingImages', 'uploadProgress']);
    }

    public function getCanSubmitReviewProperty()
    {
        return Auth::check() && $this->ratingStar && $this->reviewText;
    }

    public function getRemainingImageSlotsProperty()
    {
        return max(0, $this->maxImages - count($this->ratingImages));
    }

    public function getTotalSelectedImagesProperty()
    {
        return count($this->ratingImages);
    }

    #[Layout('layouts.app')]
    // #[Title('Shop - ')]
    public function render()
    {
        $reviews = Rating::with(['user', 'ratingImages'])
            ->where('product_id', $this->product->id)
            ->latest()
            ->paginate(5);

        return view('livewire.pages.shop-page-single', [
            'product' => $this->product,
            'related_products' => $this->related_products,
            'reviews' => $reviews,
            'averageRating' => $this->averageRating,
            'totalReviews' => $this->totalReviews,
            'ratingDistribution' => $this->ratingDistribution,
        ]);
    }
}