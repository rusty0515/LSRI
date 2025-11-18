<div>
    <!-- Card Blog -->
    <div class="max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-5 mx-auto">
        <div class="mt-5">

            <a class="inline-flex items-center gap-x-1.5 text-sm text-gray-600 decoration-2 hover:underline focus:outline-none focus:underline dark:text-red-500 mb-6"
                href="{{ route('page.shop') }}">
                <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <path d="m15 18-6-6 6-6" />
                </svg>
                {{ __('Back to Shop') }}
            </a>

            <div class="grid lg:grid-cols-3 gap-y-8 lg:gap-y-0 lg:gap-x-6">
                <!-- Content -->
                <div class="lg:col-span-2">

                    <!-- Grid -->
                    <div class="grid gap-10 lg:grid-cols-2 lg:gap-y-16">

                        <div>
                            <!-- Slider -->
                            @if ($product && $product->productImages->isNotEmpty())
                                <div data-hs-carousel='{
                                    "loadingClasses": "opacity-0",
                                    "isAutoPlay": true
                                }'
                                    class="relative">

                                    <div class="flex space-x-2 lg:flex-row hs-carousel">
                                        <!-- Thumbnail Preview -->
                                        <div class="flex-none">
                                            <div
                                                class="flex flex-col overflow-y-auto hs-carousel-pagination max-h-96 gap-y-2
                                            [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300 dark:[&::-webkit-scrollbar-track]:bg-neutral-700 dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500">

                                                @foreach ($product->productImages as $image)
                                                    <div wire:key="product-img-{{ $image->id }}"
                                                        class="hs-carousel-pagination-item shrink-0 border-gray-300 rounded-md overflow-hidden cursor-pointer w-[80px] h-[80px] hs-carousel-active:border-red-500">
                                                        <div
                                                            class="flex justify-center h-full p-2 bg-gray-100 dark:bg-neutral-900">
                                                            <img class="object-container w-auto h-full"
                                                                src="{{ asset(Storage::url($image->url)) }}"
                                                                alt="{{ $product->prod_slug }}">
                                                        </div>
                                                    </div>
                                                @endforeach

                                            </div>
                                        </div>

                                        <!-- Main Image Display -->
                                        <div
                                            class="relative overflow-hidden bg-white rounded-lg grow min-h-96 max-h-40">
                                            <div
                                                class="absolute top-0 bottom-0 flex transition-transform duration-700 opacity-0 hs-carousel-body start-0 flex-nowrap">

                                                @foreach ($product->productImages as $image)
                                                    <div wire:key="product-img-slide-{{ $image->id }}"
                                                        class="hs-carousel-slide">
                                                        <div
                                                            class="flex justify-center h-full p-6 bg-gray-100 dark:bg-neutral-900">
                                                            <img class="object-contain w-full h-full size-full start-0 rounded-xl"
                                                                src="{{ asset(Storage::url($image->url)) }}"
                                                                alt="{{ $product->prod_slug }}">
                                                        </div>
                                                    </div>
                                                @endforeach

                                            </div>

                                            <!-- Previous Button -->
                                            <button type="button"
                                                class="hs-carousel-prev hs-carousel-disabled:opacity-50 hs-carousel-disabled:pointer-events-none absolute inset-y-0 start-0 inline-flex justify-center items-center w-[46px] h-full text-gray-800 hover:bg-gray-800/10 focus:outline-none focus:bg-gray-800/10 rounded-s-lg dark:text-white dark:hover:bg-white/10 dark:focus:bg-white/10">
                                                <span class="text-2xl" aria-hidden="true">
                                                    <svg class="text-red-500 shrink-0 size-5 disabled:text-red-400 "
                                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="m15 18-6-6 6-6"></path>
                                                    </svg>
                                                </span>
                                                <span class="sr-only">{{ __('Previous') }}</span>
                                            </button>

                                            <!-- Next Button -->
                                            <button type="button"
                                                class="hs-carousel-next hs-carousel-disabled:opacity-50 hs-carousel-disabled:pointer-events-none absolute inset-y-0 end-0 inline-flex justify-center items-center w-[46px] h-full text-gray-800 hover:bg-gray-800/10 focus:outline-none focus:bg-gray-800/10 rounded-e-lg dark:text-white dark:hover:bg-white/10 dark:focus:bg-white/10">
                                                <span class="sr-only">{{ __('Next') }}</span>
                                                <span class="text-2xl" aria-hidden="true">
                                                    <svg class="text-red-500 disabled:text-red-400 shrink-0 size-5"
                                                        xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="m9 18 6-6-6-6"></path>
                                                    </svg>
                                                </span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <p class="text-center text-gray-500">{{ __('No images available for this product.') }}
                                </p>
                            @endif
                            <!-- End Slider -->

                        </div>

                        <div>
                            <span
                                class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-teal-100 text-teal-800 dark:bg-teal-800/30 dark:text-teal-500 mb-4">{{ $product->prod_sku }}</span>

                            <div class="flex flex-wrap flex-col md:flex-row items-center align-middle">
                                <h1 class="text-4xl font-bold text-gray-800 dark:text-white">{{ $product->prod_name }}
                                </h1>
                                @if ($product->discount_badge_text)
                                    {{-- Show discount badge --}}
                                    <span
                                        class="inline-block bg-red-600 text-white text-xs font-bold px-2 py-1 rounded-full ms-2">
                                        {{ $product->discount_badge_text }}
                                    </span>
                                @endif
                            </div>

                            <h6 class="mt-2 text-gray-500 text-md dark:text-neutral-500">
                                {{ $product->brand->brand_name }}</h6>



                            <!-- GROUPS -->
                            <div class="flex flex-row items-center justify-between mt-3 align-middle">
                                @if ($product->has_discount)
                                    {{-- Show prices --}}
                                    <div>
                                        <h2 class="text-3xl font-bold text-gray-800 dark:text-white">
                                            <span
                                                class="text-neutral-400 line-through text-sm mr-2">₱{{ number_format($product->prod_price, 2) }}</span>
                                            <span
                                                class="font-bold text-gray-500 dark:text-white">₱{{ number_format($product->discounted_price, 2) }}</span>
                                        </h2>
                                    </div>
                                @else
                                    {{-- No discount --}}
                                    <div>
                                        <span
                                            class="font-bold text-gray-500 dark:text-white">₱{{ number_format($product->prod_price, 2) }}</span>
                                    </div>
                                @endif
                            </div>
                            <!-- EBD GROUP -->


                            <div class="flex mt-4 gap-x-2">
                                @forelse ($product->productCategories as $prodCat)
                                    <a wire:key="product-cat-{{ $prodCat->id }}"
                                        href="{{ route('page.shop.category', $prodCat->prod_cat_slug) }}">
                                        <span
                                            class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-800/30 dark:text-yellow-500">{{ $prodCat->prod_cat_name }}</span>
                                    </a>
                                @empty
                                    <h2 class="text-gray-500 dar:text-white">{{ __('No categories') }}</h2>
                                @endforelse
                            </div>
                            <livewire:shop-partials.add-to-cart :product="$product" :key="$product->id">
                        </div>

                    </div>
                    <!-- End Grid -->


                    <div class="py-8 lg:pe-8">
                        <div class="space-y-5 text-gray-500 lg:space-y-8 dark:text-white">
                            <h2 class="text-lg font-bold lg:text-3xl dark:text-white">{{ __('Description') }}</h2>
                            <div class="mb-5">
                                {!! str($product->prod__desc)->sanitizeHtml() !!}
                            </div>

                            <div>
                                {!! str($product->prod_long_desc)->sanitizeHtml() !!}
                            </div>
                        </div>
                    </div>
                    
                    <!-- Rating and Review Section -->
                    <div id="reviews-section" class="py-8 lg:pe-8 border-t border-gray-200 dark:border-neutral-700">
                        <div class="space-y-5 text-gray-500 lg:space-y-8 dark:text-white">
                            <h2 class="text-lg font-bold lg:text-3xl dark:text-white">{{ __('Customer Reviews') }}</h2>
                            
                            <!-- Add Review Form -->
                            <!-- Add Review Form -->
@auth
    <div class="bg-white dark:bg-neutral-800 rounded-lg shadow-sm p-6 mb-8">
        <h3 class="text-xl font-semibold mb-4 dark:text-white">{{ __('Write a Review') }}</h3>
        
        <!-- Star Rating Input -->
        <div class="mb-4">
            <label class="block text-sm font-medium mb-2 dark:text-white">{{ __('Your Rating') }}</label>
            <div class="flex space-x-1" id="starRating">
                @for($i = 1; $i <= 5; $i++)
                    <button type="button" 
                            wire:click="$set('ratingStar', {{ $i }})"
                            class="text-gray-300 hover:text-yellow-400 focus:outline-none transition-colors {{ $ratingStar >= $i ? 'text-yellow-400' : '' }}">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    </button>
                @endfor
            </div>
            @error('ratingStar') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        
        <!-- Review Text Input -->
        <div class="mb-4">
            <label for="reviewText" class="block text-sm font-medium mb-2 dark:text-white">{{ __('Your Review') }}</label>
            <textarea id="reviewText" 
                      wire:model="reviewText"
                      rows="4" 
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 dark:bg-neutral-700 dark:border-neutral-600 dark:text-white"
                      placeholder="{{ __('Share your experience with this product...') }}"></textarea>
            @error('reviewText') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        
        <!-- Image Upload (Optional) -->
        <div class="mb-4">
            <label class="block text-sm font-medium mb-2 dark:text-white">
                {{ __('Add Photos (Optional)') }}
                <span class="text-xs text-gray-500 dark:text-gray-400">
                    ({{ $this->remainingImageSlots }} of {{ $maxImages }} remaining)
                </span>
            </label>
            
            <!-- Selected Images Preview -->
            <div class="mb-3" id="selectedImagesPreview">
                @if($ratingImages && count($ratingImages) > 0)
                    <div class="flex flex-wrap gap-2 mb-3">
                        @foreach($ratingImages as $index => $image)
                            <div class="relative">
                                <img src="{{ $image->temporaryUrl() }}" 
                                    alt="Preview" 
                                    class="w-20 h-20 object-cover rounded-md border border-gray-300 dark:border-neutral-600">
                                <button type="button" 
                                        wire:click="removeImage({{ $index }})"
                                        wire:loading.attr="disabled"
                                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs hover:bg-red-600 transition-colors">
                                    ×
                                </button>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Clear All Button -->
                    @if(count($ratingImages) > 1)
                        <button type="button"
                                wire:click="clearAllImages"
                                wire:loading.attr="disabled"
                                class="text-xs text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition-colors">
                            {{ __('Remove All Images') }}
                        </button>
                    @endif
                @endif
            </div>
            
            <!-- Upload Area -->
            @if($this->remainingImageSlots > 0)
                <div class="flex items-center justify-center w-full">
                    <label for="newRatingImages" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:bg-neutral-700 hover:bg-gray-100 dark:hover:bg-neutral-600 dark:border-neutral-600 dark:hover:border-gray-500 transition-colors">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                            </svg>
                            <p class="mb-2 text-sm text-gray-500 dark:text-gray-400">
                                <span class="font-semibold">{{ __('Click to upload') }}</span> 
                                {{ __('or drag and drop') }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                PNG, JPG, GIF (MAX. 5MB each)
                            </p>
                        </div>
                        <input id="newRatingImages" 
                            type="file" 
                            class="hidden" 
                            wire:model="newRatingImages" 
                            multiple 
                            accept="image/*"
                            {{ $this->remainingImageSlots === 0 ? 'disabled' : '' }} />
                    </label>
                </div>
            @else
                <div class="text-center p-4 border border-gray-300 dark:border-neutral-600 rounded-lg bg-gray-50 dark:bg-neutral-700">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ __('Maximum number of images reached') }}
                    </p>
                </div>
            @endif

            <!-- Upload Progress -->
            @if($uploadProgress > 0 && $uploadProgress < 100)
                <div class="mt-2">
                    <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-neutral-700">
                        <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
                            style="width: {{ $uploadProgress }}%"></div>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        {{ __('Uploading...') }} {{ $uploadProgress }}%
                    </p>
                </div>
            @endif

            @error('newRatingImages.*') 
                <span class="text-red-500 text-sm block mt-2">{{ $message }}</span> 
            @enderror
            @error('ratingImages.*') 
                <span class="text-red-500 text-sm block mt-2">{{ $message }}</span> 
            @enderror
        </div>

        <!-- Submit Button -->
        <button type="button" 
                wire:click="submitReview"
                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
            {{ __('Submit Review') }}
        </button>
    </div>
