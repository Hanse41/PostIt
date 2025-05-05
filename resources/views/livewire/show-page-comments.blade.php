<div class="flex flex-col h-full" x-data="{ emojiPicker: false }">
    <!-- Comments Header -->
    <div class="px-6 pb-2 border-b dark:border-zinc-700">
        <h3 class="text-lg font-semibold">Comments ({{ $commentsCount }})</h3>
    </div>

    <!-- Comments List -->
    <div class="flex-1 overflow-y-auto max-h-[calc(100vh-350px)] lg:min-h-[400px] lg:max-h-[400px] px-6 scrollbar-thin scrollbar-thumb-gray-300 dark:scrollbar-thumb-zinc-600 scrollbar-track-transparent">
        @forelse($comments as $comment)
            <div class="py-6 first:pt-4 last:pb-4 {{ !$loop->last ? 'border-b dark:border-zinc-700' : '' }}">
                <div class="flex space-x-4">
                    <!-- User Avatar -->
                    <a href="{{ route('profile.show', $comment->user) }}"
                       class="flex-shrink-0 group">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-pink-500 via-red-500 to-orange-500 p-[2px] group-hover:from-pink-600 group-hover:via-red-600 group-hover:to-orange-600">
                            <img src="{{ $comment->user->avatar }}"
                                 alt="{{ $comment->user->name }}"
                                 class="w-full h-full rounded-full object-cover ring-1 ring-white dark:ring-zinc-800">
                        </div>
                    </a>

                    <!-- Comment Content -->
                    <div class="flex-1 space-y-2">
                        <div class="flex items-center justify-between">
                            <a href="{{ route('profile.show', $comment->user) }}"
                               class="font-semibold hover:underline">
                                {{ $comment->user->name }}
                            </a>
                            <!-- Comment Options -->
                            @if($comment->user_id === auth()->id())
                                <div class="relative ml-auto" x-data="{ open: false }">
                                    <button @click="open = !open"
                                            class="p-1 text-gray-400 hover:text-gray-600 rounded-full hover:bg-gray-100 dark:hover:bg-zinc-700">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                        </svg>
                                    </button>

                                    <div x-show="open"
                                         @click.away="open = false"
                                         class="absolute right-0 mt-2 w-48 bg-white dark:bg-zinc-800 rounded-lg shadow-lg py-1 z-50">
                                        <button wire:click="startEditing({{ $comment->id }})"
                                                @click="open = false"
                                                class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-zinc-700">
                                            Edit
                                        </button>
                                        <button wire:click="deleteComment({{ $comment->id }})"
                                                @click="open = false"
                                                class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/10">
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            @endif

                        </div>

                        @if($editingCommentId === $comment->id)
                            <div class="space-y-2">
                                <textarea wire:model="editingComment"
                                          class="w-full px-4 py-2 text-sm border rounded-lg focus:ring-pink-500 focus:border-pink-500 dark:bg-zinc-800 dark:border-zinc-700"
                                          rows="3"></textarea>
                                <div class="flex justify-end space-x-2">
                                    <button wire:click="cancelEditing"
                                            class="px-3 py-1.5 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800">
                                        Cancel
                                    </button>
                                    <button wire:click="updateComment"
                                            class="px-3 py-1.5 text-sm bg-pink-500 text-white rounded-lg hover:bg-pink-600">
                                        Save
                                    </button>
                                </div>
                            </div>
                        @else
                            <p class="text-gray-800 dark:text-gray-200">
                                @if($comment->mentioned_user)
                                    <span class="text-pink-500 font-medium">{{ '@'.$comment->mentioned_user }}</span>
                                @endif
                                {{ $comment->body }}
                            </p>
                        @endif

                        <!-- Comment Actions -->
                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-gray-500 dark:text-gray-500">
                                {{ $comment->created_at }}
                            </span>
                            <!-- Like Button -->
                            <button wire:click="toggleCommentLike({{ $comment->id }})"
                                    class="flex items-center space-x-1 text-sm {{ $comment->isLikedBy(auth()->user()) ? 'text-pink-500' : 'text-gray-500 hover:text-pink-500' }}">
                                <svg class="w-5 h-5" fill="{{ $comment->isLikedBy(auth()->user()) ? 'currentColor' : 'none' }}"
                                     stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                                <span>{{ $comment->likes()->count() }}</span>
                            </button>

                            <!-- Reply Button -->
                            <button wire:click="setReplyTo('{{ $comment->user->name }}', {{ $comment->id }})"
                                    class="text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                                Reply
                            </button>


                        </div>

                        <!-- Replies -->
                        @if($comment->replies->count() > 0)
                            <div class="relative mt-4 space-y-4 pl-4 border-l-2 border-gray-100 dark:border-zinc-700"
                            x-data="{ showReplies: false }">
                            <button @click="showReplies = !showReplies"
                                    class="text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 mb-4 flex items-center space-x-2">
                                <svg class="w-4 h-4 transition-transform"
                                    :class="{ 'rotate-90': showReplies }"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                                <span>{{ $comment->totalRepliesCount }} {{ Str::plural('reply', $comment->totalRepliesCount) }}</span>
                            </button>

                                <div x-show="showReplies"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 transform -translate-y-2"
                                x-transition:enter-end="opacity-100 transform translate-y-0"
                                x-cloak
                                class="space-y-4">
                                    @foreach($comment->replies->take($comment->showRepliesCount) as $reply)
                                        <div class="flex space-x-4 relative">
                                            <div class="absolute -left-4 top-4 w-4 h-0.5 bg-gray-200 dark:bg-zinc-700"></div>

                                            <a href="{{ route('profile.show', $reply->user) }}"
                                            class="flex-shrink-0 group">
                                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-pink-500 via-red-500 to-orange-500 p-[2px]">
                                                    <img src="{{ $reply->user->avatar }}"
                                                        alt="{{ $reply->user->name }}"
                                                        class="w-full h-full rounded-full object-cover ring-2 ring-white dark:ring-zinc-800">
                                                </div>
                                            </a>

                                            <div class="flex-1 space-y-2">
                                                <!-- Reply Header -->
                                                <div class="flex items-center justify-between">
                                                    <a href="{{ route('profile.show', $reply->user) }}"
                                                    class="font-semibold hover:underline">
                                                        {{ $reply->user->name }}
                                                    </a>
                                                    @if($reply->user_id === auth()->id())
                                                        <div class="relative ml-auto" x-data="{ open: false }">
                                                            <button @click="open = !open"
                                                                    class="p-1 text-gray-400 hover:text-gray-600 rounded-full hover:bg-gray-100 dark:hover:bg-zinc-700">
                                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                                    <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                                                </svg>
                                                            </button>

                                                            <div x-show="open"
                                                                @click.away="open = false"
                                                                class="absolute right-0 mt-2 w-48 bg-white dark:bg-zinc-800 rounded-lg shadow-lg py-1 z-50">
                                                                <button wire:click="startEditing({{ $reply->id }})"
                                                                        @click="open = false"
                                                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-zinc-700">
                                                                    Edit
                                                                </button>
                                                                <button wire:click="deleteComment({{ $reply->id }})"
                                                                        @click="open = false"
                                                                        class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/10">
                                                                    Delete
                                                                </button>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>

                                                <!-- Reply Content -->
                                                @if($editingCommentId === $reply->id)
                                                    <div class="space-y-2">
                                                        <textarea wire:model="editingComment"
                                                                class="w-full px-4 py-2 text-sm border rounded-lg focus:ring-pink-500 focus:border-pink-500 dark:bg-zinc-800 dark:border-zinc-700"
                                                                rows="2"></textarea>
                                                        <div class="flex justify-end space-x-2">
                                                            <button wire:click="cancelEditing"
                                                                    class="px-3 py-1.5 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800">
                                                                Cancel
                                                            </button>
                                                            <button wire:click="updateComment"
                                                                    class="px-3 py-1.5 text-sm bg-pink-500 text-white rounded-lg hover:bg-pink-600">
                                                                Save
                                                            </button>
                                                        </div>
                                                    </div>
                                                @else
                                                    <p class="text-gray-800 dark:text-gray-200 text-sm">
                                                        @if($reply->mentioned_user)
                                                            <span class="text-pink-500 font-medium">{{ '@'.$reply->mentioned_user }}</span>
                                                        @endif
                                                        {{ $reply->body }}
                                                    </p>
                                                @endif

                                                <!-- Reply Actions -->
                                                <div class="flex items-center space-x-4">
                                                    <span class="text-sm text-gray-500 dark:text-gray-500">
                                                        {{ $reply->created_at }}
                                                    </span>
                                                    <button wire:click="toggleCommentLike({{ $reply->id }})"
                                                            class="flex items-center space-x-1 text-xs {{ $reply->isLikedBy(auth()->user()) ? 'text-pink-500' : 'text-gray-500 hover:text-pink-500' }}">
                                                        <svg class="w-4 h-4" fill="{{ $reply->isLikedBy(auth()->user()) ? 'currentColor' : 'none' }}"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                                        </svg>
                                                        <span>{{ $reply->likes()->count() }}</span>
                                                    </button>

                                                    <button wire:click="setReplyTo('{{ $reply->user->name }}', {{ $comment->id }})"
                                                        class="text-xs text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                                                    Reply
                                                    </button>


                                                </div>
                                            </div>
                                        </div>
                                    @endforeach

                                    <!-- Load More Button -->
                                    @if($comment->totalRepliesCount > $comment->showRepliesCount)
                                        <button wire:click="loadMoreReplies({{ $comment->id }})"
                                                class="mt-2 text-sm text-pink-500 hover:text-pink-600 ml-12 flex items-center space-x-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                            </svg>
                                            <span>Show more replies ({{ $comment->totalRepliesCount - $comment->showRepliesCount }} remaining)</span>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center py-12">
                <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <p class="text-gray-500 dark:text-gray-400 text-center">
                    No comments yet.<br>
                    Be the first to share your thoughts!
                </p>
            </div>
        @endforelse
    </div>


</div>
