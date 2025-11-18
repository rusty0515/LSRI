<div>
    <!-- Card Blog -->
    <div class="max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">
        <!-- Title -->
        <div class="max-w-2xl mx-auto mb-10 text-center lg:mb-14">
            <h2 class="text-2xl font-bold md:text-4xl md:leading-tight dark:text-white">{{ __('Shop') }}</h2>
            <p class="mt-1 text-gray-600 dark:text-neutral-400">
                {{ __('Lorem ipsum dolor sit amet consectetur,
                                adipisicing elit. Omnis, iste!') }}</p>
        </div>
        <!-- End Title -->

        <!-- Sort Menu -->
        <div class="m-1 hs-dropdown [--trigger:hover] relative inline-flex mb-4 md:mb-6">
            <button id="hs-dropdown-hover-event" type="button"
                class="inline-flex items-center px-4 py-3 text-sm font-medium text-gray-800 border border-gray-200 rounded-lg shadow-sm bg-neutral-200 hs-dropdown-toggle gap-x-2 hover:bg-gray-50 focus:outline-none focus:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-white dark:hover:bg-neutral-700 dark:focus:bg-neutral-700"
                aria-haspopup="menu" aria-expanded="false" aria-label="Dropdown">

                <svg class="size-4" width="24" height="24" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 7.5 7.5 3m0 0L12 7.5M7.5 3v13.5m13.5 0L16.5 21m0 0L12 16.5m4.5 4.5V7.5" />
                </svg>
                {{ $this->getCurrentSortLabel() }}
                <svg class="hs-dropdown-open:rotate-180 size-4" xmlns="http://www.w3.org/2000/svg" width="24"
                    height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="m6 9 6 6 6-6" />
                </svg>
            </button>

            <div class="hs-dropdown-menu transition-[opacity,margin] duration hs-dropdown-open:opacity-100 opacity-0 hidden min-w-60 bg-white shadow-md rounded-lg mt-2 dark:bg-neutral-800 dark:border dark:border-neutral-700 dark:divide-neutral-700 after:h-4 after:absolute after:-bottom-4 after:start-0 after:w-full before:h-4 before:absolute before:-top-4 before:start-0 before:w-full"
                role="menu" aria-orientation="vertical" aria-labelledby="hs-dropdown-hover-event">
                <div class="p-1 space-y-0.5">
                    <button wire:click="setSortBy('latest')"
                        class="w-full flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm text-gray-800 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-neutral-300 dark:focus:bg-neutral-700 {{ $sortBy === 'latest' ? 'bg-gray-100 dark:bg-neutral-700' : '' }}">
                        Latest
                    </button>
                    <button wire:click="setSortBy('oldest')"
                        class="w-full flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm text-gray-800 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-neutral-300 dark:focus:bg-neutral-700 {{ $sortBy === 'oldest' ? 'bg-gray-100 dark:bg-neutral-700' : '' }}">
                        Oldest
                    </button>
                    <button wire:click="setSortBy('popular')"
                        class="w-full flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm text-gray-800 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-neutral-300 dark:focus:bg-neutral-700 {{ $sortBy === 'popular' ? 'bg-gray-100 dark:bg-neutral-700' : '' }}">
                        Popular
                    </button>
                    <button wire:click="setSortBy('cheaper')"
                        class="w-full flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm text-gray-800 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-neutral-300 dark:focus:bg-neutral-700 {{ $sortBy === 'cheaper' ? 'bg-gray-100 dark:bg-neutral-700' : '' }}">
                        Cheaper
                    </button>
                </div>
            </div>
        </div>
        <!-- End Sort menu -->

        <div class="grid gap-6 sm:grid-cols-2 md:grid-cols-5">
            <div class="col-span-5 md:col-span-4">
                <!-- Grid -->
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-8">

                    @forelse ($this->products as $product)
                        <!-- Card -->
                        <div wire:key="card-product-{{ $product->id . '' . $product->prod_sku }}"
                            class="flex flex-col mb-4 border shadow-sm bg-neutral-200 rounded-xl dark:bg-neutral-900 border-neutral-300 dark:border-neutral-700 dark:shadow-neutral-700/70 col-span-4 md:col-span-2">

                            <a class="px-5 py-5" href="{{ route('page.shop.single', $product->prod_slug) }}">
                                <div class="p-4 md:p-0 mb-4">
                                    <small
                                        class="inline-flex items-center gap-x-1.5 py-1 px-3 rounded-full text-xs  bg-red-100 text-red-800 dark:bg-red-800/30 dark:text-red-500">
                                        {{ $product->prod_sku }}
                                    </small>

                                    <h5 class="text-sm font-bold text-gray-800 dark:text-white py-1.5">
                                        {{ $product->prod_name }}

                                        @if ($product->discount_badge_text)
                                            {{-- Show discount badge --}}
                                            <span
                                                class="inline-block bg-red-600 text-white text-xs font-bold px-2 py-1 rounded-full ms-2">
                                                {{ $product->discount_badge_text }}
                                            </span>
                                        @endif
                                    </h5>

                                    <div class="flex flex-row justify-between align-middle items-center">
                                        <p class="text-sm text-gray-500 dark:text-neutral-500">
                                            4k+ sold
                                            {{-- {{ $product->brand->brand_name }} --}}
                                        </p>

                                        <div>

                                        </div>
                                    </div>

                                    <!-- GROUPS -->
                                    <div class="flex flex-row items-center justify-between mt-3 align-middle">

                                        @if ($product->has_discount)
                                            {{-- Show prices --}}
                                            <div>
                                                <span
                                                    class="text-neutral-400 line-through text-sm mr-2">₱{{ number_format($product->prod_price, 2) }}</span>
                                                <span
                                                    class="font-bold text-gray-500 dark:text-white">₱{{ number_format($product->discounted_price, 2) }}</span>
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

                                </div>
                                <div class="aspect-4/4 overflow-hidden rounded-2xl">
                                    <img class="size-full object-cover rounded-2xl"
                                        src="{{ asset(Storage::url($product->prod_ft_image)) }}"
                                        alt="{{ $product->prod_slug }}">
                                </div>
                            </a>

                        </div>
                        <!-- End Card -->

                    @empty
                        <div class="container w-full mx-auto text-center col-span-full">

                            <svg class="flex items-center justify-center flex-shrink-0 w-auto mx-auto text-red-500 align-middle h-14"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                            </svg>

                            <h1 class="mt-4 text-2xl text-gray-800 dark:text-white">{{ __('No Products Created') }}
                            </h1>

                        </div>
                    @endforelse

                </div>
                <!-- End Grid -->
            </div>

            <div class="col-span-5 md:col-span-1">

                <div class="flex flex-col flex-[1_0_0%]">
                    {{-- CATEGORIES --}}
<div class="p-4 flex-1 md:p-5 border-b border-gray-200 dark:border-neutral-700">
    <h3 class="text-sm font-bold text-gray-800 dark:text-white">
        {{ __('Category') }}
    </h3>
    <div class="flex flex-col gap-y-4 mt-5 md:mt-7 max-h-60 overflow-y-auto">
        @forelse($this->categories as $category)
            <div class="flex items-center">
                <input 
                    type="checkbox" 
                    wire:model.live="selectedCategories"
                    value="{{ $category->id }}"
                    id="category-{{ $category->id }}"
                    class="shrink-0 mt-0.5 p-2 border-gray-200 rounded-sm text-red-600 focus:ring-red-500 checked:border-red-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-red-500 dark:checked:border-red-500 dark:focus:ring-offset-gray-800"
                >
                <label for="category-{{ $category->id }}" class="text-sm text-gray-500 ms-3 dark:text-neutral-400 flex justify-between w-full">
                    <span>{{ $category->prod_cat_name }}</span>
                    <span class="text-xs bg-gray-200 dark:bg-neutral-700 px-2 py-1 rounded-full">{{ $category->products_count }}</span>
                </label>
            </div>
        @empty
            <div class="text-sm text-gray-500 dark:text-neutral-400 text-center py-2">
                No categories found
            </div>
        @endforelse
    </div>
</div>
{{-- END CATEGORIES --}}
                    {{-- BRANDS --}}
<div class="p-4 flex-1 md:p-5 border-b border-gray-200 dark:border-neutral-700">
    <h3 class="text-sm font-bold text-gray-800 dark:text-white">
        {{ __('Brands') }}
    </h3>

    <div class="max-w-sm mt-4">
        <!-- SearchBox -->
        <div class="relative">
            <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none z-20 ps-3.5">
                <svg class="shrink-0 size-4 text-gray-400 dark:text-white/60" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.3-4.3"></path>
                </svg>
            </div>
            <input 
                wire:model.live.debounce.300ms="brandSearch"
                class="py-2.5 py-3 ps-10 pe-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:border-red-500 focus:ring-red-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600" 
                type="text" 
                placeholder="Search brands..."
            >
        </div>
    </div>

    <div class="flex flex-col gap-y-4 mt-5 md:mt-7 max-h-60 overflow-y-auto">
        @forelse($this->brands as $brand)
            <div class="flex items-center">
                <input 
                    type="checkbox" 
                    wire:model.live="selectedBrands"
                    value="{{ $brand->id }}"
                    id="brand-{{ $brand->id }}"
                    class="shrink-0 mt-0.5 p-2 border-gray-200 rounded-sm text-red-600 focus:ring-red-500 checked:border-red-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:checked:bg-red-500 dark:checked:border-red-500 dark:focus:ring-offset-gray-800"
                >
                <label for="brand-{{ $brand->id }}" class="text-sm text-gray-500 ms-3 dark:text-neutral-400 flex justify-between w-full">
                    <span>{{ $brand->brand_name }}</span>
                    <span class="text-xs bg-gray-200 dark:bg-neutral-700 px-2 py-1 rounded-full">{{ $brand->products_count }}</span>
                </label>
            </div>
        @empty
            <div class="text-sm text-gray-500 dark:text-neutral-400 text-center py-2">
                No brands found
            </div>
        @endforelse
    </div>
</div>
{{-- END BRANDS --}}

                    {{-- PRICE RANGE --}}
                    {{-- PRICE RANGE --}}
<div class="p-4 flex-1 md:p-5 border-b border-gray-200 dark:border-neutral-700">
    <h3 class="text-sm font-bold text-gray-800 dark:text-white mb-4">
        {{ __('Price Range') }}
    </h3>
    <div class="space-y-4">
        <div class="flex justify-between items-center">
            <span class="text-sm text-gray-500 dark:text-neutral-400">Max: ₱{{ number_format($priceRange, 2) }}</span>
        </div>
        <input 
            type="range" 
            wire:model.live="priceRange"
            min="0" 
            max="10000" 
            step="100"
            class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-neutral-700
                   [&::-webkit-slider-thumb]:appearance-none
                   [&::-webkit-slider-thumb]:h-4
                   [&::-webkit-slider-thumb]:w-4
                   [&::-webkit-slider-thumb]:rounded-full
                   [&::-webkit-slider-thumb]:bg-red-600
                   dark:[&::-webkit-slider-thumb]:bg-red-500"
        >
        <!--  -->
    </div>
</div>
{{-- END PRICE RANGE --}}
                    {{-- END PRICE RANGE --}}


                </div>

            </div>

        </div>


    </div>
    <!-- End Card Blog -->

</div>
