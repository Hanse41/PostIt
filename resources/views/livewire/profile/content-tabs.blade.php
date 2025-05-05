<div class="space-y-6">
    <!-- Enhanced Tab Navigation -->
    <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border dark:border-zinc-800">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between p-3 sm:p-4 border-b dark:border-zinc-800">
            <!-- Tabs with Improved Mobile Layout -->
            <div class="flex space-x-4 sm:space-x-6 overflow-x-auto scrollbar-hide">
                <!-- Posts Tab -->
                <button wire:click="setTab('posts')"
                        class="group flex items-center space-x-2 whitespace-nowrap transition-all">
                    <div class="flex items-center space-x-2 py-2 px-1 border-b-2 {{ $activeTab === 'posts' ? 'border-pink-500' : 'border-transparent' }}">
                        <svg class="w-5 h-5 {{ $activeTab === 'posts' ? 'text-pink-500' : 'text-gray-500 group-hover:text-gray-700 dark:text-gray-400 dark:group-hover:text-gray-300' }}"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>
                        </svg>
                        <span class="font-medium {{ $activeTab === 'posts' ? 'text-pink-500' : 'text-gray-500 group-hover:text-gray-700 dark:text-gray-400 dark:group-hover:text-gray-300' }}">
                            Posts
                        </span>
                        <span class="flex h-5 w-5 items-center justify-center rounded-full text-sm transition-colors {{ $activeTab === 'posts' ? 'bg-pink-100 text-pink-500 dark:bg-pink-500/20' : 'bg-gray-100 text-gray-600 dark:bg-zinc-800 dark:text-gray-400' }}">
                            {{ $user->posts()->count() }}
                        </span>
                    </div>
                </button>

                <!-- Bookmarks Tab -->
                @if (auth()->id() === $user->id)
                    <button wire:click="setTab('saved')"
                            class="group flex items-center space-x-2 whitespace-nowrap transition-all">
                        <div class="flex items-center space-x-2 py-2 px-1 border-b-2 {{ $activeTab === 'saved' ? 'border-pink-500' : 'border-transparent' }}">
                            <svg class="w-5 h-5 {{ $activeTab === 'saved' ? 'text-pink-500' : 'text-gray-500 group-hover:text-gray-700 dark:text-gray-400 dark:group-hover:text-gray-300' }}"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                            </svg>
                            <span class="font-medium {{ $activeTab === 'saved' ? 'text-pink-500' : 'text-gray-500 group-hover:text-gray-700 dark:text-gray-400 dark:group-hover:text-gray-300' }}">
                                Saved
                            </span>
                            <span class="flex h-5 w-5 items-center justify-center rounded-full text-sm transition-colors {{ $activeTab === 'saved' ? 'bg-pink-100 text-pink-500 dark:bg-pink-500/20' : 'bg-gray-100 text-gray-600 dark:bg-zinc-800 dark:text-gray-400' }}">
                                {{ $user->bookmarks()->count() }}
                            </span>
                        </div>
                    </button>
                @endif
            </div>

            <!-- Enhanced Sort Controls -->
            <div class="mt-3 sm:mt-0">
                <div class="relative inline-block w-full sm:w-auto">
                    <select wire:model.live="sortBy"
                            class="w-full sm:w-48 appearance-none pl-3 pr-10 py-2 text-sm font-medium bg-gray-100 dark:bg-zinc-800 border-0 rounded-lg text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-pink-500 cursor-pointer transition-colors">
                        <option value="latest">Latest Posts</option>
                        <option value="oldest">Oldest Posts</option>
                        <option value="popular">Most Popular</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Content Grid with Better Responsiveness -->
    <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        @forelse($posts as $post)
            <div class="group relative aspect-square rounded-xl overflow-hidden bg-gray-100 dark:bg-zinc-900 shadow-sm hover:shadow-md transition-all duration-300">
                @php
                    $firstMedia = $post->media->first();
                    $mediaCount = $post->media->count();
                @endphp

                @if($firstMedia)
                    @if($firstMedia->media_type === 'video')
                        <!-- Enhanced Video Preview -->
                        <div class="absolute inset-0 bg-black">
                            <video class="w-full h-full object-cover" preload="metadata">
                                <source src="{{ Storage::url($firstMedia->media_path) }}" type="video/mp4">
                            </video>
                            <div class="absolute top-2 right-2 sm:top-3 sm:right-3 bg-black/50 backdrop-blur-md rounded-lg p-1.5 sm:p-2">
                                <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                </svg>
                            </div>
                        </div>
                    @else
                        <!-- Enhanced Image Preview -->
                        <img src="{{ Storage::url($firstMedia->media_path) }}"
                             alt="Post preview"
                             class="w-full h-full object-cover transform transition-transform duration-500 group-hover:scale-110"
                             loading="lazy">
                        @if($mediaCount > 1)
                            <div class="absolute top-2 right-2 sm:top-3 sm:right-3 bg-black/50 backdrop-blur-md rounded-lg p-1.5 sm:p-2">
                                <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>
                                </svg>
                            </div>
                        @endif
                    @endif

                    <!-- Enhanced Hover Overlay -->
                    <a href="{{ route('posts.show', $post) }}"
                       class="absolute inset-0 bg-gradient-to-b from-black/10 via-black/25 to-black/75 opacity-0 group-hover:opacity-100 transition-all duration-300">
                        <div class="absolute inset-x-0 bottom-0 p-3 sm:p-4">
                            <div class="flex items-center justify-around">
                                <div class="flex items-center space-x-1 sm:space-x-2">
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 001.106 1.79l.05.025A4 4 0 008.943 18h5.416a2 2 0 001.962-1.608l1.2-6A2 2 0 0015.56 8H12V4a2 2 0 00-2-2 1 1 0 00-1 1v.667a4 4 0 01-.8 2.4L6.8 7.933a4 4 0 00-.8 2.4z"/>
                                    </svg>
                                    <span class="text-sm sm:text-base text-white font-medium">{{ $post->likes_count }}</span>
                                </div>
                                <div class="flex items-center space-x-1 sm:space-x-2">
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zM7 8H5v2h2V8zm2 0h2v2H9V8zm6 0h-2v2h2V8z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-sm sm:text-base text-white font-medium">{{ $post->comments_count }}</span>
                                </div>
                            </div>
                        </div>
                    </a>
                @else
                    <!-- Enhanced Fallback -->
                    <div class="absolute inset-0 flex items-center justify-center bg-gray-200 dark:bg-zinc-800">
                        <svg class="w-10 h-10 sm:w-12 sm:h-12 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                @endif
            </div>
        @empty
            <!-- Enhanced Empty State -->
            <div class="col-span-full">
                <div class="flex flex-col items-center justify-center py-12 sm:py-16 text-center">
                    <svg class="w-14 h-14 sm:w-16 sm:h-16 text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-lg sm:text-xl font-medium text-gray-900 dark:text-white mb-2">
                        {{ $activeTab === 'posts' ? 'No posts yet' : 'No saved posts yet' }}
                    </p>
                    <p class="text-sm sm:text-base text-gray-500 dark:text-gray-400">
                        {{ $activeTab === 'posts' ? 'Share your first post with the world!' : 'Posts you save will appear here.' }}
                    </p>
                </div>
            </div>
        @endforelse
    </div>
</div>
