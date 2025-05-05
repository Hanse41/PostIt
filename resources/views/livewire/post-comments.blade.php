<div class="space-y-6">

    <!-- Comments List -->
    <div class="space-y-6 max-h-[400px] overflow-y-auto pr-4 scrollbar-thin scrollbar-thumb-gray-300 dark:scrollbar-thumb-zinc-600 scrollbar-track-transparent">
        @if($comments->isEmpty())
            <div class="flex flex-col items-center justify-center py-8 text-center">
                <svg class="w-16 h-16 text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <p class="text-gray-500 dark:text-gray-400 mb-2">No comments yet</p>
                <p class="text-sm text-gray-400 dark:text-gray-500">Be the first to share your thoughts</p>
            </div>
        @else
            @foreach($comments as $comment)
                <div class="group relative">
                    <!-- Main Comment -->
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0 w-10 h-10 sm:w-8 sm:h-8 rounded-full bg-gradient-to-br from-pink-500 via-red-500 to-orange-500 p-[2px] shadow-lg z-10">
                            <img src="{{ $comment->user->avatar }}"
                                 alt="{{ $comment->user->name }}"
                                 class="w-full h-full rounded-full object-cover">
                        </div>
                        <div class="flex-1">
                            <div class="bg-gray-100 dark:bg-zinc-900 rounded-2xl px-4 py-3 relative group hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition-colors duration-200">
                                <div class="flex flex-col gap-1">
                                    <div class="flex items-center justify-between">
                                        <a href="{{ route('profile.show', $comment->user) }}"
                                           class="font-semibold text-sm hover:underline">
                                            {{ $comment->user->name }}
                                        </a>
                                        <span class="text-xs text-gray-500">
                                            {{ $comment->created_at}}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $comment->body }}</p>
                                </div>

                                <!-- Comment Actions -->
                                <div class="mt-2 flex items-center space-x-4 text-sm opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                    @auth
                                        <button wire:click="$dispatch('setReplyTo', { username: '{{ $comment->user->name }}', commentId: {{ $comment->id }} })"
                                                class="text-gray-500 hover:text-pink-500 dark:hover:text-pink-400 transition-colors duration-200 flex items-center space-x-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                            </svg>
                                            <span>Reply</span>
                                        </button>
                                        @if($comment->user_id === auth()->id())
                                            <button wire:click="$dispatch('startEditing', { commentId: {{ $comment->id }} })"
                                                    class="text-gray-500 hover:text-pink-500 dark:hover:text-pink-400 transition-colors duration-200 flex items-center space-x-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                                <span>Edit</span>
                                            </button>
                                        @endif
                                        @if($comment->user_id === auth()->id() || $post->user_id === auth()->id())
                                            <button wire:click="$dispatch('deleteComment', { commentId: {{ $comment->id }} })"
                                                    class="text-gray-500 hover:text-red-500 transition-colors duration-200 flex items-center space-x-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                <span>Delete</span>
                                            </button>
                                        @endif
                                    @endauth
                                </div>
                            </div>
                        </div>
                    </div>



                    <!-- Replies Section -->
                    @if($comment->replies->count() > 0)
                        <div class="relative ml-11 mt-2 pt-2" x-data="{ showReplies: false }">
                            <!-- Toggle Replies Button -->
                            <button @click="showReplies = !showReplies"
                                    class="ml-8 text-sm text-gray-500 hover:text-pink-500 dark:hover:text-pink-400 transition-colors duration-200 flex items-center space-x-2">
                                <svg class="w-4 h-4 transition-transform duration-200"
                                    :class="{ 'rotate-180': showReplies }"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                                <span>
                                    {{ $comment->replies->count() }} {{ Str::plural('reply', $comment->replies->count()) }}
                                </span>
                            </button>

                            <!-- Replies Container -->
                            <div x-show="showReplies" x-collapse>
                                <!-- Visual Connection Line -->
                                <div class="absolute left-[-24px] top-0 bottom-0 w-px bg-gray-400"></div>

                                @foreach($comment->replies->take($repliesPerComment) as $reply)
                                <div class="relative flex items-start space-x-2">
                                    <!-- Horizontal Connection Line -->
                                    <div class="absolute left-[-24px] top-3 w-5 h-px bg-gray-400"></div>

                                    <div class="flex-shrink-0 w-8 h-8 sm:w-6 sm:h-6 rounded-full bg-gradient-to-br from-pink-500 via-red-500 to-orange-500 p-[2px] shadow-lg z-10">
                                        <img src="{{ $reply->user->avatar }}"
                                            alt="{{ $reply->user->name }}"
                                            class="w-full h-full rounded-full object-cover">
                                    </div>
                                    <div class="flex-1">
                                        <div class="bg-gray-100 dark:bg-zinc-900 rounded-2xl px-3 py-2 group hover:bg-gray-50 dark:hover:bg-zinc-800/50 transition-colors duration-200">
                                            <div class="flex flex-col gap-1">
                                                <div class="flex items-center justify-between">
                                                    <a href="{{ route('profile.show', $reply->user) }}"
                                                       class="font-semibold text-sm hover:underline">
                                                        {{ $reply->user->name }}
                                                    </a>
                                                    <span class="text-xs text-gray-500">
                                                        {{ $reply->created_at }}
                                                    </span>
                                                </div>
                                                <p class="text-sm text-gray-700 dark:text-gray-300">{{ $reply->body }}</p>
                                            </div>

                                            <!-- Reply Actions -->
                                            <div class="mt-2 flex items-center space-x-4 text-sm opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                                @auth
                                                    @if($reply->user_id === auth()->id())
                                                        <button wire:click="$dispatch('startEditing', { commentId: {{ $reply->id }} })"
                                                                class="text-gray-500 hover:text-pink-500 dark:hover:text-pink-400 transition-colors duration-200 flex items-center space-x-1">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                            </svg>
                                                            <span>Edit</span>
                                                        </button>
                                                    @endif
                                                    @if($reply->user_id === auth()->id() || $post->user_id === auth()->id())
                                                        <button wire:click="$dispatch('deleteComment', { commentId: {{ $reply->id }} })"
                                                                class="text-gray-500 hover:text-red-500 transition-colors duration-200 flex items-center space-x-1">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                            </svg>
                                                            <span>Delete</span>
                                                        </button>
                                                    @endif
                                                @endauth
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                 @endforeach

                                @if($comment->replies->count() > $repliesPerComment && !in_array($comment->id, $expandedComments))
                                    <div class="ml-8 mt-5">
                                        <button wire:click="loadMoreReplies({{ $comment->id }})"
                                                class="inline-flex items-center space-x-2 text-sm text-gray-500 hover:text-pink-500 dark:hover:text-pink-400 transition-colors duration-200">
                                            <span wire:loading.remove wire:target="loadMoreReplies({{ $comment->id }})">
                                                Show {{ min(3, $comment->replies->count() - $repliesPerComment) }} more {{ Str::plural('reply', min(3, $comment->replies->count() - $repliesPerComment)) }}
                                            </span>
                                            <span wire:loading wire:target="loadMoreReplies({{ $comment->id }})" class="inline-flex items-center">
                                                <svg class="animate-spin mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                                Loading replies...
                                            </span>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach

            <!-- Load More Comments Button -->
            @if($hasMorePages)
                <div class="flex justify-center py-4">
                    <button wire:click="loadMore"
                            wire:loading.attr="disabled"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-pink-500 dark:hover:text-pink-400 transition-colors disabled:opacity-50">
                        <span wire:loading.remove wire:target="loadMore">
                            Load More Comments
                        </span>
                        <span wire:loading wire:target="loadMore" class="inline-flex items-center">
                            <svg class="animate-spin mr-2 h-4 w-4 text-pink-500" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Loading...
                        </span>
                    </button>
                </div>
            @endif
        @endif
    </div>


</div>
