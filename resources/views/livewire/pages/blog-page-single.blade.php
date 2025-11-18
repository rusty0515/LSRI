{{-- @dd($post) --}}
<div>
   <!-- Blog Article -->
    <div class="max-w-3xl px-4 pt-6 lg:pt-10 pb-12 sm:px-6 lg:px-8 mx-auto">
        <div class="max-w-2xl">
            <!-- Avatar Media -->
            <div class="flex justify-between items-center mb-6">
                <div class="flex w-full sm:items-center gap-x-5 sm:gap-x-3">
                    <div class="shrink-0">
                        @if($post->author && $post->author->profile_photo)
                            <img class="size-[46px] border-2 border-white rounded-full" src="{{ asset(Storage::url($post->author->profile_photo)) }}" alt="{{ $post->author->name }}">
                        @else
                            <img class="size-[46px] border-2 border-white rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode($post->author->name ?? 'Author') }}&background=4F46E5&color=fff" alt="Author">
                        @endif
                    </div>

                    <div class="grow">
                        <div class="flex justify-between items-center gap-x-2">
                            <div>
                                <!-- Tooltip -->
                                <div class="hs-tooltip inline-block">
                                    <div class=" sm:mb-1 block text-start cursor-pointer">
                                        <span class="font-semibold text-gray-800 dark:text-neutral-200">
                                            {{ $post->user->name }}
                                        </span>
                                    </div>
                                </div>
                                <!-- End Tooltip -->

                                <ul class="text-xs text-gray-500 dark:text-neutral-500">
                                    <li class="inline-block relative pe-6 last:pe-0 last-of-type:before:hidden before:absolute before:top-1/2 before:end-2 before:-translate-y-1/2 before:size-1 before:bg-gray-300 before:rounded-full dark:text-neutral-400 dark:before:bg-neutral-600">
                                        {{ $post->created_at->diffForHumans() }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Avatar Media -->

            <!-- Content -->
            <div class="space-y-5 md:space-y-8">
                <div class="space-y-3 text-gray-800 dark:text-neutral-200">
                    <h2 class="text-2xl font-bold md:text-3xl">{{ $post->title }}</h2>

                    <div>
                        {!! $post->content !!}
                    </div>
                </div>

                <div>
                    @if (!empty($post->blogCategory))
                     <a class="m-1 inline-flex items-center gap-1.5 py-2 px-3 rounded-full text-sm bg-gray-100 text-gray-800 hover:bg-gray-200 focus:outline-hidden focus:bg-gray-200 dark:bg-neutral-800 dark:text-neutral-200 dark:hover:bg-neutral-700 dark:focus:bg-neutral-700" href="#!">
                        {{ $post->blogCategory->cat_name }}
                    </a>
                    @else
                    <span class="m-1 inline-flex items-center gap-1.5 py-2 px-3 rounded-full text-sm bg-gray-100 text-gray-800 dark:bg-neutral-800 dark:text-neutral-200">
                       {{ __('No Category') }}
                    </span>
                    @endif
                </div>
            </div>
            <!-- End Content -->

            <!-- Enhanced Comments Section -->
            <div class="mt-12 border-t border-gray-200 dark:border-neutral-700 pt-8">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-800 dark:text-neutral-200">
                        Comments ({{ $comments->count() }})
                    </h3>
                    
                   
                </div>

                <!-- Enhanced Comment Form -->
                @auth
                <div class="mb-8 p-6 bg-white dark:bg-neutral-800 rounded-xl border border-gray-200 dark:border-neutral-700 shadow-sm hover:shadow-md transition-shadow duration-300">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="shrink-0">
                            @if(auth()->user()->profile_photo)
                                <img class="size-10 rounded-full border-2 border-gray-200 dark:border-neutral-600" src="{{ asset(Storage::url(auth()->user()->profile_photo)) }}" alt="{{ auth()->user()->name }}">
                            @else
                                <img class="size-10 rounded-full border-2 border-gray-200 dark:border-neutral-600" src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=4F46E5&color=fff" alt="{{ auth()->user()->name }}">
                            @endif
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800 dark:text-neutral-200">Add your comment</h4>
                            <p class="text-sm text-gray-500 dark:text-neutral-400">Share your thoughts with the community</p>
                        </div>
                    </div>
                    
                    <!-- Success/Error Messages -->
                    @if(session('message'))
                        <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded-lg flex items-center gap-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            {{ session('message') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded-lg flex items-center gap-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            {{ session('error') }}
                        </div>
                    @endif

                    <form wire:submit.prevent="addComment" class="space-y-4">
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                Title <span class="text-gray-400 text-sm">(Optional)</span>
                            </label>
                            <input 
                                type="text" 
                                id="title"
                                wire:model="newComment.title"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-hidden focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-200 dark:bg-neutral-700 dark:border-neutral-600 dark:text-white"
                                placeholder="Give your comment a title..."
                                maxlength="100"
                            >
                            @error('newComment.title')
                                <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div> 

                        <div>
                            <label for="content" class="block text-sm font-medium text-gray-700 dark:text-neutral-300 mb-2">
                                Your Comment <span class="text-red-500">*</span>
                            </label>
                            <textarea 
                                id="content"
                                wire:model="newComment.content"
                                rows="4"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-hidden focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-200 resize-none dark:bg-neutral-700 dark:border-neutral-600 dark:text-white"
                                placeholder="What are your thoughts on this post?..."
                                required
                                maxlength="1000"
                            ></textarea>
                            <div class="flex justify-between items-center mt-1">
                                @error('newComment.content')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                                <span class="text-xs text-gray-500 dark:text-neutral-400">
                                    <span wire:model="newComment.content">{{ strlen($newComment['content'] ?? '') }}</span>/1000 characters
                                </span>
                            </div>
                        </div>

                        <div class="flex justify-between items-center pt-2">
                            <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-neutral-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Be respectful and constructive
                            </div>
                            <button 
                                type="submit"
                                class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:outline-hidden focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all duration-200 font-medium shadow-sm hover:shadow-md disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                                wire:loading.attr="disabled"
                                :disabled="!trim($newComment['content'] ?? '')"
                            >
                                <svg wire:loading class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span wire:loading.remove>Post Comment</span>
                                <span wire:loading>Posting...</span>
                            </button>
                        </div>
                    </form>
                </div>
                @else
                <div class="mb-8 p-6 bg-gray-50 dark:bg-neutral-800 rounded-xl border border-gray-200 dark:border-neutral-700 text-center hover:shadow-md transition-shadow duration-300">
                    <div class="w-16 h-16 mx-auto mb-4 text-gray-400 dark:text-neutral-600">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                        </svg>
                    </div>
                    <p class="text-gray-600 dark:text-neutral-400 mb-3 font-medium">
                        Join the conversation
                    </p>
                    <p class="text-gray-500 dark:text-neutral-500 text-sm mb-4 max-w-md mx-auto">
                        Login to share your thoughts and connect with other readers
                    </p>
                    <a href="{{ route('filament.auth.auth.login') }}" class="inline-flex items-center px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:outline-hidden focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors duration-200 font-medium shadow-sm hover:shadow-md">
                        {{__('Login Now')}}  
                    </a>

                    <p class="text-sm text-gray-500 dark:text-neutral-500 mt-3">
                        {{ __("Don't have an account?") }}
                        <a href="{{ route('filament.auth.auth.register') }}" class="text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                            {{ __('Sign up') }}
                        </a>
                    </p>
                </div>
                @endauth

                <!-- Enhanced Comments List -->
                <div class="space-y-4">
                    @forelse($comments as $comment)
                    <div class="group p-6 bg-white dark:bg-neutral-800 rounded-xl border border-gray-200 dark:border-neutral-700 shadow-sm hover:shadow-md transition-all duration-300 hover:border-gray-300 dark:hover:border-neutral-600">
                        <div class="flex items-start gap-4">
                            <div class="shrink-0">
                                @if($comment->user->profile_photo)
                                    <img class="size-12 rounded-full border-2 border-gray-200 dark:border-neutral-600 group-hover:border-red-200 dark:group-hover:border-red-400 transition-colors duration-300" src="{{ asset(Storage::url($comment->user->profile_photo)) }}" alt="{{ $comment->user->name }}">
                                @else
                                    <img class="size-12 rounded-full border-2 border-gray-200 dark:border-neutral-600 group-hover:border-red-200 dark:group-hover:border-red-400 transition-colors duration-300" src="https://ui-avatars.com/api/?name={{ urlencode($comment->user->name) }}&background=4F46E5&color=fff" alt="{{ $comment->user->name }}">
                                @endif
                            </div>
                            
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-3">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h5 class="font-semibold text-gray-800 dark:text-neutral-200 text-base">
                                            {{ $comment->user->name }}
                                        </h5>
                                        @if($comment->user->id === $post->user->id)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 border border-red-200 dark:border-red-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                            Author
                                        </span>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs text-gray-500 dark:text-neutral-400 font-medium bg-gray-50 dark:bg-neutral-700 px-2 py-1 rounded">
                                            {{ $comment->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                </div>
                                
                                @if($comment->title)
                                    <h6 class="font-semibold text-gray-800 dark:text-neutral-200 text-sm">
                                        {{ $comment->title }}
                                    </h6>
                                @endif
                                
                                <div class="prose prose-sm max-w-none text-gray-600 dark:text-neutral-300">
                                    <p class="whitespace-pre-wrap leading-relaxed">{{ $comment->content }}</p>
                                </div>

                                <!-- Comment Actions -->
                                @auth
                                <!-- <div class="flex items-center gap-4 mt-4 pt-3 border-t border-gray-100 dark:border-neutral-700">
                                    <button class="flex items-center gap-1 text-sm text-gray-500 hover:text-red-600 dark:text-neutral-400 dark:hover:text-red-400 transition-colors duration-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/>
                                        </svg>
                                        Helpful
                                    </button>
                                    <button class="flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 dark:text-neutral-400 dark:hover:text-neutral-300 transition-colors duration-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                        </svg>
                                        Reply
                                    </button>
                                </div> -->
                                @endauth
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-16">
                        <div class="mx-auto w-24 h-24 mb-6 text-gray-200 dark:text-neutral-600">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                        </div>
                        <p class="text-gray-500 dark:text-neutral-400 text-lg font-semibold mb-2">No comments yet</p>
                        <p class="text-gray-400 dark:text-neutral-500 text-sm max-w-md mx-auto">
                            Be the first to share your thoughts on this post. Your comment could start an interesting discussion!
                        </p>
                    </div>
                    @endforelse
                </div>

                <!-- Load More Comments -->
               
            </div>
            <!-- End Enhanced Comments Section -->
        </div>
    </div>
    <!-- End Blog Article -->

    <!-- Sticky Share Group -->
    <div class="sticky bottom-6 inset-x-0 text-center pb-6 lg:pb-10">
        <div class="inline-block bg-white shadow-lg rounded-full py-3 px-4 dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700">
            <div class="flex items-center gap-x-1.5">
                <!-- Comment Count -->
                <div class="hs-tooltip inline-block">
                    <button type="button" class="hs-tooltip-toggle flex items-center gap-x-2 text-sm text-gray-500 hover:text-gray-800 focus:outline-hidden focus:text-gray-800 dark:text-neutral-400 dark:hover:text-neutral-200 dark:focus:text-neutral-200">
                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 21 1.9-5.7a8.5 8.5 0 1 1 3.8 3.8z"/></svg>
                        {{ $comments->count() }}
                        <span class="hs-tooltip-content hs-tooltip-shown:opacity-100 hs-tooltip-shown:visible opacity-0 transition-opacity inline-block absolute invisible z-10 py-1 px-2 bg-gray-900 text-xs font-medium text-white rounded-md shadow-2xs dark:bg-black" role="tooltip">
                            Comments
                        </span>
                    </button>
                </div>
                <!-- End Comment Count -->

                <div class="block h-3 border-e border-gray-300 mx-3 dark:border-neutral-600"></div>

                <!-- Share Button -->
                <div class="hs-dropdown relative inline-flex">
                    <button id="hs-blog-article-share-dropdown" type="button" class="flex items-center gap-x-2 text-sm text-gray-500 hover:text-gray-800 focus:outline-hidden focus:text-gray-800 dark:text-neutral-400 dark:hover:text-neutral-200 dark:focus:text-neutral-200" aria-haspopup="menu" aria-expanded="false" aria-label="Dropdown">
                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/><polyline points="16 6 12 2 8 6"/><line x1="12" x2="12" y1="2" y2="15"/></svg>
                        Share
                    </button>
                    <div class="hs-dropdown-menu w-56 transition-[opacity,margin] duration hs-dropdown-open:opacity-100 opacity-0 hidden mb-1 z-10 bg-gray-900 shadow-md rounded-xl p-2 dark:bg-neutral-950" role="menu" aria-orientation="vertical" aria-labelledby="hs-blog-article-share-dropdown">
                        <a class="flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm text-gray-400 hover:bg-white/10 focus:outline-hidden focus:bg-white/10 dark:text-neutral-400 dark:hover:bg-neutral-900 dark:focus:bg-neutral-900" href="#" onclick="copyToClipboard('{{ url()->current() }}')">
                            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                            Copy link
                        </a>
                        <div class="border-t border-gray-600 my-2 dark:border-neutral-800"></div>
                        @php
                            $shareText = "Check out this blog post: " . $post->title;
                            $shareUrl = url()->current();
                        @endphp
                        <a class="flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm text-gray-400 hover:bg-white/10 focus:outline-hidden focus:bg-white/10 dark:text-neutral-400 dark:hover:bg-neutral-900 dark:focus:bg-neutral-900" href="https://twitter.com/intent/tweet?text={{ urlencode($shareText) }}&url={{ urlencode($shareUrl) }}" target="_blank">
                            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M5.026 15c6.038 0 9.341-5.003 9.341-9.334 0-.14 0-.282-.006-.422A6.685 6.685 0 0 0 16 3.542a6.658 6.658 0 0 1-1.889.518 3.301 3.301 0 0 0 1.447-1.817 6.533 6.533 0 0 1-2.087.793A3.286 3.286 0 0 0 7.875 6.03a9.325 9.325 0 0 1-6.767-3.429 3.289 3.289 0 0 0 1.018 4.382A3.323 3.323 0 0 1 .64 6.575v.045a3.288 3.288 0 0 0 2.632 3.218 3.203 3.203 0 0 1-.865.115 3.23 3.23 0 0 1-.614-.057 3.283 3.283 0 0 0 3.067 2.277A6.588 6.588 0 0 1 .78 13.58a6.32 6.32 0 0 1-.78-.045A9.344 9.344 0 0 0 5.026 15z"/>
                            </svg>
                            Share on Twitter
                        </a>
                        <a class="flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm text-gray-400 hover:bg-white/10 focus:outline-hidden focus:bg-white/10 dark:text-neutral-400 dark:hover:bg-neutral-900 dark:focus:bg-neutral-900" href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($shareUrl) }}" target="_blank">
                            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951z"/>
                            </svg>
                            Share on Facebook
                        </a>
                        <a class="flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm text-gray-400 hover:bg-white/10 focus:outline-hidden focus:bg-white/10 dark:text-neutral-400 dark:hover:bg-neutral-900 dark:focus:bg-neutral-900" href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode($shareUrl) }}" target="_blank">
                            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M0 1.146C0 .513.526 0 1.175 0h13.65C15.474 0 16 .513 16 1.146v13.708c0 .633-.526 1.146-1.175 1.146H1.175C.526 16 0 15.487 0 14.854V1.146zm4.943 12.248V6.169H2.542v7.225h2.401zm-1.2-8.212c.837 0 1.358-.554 1.358-1.248-.015-.709-.52-1.248-1.342-1.248-.822 0-1.359.54-1.359 1.248 0 .694.521 1.248 1.327 1.248h.016zm4.908 8.212V9.359c0-.216.016-.432.08-.586.173-.431.568-.878 1.232-.878.869 0 1.216.662 1.216 1.634v3.865h2.401V9.25c0-2.22-1.184-3.252-2.764-3.252-1.274 0-1.845.7-2.165 1.193v.025h-.016a5.54 5.54 0 0 1 .016-.025V6.169h-2.4c.03.678 0 7.225 0 7.225h2.4z"/>
                            </svg>
                            Share on LinkedIn
                        </a>
                    </div>
                </div>
                <!-- End Share Button -->
            </div>
        </div>
    </div>
    <!-- End Sticky Share Group -->
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Show a nicer toast notification
        const toast = document.createElement('div');
        toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50 transform transition-transform duration-300';
        toast.textContent = 'Link copied to clipboard!';
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }, function(err) {
        console.error('Could not copy text: ', err);
    });
}
</script>