@else
    <!-- Login Prompt for Guests -->
    <div class="bg-white dark:bg-neutral-800 rounded-lg shadow-sm p-6 mb-8 text-center">
        <div class="mb-4">
            <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
        </div>
        <h3 class="text-xl font-semibold mb-2 dark:text-white">{{ __('Login to Write a Review') }}</h3>
        <p class="text-gray-600 dark:text-neutral-400 mb-4">
            {{ __('Please login to share your experience with this product.') }}
        </p>
        <a href="{{ route('filament.auth.auth.login') }}" 
           class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
            {{ __('Login Now') }}
        </a>
        <p class="text-sm text-gray-500 dark:text-neutral-500 mt-3">
            {{ __("Don't have an account?") }}
            <a href="{{ route('filament.auth.auth.register') }}" class="text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                {{ __('Sign up') }}
            </a>
        </p>
    </div>
@endauth
                            
                            <!-- Reviews List -->
                            <div class="space-y-6">
                                <h3 class="text-xl font-semibold mb-4 dark:text-white">{{ __('Customer Reviews') }}</h3>
                                
                                @if($reviews && $reviews->count() > 0)
                                    @foreach($reviews as $review)
                                        <div class="bg-white dark:bg-neutral-800 rounded-lg shadow-sm p-6">
                                            <div class="flex items-start justify-between mb-4">
                                                <div>
                                                    <h4 class="font-semibold text-gray-800 dark:text-white">{{ $review->user->name ?? 'Anonymous' }}</h4>
                                                    <div class="flex items-center mt-1">
                                                        <div class="flex">
                                                            @for($i = 1; $i <= 5; $i++)
                                                                <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }} dark:{{ $i <= $review->rating ? 'text-yellow-600' : 'text-neutral-600' }}" 
                                                                     fill="currentColor" viewBox="0 0 20 20">
                                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                                </svg>
                                                            @endfor
                                                        </div>
                                                        <span class="ml-2 text-sm text-gray-500 dark:text-neutral-400">{{ $review->created_at->format('M d, Y') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <p class="text-gray-700 dark:text-neutral-300 mb-4">{{ $review->review }}</p>
                                            
                                            @if($review->ratingImages && $review->ratingImages->count() > 0)
                                                <div class="flex flex-wrap gap-2 mb-4">
                                                    @foreach($review->ratingImages as $image)
                                                        <img src="{{ asset(Storage::url($image->url)) }}" 
                                                            alt="Review image" 
                                                            class="w-20 h-20 object-cover rounded-md cursor-pointer border border-gray-200 dark:border-neutral-600"
                                                            onclick="openImageModal('{{ asset(Storage::url($image->url)) }}')">
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                    
                                    <!-- Pagination -->
                                    <div class="mt-6">
                                        {{ $reviews->links() }}
                                    </div>
                                @else
                                    <div class="text-center py-8">
                                        <p class="text-gray-500 dark:text-neutral-400">{{ __('No reviews yet. Be the first to review this product!') }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Content -->

                <!-- Sidebar -->
                <div
                    class="lg:col-span-1 lg:w-full lg:h-full lg:bg-gradient-to-r lg:from-gray-50 lg:via-transparent lg:to-transparent dark:from-neutral-800">
                    <div class="sticky top-0 py-8 start-0 lg:ps-8">
                        <h1 class="mb-5 text-lg font-bold lg:mb-8 lg:text-3xl dark:text-white">
                            {{ __('Related Products') }}</h1>
                        <div class="space-y-6">

                            <div class="grid gap-2 lg:grid-cols-2">
                                @forelse ($related_products as $related_product)
                                    <div wire:key="related-product-{{ $related_product->id }}">
                                        <!-- Media -->
                                        <a class="flex flex-col items-center group gap-x-6 focus:outline-none"
                                            href="{{ route('page.shop.single', ['prod_slug' => $related_product->prod_slug]) }}">
                                            <div class="relative">
                                                <img class="top-0 object-contain rounded-lg w-full h-[200px]"
                                                    src="{{ asset(Storage::url($related_product->productImages[0]->url)) }}"
                                                    alt="{{ $related_product->prod_slug }}">
                                            </div>

                                            <div class="p-2 text-start">
                                                <h3 class="mt-2 text-sm text-gray-800 dark:text-white">
                                                    {{ $related_product->prod_name }}</h3>

                                                @if ($related_product->has_discount)
                                                    {{-- Show discounted price --}}
                                                    <div class="mt-3">
                                                        <div
                                                            class="flex flex-row items-center align-middle justify-start gap-2">
                                                            <span
                                                                class="text-neutral-400 line-through text-sm mr-1">₱{{ number_format($related_product->prod_price, 2) }}</span>
                                                            @if ($related_product->discount_badge_text)
                                                                {{-- Discount badge on image --}}
                                                                <span
                                                                    class=" bg-red-600 text-white text-xs font-bold px-2 py-1 rounded-full">
                                                                    {{ $related_product->discount_badge_text }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <h4 class="text-lg font-bold text-gray-800 dark:text-white">
                                                            ₱{{ number_format($related_product->discounted_price, 2) }}
                                                        </h4>
                                                    </div>
                                                @else
                                                    {{-- Regular price --}}
                                                    <h4 class="mt-3 text-lg font-bold text-gray-800 dark:text-white">
                                                        ₱{{ number_format($related_product->prod_price, 2) }}</h4>
                                                @endif
                                            </div>
                                        </a>
                                        <!-- End Media -->
                                    </div>
                                @empty
                                    <h1 class="text-lg font-bold text-gray-800 dark:text-white">
                                        {{ __('No related products') }}</h1>
                                @endforelse

                            </div>

                        </div>

                        <hr class="my-5 border-gray-500 lg:my-8">

                        <div class="lg:mt-8">
    <h1 class="mb-5 text-lg font-bold lg:mb-8 lg:text-3xl dark:text-white">
        {{ __('Product Reviews') }}</h1>
    <!-- Header -->
    <div class="flex items-center justify-between mb-3 gap-x-3">
        <div class="flex items-center gap-x-2">
            <h4 class="font-semibold text-gray-800 dark:text-white">
                {{ number_format($averageRating, 1) }}
            </h4>

            <!-- Rating -->
            <div class="flex">
                @for($i = 1; $i <= 5; $i++)
                    <svg class="shrink-0 size-4 {{ $i <= $averageRating ? 'text-yellow-400 dark:text-yellow-600' : 'text-gray-300 dark:text-neutral-600' }}"
                        xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                        fill="currentColor" viewBox="0 0 16 16">
                        <path
                            d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z">
                        </path>
                    </svg>
                @endfor
            </div>
            <!-- End Rating -->
        </div>

        <!-- <a class="inline-flex items-center text-xs font-medium text-blue-600 gap-x-1 decoration-2 hover:underline"
            href="#reviews-section">
            See all ({{ $totalReviews }})
        </a> -->
    </div>
    <!-- End Header -->

    <div class="mb-3">
        @foreach($ratingDistribution as $star => $percentage)
            <!-- Progress -->
            <div class="flex items-center gap-x-3 whitespace-nowrap">
                <div class="w-10 text-end">
                    <span class="text-sm text-gray-800 dark:text-white">{{ $star }} star</span>
                </div>
                <div class="flex w-full h-2 overflow-hidden bg-gray-200 rounded-full dark:bg-neutral-700"
                    role="progressbar" aria-valuenow="{{ $percentage }}" 
                    aria-valuemin="0" aria-valuemax="100">
                    <div class="flex flex-col justify-center overflow-hidden text-xs text-center text-white transition duration-500 bg-yellow-400 rounded-full whitespace-nowrap dark:bg-yellow-600"
                        style="width: {{ $percentage }}%"></div>
                </div>
                <div class="w-10 text-end">
                    <span class="text-sm text-gray-800 dark:text-white">{{ $percentage }}%</span>
                </div>
            </div>
            <!-- End Progress -->
        @endforeach
    </div>
</div>
                    </div>
                </div>
                <!-- End Sidebar -->
            </div>

            <a class="inline-flex items-center gap-x-1.5 text-sm text-gray-600 decoration-2 hover:underline focus:outline-none focus:underline dark:text-red-500"
                href="{{ route('page.shop') }}">
                <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="m15 18-6-6 6-6" />
                </svg>
                {{ __('Back to Shop') }}
            </a>

        </div>
    </div>
    <!-- End Card Blog -->

    <!-- Image Modal -->
    <div id="imageModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-75">
        <div class="relative max-w-4xl max-h-full">
            <button type="button" 
                    class="absolute top-4 right-4 text-white hover:text-gray-300 focus:outline-none"
                    onclick="closeImageModal()">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            <img id="modalImage" src="" alt="Review image" class="max-w-full max-h-full object-contain">
        </div>
    </div>

</div>

<script>
    function openImageModal(imageSrc) {
        document.getElementById('modalImage').src = imageSrc;
        document.getElementById('imageModal').classList.remove('hidden');
        document.getElementById('imageModal').classList.add('flex');
    }

    function closeImageModal() {
        document.getElementById('imageModal').classList.add('hidden');
        document.getElementById('imageModal').classList.remove('flex');
    }

    // Close modal when clicking outside the image
    document.getElementById('imageModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeImageModal();
        }
    });
</script>