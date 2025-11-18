<div>
    <!-- Card Blog -->
    <div class="max-w-[85rem] px-4 py-10 sm:px-6 lg:px-8 lg:py-32 mx-auto">

        <!-- Title -->
        <div class="max-w-2xl mx-auto mb-10 text-center lg:mb-14">
            <h2 class="text-2xl font-bold md:text-4xl md:leading-tight dark:text-white">Read our latest blog</h2>
            <p class="mt-1 text-gray-600 dark:text-neutral-400">Stay updated with our latest articles, tips, and insights.</p>
        </div>
        <!-- End Title -->

        <!-- Grid -->
        <div class="grid gap-6 lg:grid-cols-2">
            @forelse ($posts as $post)
                <!-- Card -->
                <a class="relative block group rounded-xl focus:outline-none" href="{{ route('page.blog.single', $post->slug) }}">
                    <div class="shrink-0 relative rounded-xl overflow-hidden w-full h-[350px] before:absolute before:inset-x-0 before:z-[1] before:size-full before:bg-gradient-to-t before:from-gray-900/70">
                        @if($post->featured_img)
                            <img class="absolute top-0 object-cover size-full start-0" src="{{ asset(Storage::url($post->featured_img)) }}" alt="{{ $post->title }}">
                        @else
                            <img class="absolute top-0 object-cover size-full start-0" src="https://via.placeholder.com/800x600/4F46E5/FFFFFF?text=Blog+Image" alt="Default blog image">
                        @endif
                    </div>

                    <div class="absolute inset-x-0 top-0 z-10">
                        <div class="flex flex-col h-full p-4 sm:p-6">
                            <!-- Avatar -->
                            <div class="flex items-center">
                                <div class="shrink-0">
                                    @if($post->author && $post->author->profile_photo)
                                        <img class="size-[46px] border-2 border-white rounded-full" src="{{ asset(Storage::url($post->author->profile_photo)) }}" alt="{{ $post->author->name }}">
                                    @else
                                        <img class="size-[46px] border-2 border-white rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode($post->author->name ?? 'Author') }}&background=4F46E5&color=fff" alt="Author">
                                    @endif
                                </div>
                                <div class="ms-2.5 sm:ms-4">
                                    <h4 class="font-semibold text-white">
                                        {{ $post->author->name }}
                                    </h4>
                                    <p class="text-xs text-white/80">
                                        {{ $post->created_at->format('M d, Y') }}
                                    </p>
                                </div>
                            </div>
                            <!-- End Avatar -->
                        </div>
                    </div>

                    <div class="absolute inset-x-0 bottom-0 z-10">
                        <div class="flex flex-col h-full p-4 sm:p-6">
                            <h3 class="text-lg font-semibold text-white sm:text-3xl group-hover:text-white/80 group-focus:text-white/80">
                                {{ $post->title }}
                            </h3>
                            <p class="mt-2 text-white/80 line-clamp-2">
                                {{ $post->excerpt ?? \Illuminate\Support\Str::limit(strip_tags($post->content), 120) }}
                            </p>
                            <div class="mt-3 flex flex-wrap gap-2">
                                @if($post->blogCategory)
                                    <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-800/30 dark:text-yellow-500">
                                        {{ $post->blogCategory->cat_name }}
                                    </span>
                                @endif
                                
                                <!-- Reading Time -->
                                @if($post->reading_time)
                                    <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-800/30 dark:text-blue-500">
                                        {{ $post->reading_time }} min read
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </a>
                <!-- End Card -->
            @empty
                <div class="relative flex-col block col-span-2 text-center py-12">
                    <svg class="mx-auto mb-4 text-gray-400 size-16 dark:text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                    </svg>

                    <h2 class="text-xl font-semibold text-gray-500 dark:text-gray-400 mb-2">
                        {{ __('No blog posts yet') }}
                    </h2>
                    <p class="text-gray-400 dark:text-gray-500">
                        {{ __('Check back later for new articles.') }}
                    </p>
                </div>
            @endforelse
        </div>
        <!-- End Grid -->

      
    </div>
    <!-- End Card Blog -->
</div>