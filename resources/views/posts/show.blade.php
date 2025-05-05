

<x-layouts.app :title="$post->user->name . '\'s Post'">
    <div class="max-w-6xl mx-auto px-4 py-8">
        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm overflow-hidden">
            <!-- Grid Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-2 min-h-[90vh] lg:min-h-[800px]">
                <!-- Media Section -->
                <div class="relative bg-black flex items-center justify-center"
                    x-data="{
                        currentIndex: 0,
                        isLoading: true,
                        init() {
                            this.$watch('currentIndex', () => {
                                this.isLoading = true;
                                setTimeout(() => this.isLoading = false, 300);
                            });
                        }
                    }">
                    @php
                        $mediaItems = $post->media()->orderBy('id', 'asc')->get();
                        $mediaCount = $mediaItems->count();
                    @endphp

                    <!-- Loading Indicator -->
                    <div x-show="isLoading"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        class="absolute inset-0 flex items-center justify-center bg-black/20 backdrop-blur-sm z-10">
                        <div class="w-8 h-8 border-2 border-pink-500 border-t-transparent rounded-full animate-spin"></div>
                    </div>

                    <!-- Media Container -->
                    <div class="relative w-full h-full max-h-screen">
                        @foreach($mediaItems as $index => $media)
                            <div x-show="currentIndex === {{ $index }}"
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 transform scale-95"
                                 x-transition:enter-end="opacity-100 transform scale-100"
                                 x-transition:leave="transition ease-in duration-200"
                                 x-transition:leave-start="opacity-100 transform scale-100"
                                 x-transition:leave-end="opacity-0 transform scale-95"
                                 class="absolute inset-0 flex items-center justify-center">
                                @if($media->media_type === 'video')
                                    <video class="w-full h-full object-contain max-h-[calc(100vh-4rem)]"
                                           controls
                                           playsinline
                                           @loadeddata="isLoading = false"
                                           preload="metadata">
                                        <source src="{{ Storage::url($media->media_path) }}" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                @else
                                    <img src="{{ Storage::url($media->media_path) }}"
                                         alt="Post media"
                                         class="w-full h-full object-contain max-h-[calc(100vh-4rem)]"
                                         @load="isLoading = false"
                                         loading="lazy"
                                         onerror="this.onerror=null; this.src='{{ asset('images/placeholder.png') }}';">
                                @endif
                            </div>
                        @endforeach
                    </div>

                    @if($mediaCount > 1)
                        <!-- Navigation Controls -->
                        <div class="absolute inset-0 flex items-center justify-between px-4">
                            <button @click="currentIndex = (currentIndex - 1 + {{ $mediaCount }}) % {{ $mediaCount }}"
                                    :disabled="isLoading"
                                    class="p-2 rounded-full bg-black/50 backdrop-blur-sm text-white hover:bg-black/75 transition-all duration-200 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed transform hover:scale-110"
                                    :class="{ 'opacity-0': currentIndex === 0, 'opacity-100': currentIndex !== 0 }">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </button>
                            <button @click="currentIndex = (currentIndex + 1) % {{ $mediaCount }}"
                                    :disabled="isLoading"
                                    class="p-2 rounded-full bg-black/50 backdrop-blur-sm text-white hover:bg-black/75 transition-all duration-200 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed transform hover:scale-110"
                                    :class="{ 'opacity-0': currentIndex === {{ $mediaCount - 1 }}, 'opacity-100': currentIndex !== {{ $mediaCount - 1 }} }">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                        </div>

                        <!-- Media Counter -->
                        <div class="absolute top-4 right-4 px-3 py-1.5 rounded-lg bg-black/50 backdrop-blur-sm text-white text-sm font-medium z-10">
                            <span x-text="currentIndex + 1"></span>/<span>{{ $mediaCount }}</span>
                        </div>

                        <!-- Slide Indicators -->
                        <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2 z-10">
                            @foreach($mediaItems as $index => $media)
                                <button @click="currentIndex = {{ $index }}"
                                        :disabled="isLoading"
                                        class="w-2.5 h-2.5 rounded-full transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed transform hover:scale-110"
                                        :class="{
                                            'bg-pink-500 scale-100': currentIndex === {{ $index }},
                                            'bg-white/50 hover:bg-white/75 scale-90': currentIndex !== {{ $index }}
                                        }">
                                </button>
                            @endforeach
                        </div>
                    @endif

                    <!-- Keyboard Navigation -->
                    <div x-effect="
                        function handleKeydown(e) {
                            if (!isLoading && e.key === 'ArrowLeft' && currentIndex > 0) {
                                currentIndex = (currentIndex - 1 + {{ $mediaCount }}) % {{ $mediaCount }};
                            } else if (!isLoading && e.key === 'ArrowRight' && currentIndex < {{ $mediaCount - 1 }}) {
                                currentIndex = (currentIndex + 1) % {{ $mediaCount }};
                            }
                        }
                        window.addEventListener('keydown', handleKeydown);
                        return () => window.removeEventListener('keydown', handleKeydown);
                    "></div>
                </div>

                <!-- Content Section -->
                <div class="flex flex-col h-full">
                    <!--  Post Header -->
                    <div class="p-6 border-b dark:border-zinc-700">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <a href="{{ route('profile.show', $post->user) }}"
                                   class="group">
                                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-pink-500 via-red-500 to-orange-500 p-[2px] group-hover:from-pink-600 group-hover:via-red-600 group-hover:to-orange-600 transition">
                                        <img src="{{ $post->user->avatar }}"
                                             alt="{{ $post->user->name }}"
                                             class="w-full h-full rounded-full object-cover ring-1 ring-white dark:ring-zinc-800">
                                    </div>
                                </a>
                                <div>
                                    <a href="{{ route('profile.show', $post->user) }}" class="font-semibold hover:underline transition">
                                        {{ $post->user->name }}
                                    </a>
                                    @if($post->created_at)
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $post->created_at->diffForHumans() }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <!-- Post Options -->
                            @if($post->user_id === auth()->id())
                                <div class="relative" x-data="{ open: false }">
                                    <button @click="open = !open"
                                            class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-zinc-700 transition">
                                        <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                        </svg>
                                    </button>

                                    <!-- Options Dropdown -->
                                    <div x-show="open"
                                         @click.away="open = false"
                                         class="absolute right-0 mt-2 w-48 bg-white dark:bg-zinc-800 rounded-lg shadow-lg py-2 z-50">
                                        <a href="{{ route('posts.edit', $post) }}"
                                           class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-zinc-700">
                                            Edit Post
                                        </a>
                                        <form action="{{ route('posts.destroy', $post) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/10">
                                                Delete Post
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!--scrollable content area -->
                    <div class="flex-1 overflow-y-auto scrollbar-thin scrollbar-thumb-gray-300 dark:scrollbar-thumb-zinc-600">
                        <div class="p-4 space-y-4">
                            @if($post->caption)
                                <p class="text-sm">
                                    <a href="{{ route('profile.show', $post->user) }}" class="font-semibold hover:underline">{{ $post->user->name }}</a>
                                    <span class="ml-1">{{ $post->caption }}</span>
                                </p>
                            @endif

                            @if($post->tags)
                                <div class="flex flex-wrap gap-2">
                                    @foreach(json_decode($post->tags, true) as $tag)
                                        <a href="{{ route('posts.index', ['tag' => $tag]) }}"
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-200">
                                            #{{ $tag }}
                                        </a>
                                    @endforeach
                                </div>

                            @endif

                            <!-- Post Actions for Mobile - Show only on small screens -->
                            <div class="block lg:hidden border-t dark:border-zinc-700 pt-4">
                                <div class="flex justify-between items-center">
                                    <div class="flex items-center space-x-4">
                                        <livewire:like-button :post="$post" :wire:key="'like-'.$post->id" />
                                        <livewire:comment-modal :post="$post" :wire:key="'comment-modal-mobile-'.$post->id" />
                                        <button class="p-2 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 rounded-full hover:bg-gray-100 dark:hover:bg-zinc-700 transition-colors duration-200">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                                            </svg>
                                        </button>
                                    </div>
                                    <livewire:bookmark-button :post="$post" :wire:key="'bookmark-'.$post->id" />
                                </div>
                            </div>

                            <!-- Comment Section-->
                            <div class="hidden lg:block flex-1 overflow-y-auto scrollbar-thin scrollbar-thumb-gray-300 dark:scrollbar-thumb-zinc-600">
                                @if($post->comments_count > 0)
                                    <livewire:show-page-comments :post="$post" :wire:key="'comments-'.$post->id" />
                                @else
                                    <div class="flex flex-col items-center justify-center py-8 text-center">
                                        <svg class="w-16 h-16 text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                        </svg>
                                        <p class="text-gray-500 dark:text-gray-400 mb-2">No comments yet</p>
                                        <p class="text-sm text-gray-400 dark:text-gray-500">Be the first to share your thoughts</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <!-- Post Actions -->
                    <div class="hidden lg:block">
                        <!-- Comment Input -->
                        <div class="bg-white dark:bg-zinc-800">
                            <livewire:comment-input :post="$post" :wire:key="'comment-input-'.$post->id" />
                        </div>

                        <!-- Actions Bar -->
                        <div class="border-t dark:border-zinc-700 p-4 bg-white dark:bg-zinc-800">
                            <div class="flex justify-between items-center">
                                <div class="flex items-center space-x-4">
                                    <livewire:like-button :post="$post" :wire:key="'like-desktop-'.$post->id" />
                                    <button class="p-2 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 rounded-full hover:bg-gray-100 dark:hover:bg-zinc-700 transition-colors duration-200">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                                        </svg>
                                    </button>
                                </div>
                                <livewire:bookmark-button :post="$post" :wire:key="'bookmark-desktop-'.$post->id" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>



