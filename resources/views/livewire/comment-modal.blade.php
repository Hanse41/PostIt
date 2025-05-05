@php
    use Carbon\Carbon;
@endphp

<div x-data="{
    open: false,
    emojiPicker: false,
    addEmoji(emoji) {
        $wire.set('newComment', ($wire.get('newComment') || '') + emoji);
        this.emojiPicker = false;
    }
}"
     @keydown.escape.window="open = false; emojiPicker = false"
     @openModal.window="open = true">

    <!-- Comment Button Trigger -->
    <button @click="open = true"
            class="flex justify-center items-center align-middle space-x-2 text-gray-500 hover:text-gray-700 transition-colors">
        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
        </svg>

        <span class="text-sm text-gray-500 dark:text-zinc-400">
            {{ $commentsCount }}
        </span>
    </button>

    <!-- Modal Backdrop -->
    <div x-show="open"
         class="fixed inset-0 bg-black bg-opacity-50  z-50"
         @click="open = false">
    </div>

    <!-- Modal Content -->
    <div x-show="open"
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         @click.away="open = false">
        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-xl w-full max-w-lg overflow-hidden"
             @click.stop>
            <!-- Modal Header -->
            <div class="px-4 py-3 border-b dark:border-zinc-700 flex items-center justify-between">
                <h3 class="text-lg font-semibold">Comments {{'('.$commentsCount.')'}}</h3>
                <button @click="open = false" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Comments List -->
            <div class="p-4 max-h-[60vh] overflow-y-auto space-y-4">
                @forelse($comments as $comment)
                    <div class="flex items-start space-x-3 pb-4 border-b dark:border-zinc-700">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-pink-500 via-red-500 to-orange-500 p-[2px]">
                            <img src="{{ $comment->user->avatar }}"
                                alt="{{ $comment->user->name }}"
                                class="w-full h-full rounded-full object-cover">
                        </div>
                        <div class="flex-1">
                            <div class="flex justify-between items-start w-full">
                                <div class="flex flex-col">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('profile.show', $comment->user) }}"
                                        class="font-semibold text-sm hover:underline">
                                            {{ $comment->user->name }}
                                        </a>
                                    </div>

                                    @if($editingCommentId === $comment->id)
                                        <div class="mt-2">
                                            <textarea wire:model="editingComment"
                                                    class="w-full p-2 text-sm border rounded-lg focus:ring-pink-500 focus:border-pink-500 dark:bg-zinc-800 dark:border-zinc-700"
                                                    rows="2"></textarea>
                                            <div class="flex justify-end space-x-2 mt-2">
                                                <button wire:click="cancelEditing"
                                                        class="text-xs text-gray-500 hover:text-gray-700">
                                                    Cancel
                                                </button>
                                                <button wire:click="updateComment"
                                                        class="text-xs text-pink-500 hover:text-pink-600 font-medium">
                                                    Save
                                                </button>
                                            </div>
                                        </div>
                                    @else
                                        <p class="text-sm mt-1">
                                            @if($comment->mentioned_user)
                                                <span class="text-pink-500">{{ '@'.$comment->mentioned_user }}</span>
                                            @endif
                                            {{ $comment->body }}
                                        </p>
                                        <div class="flex items-center space-x-4 mt-2 text-xs text-gray-500">
                                            <span>{{ Carbon::parse($comment->created_at)->diffForHumans() }}</span>
                                            <div class="flex items-center space-x-1">
                                                @if($comment->isLikedBy(auth()->user()))
                                                    <button wire:click="toggleCommentLike({{ $comment->id }})"
                                                            class="text-pink-500 hover:text-pink-600">
                                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd"
                                                                d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"
                                                                clip-rule="evenodd"/>
                                                        </svg>
                                                    </button>
                                                @else
                                                    <button wire:click="toggleCommentLike({{ $comment->id }})"
                                                            class="text-gray-400 hover:text-pink-500">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                                        </svg>
                                                    </button>
                                                @endif
                                                <span class="text-xs">{{ $comment->likes()->count() }}</span>
                                            </div>
                                            <button wire:click="setReplyTo('{{ $comment->user->name }}', {{ $comment->id }})"
                                                    class="hover:text-gray-700">
                                                Reply
                                            </button>
                                        </div>
                                    @endif
                                </div>

                                @if($comment->user_id === auth()->id())
                                    <div class="relative" x-data="{ open: false }">
                                        <button @click="open = !open"
                                                class="text-gray-400 hover:text-gray-600 transition-colors">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                            </svg>
                                        </button>
                                        <div x-show="open"
                                            @click.away="open = false"
                                            class="absolute right-0 mt-2 w-48 bg-white dark:bg-zinc-800 rounded-md shadow-lg py-1 z-50">
                                            <button wire:click="startEditing({{ $comment->id }})"
                                                    @click="open = false"
                                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-zinc-700">
                                                Edit
                                            </button>
                                            <button wire:click="deleteComment({{ $comment->id }})"
                                                    @click="open = false"
                                                    onclick="return confirm('Are you sure you want to delete this comment?')"
                                                    class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 dark:hover:bg-zinc-700">
                                                Delete
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>


                            <!-- Replies Section -->
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
                                            <div class="relative flex items-start space-x-3">
                                            <div class="absolute -left-4 top-4 w-3 h-0.5 bg-gray-200 dark:bg-zinc-700"></div>

                                            <div class="w-6 h-6 rounded-full bg-gradient-to-br from-pink-500 via-red-500 to-orange-500 p-[2px]">
                                                <img src="{{ $reply->user->avatar }}"
                                                    alt="{{ $reply->user->name }}"
                                                    class="w-full h-full rounded-full object-cover">
                                            </div>
                                            <div class="flex-1">
                                                <div class="flex justify-between items-start w-full">
                                                    <div class="flex flex-col">
                                                        <div class="flex items-center space-x-2">
                                                            <a href="{{ route('profile.show', $reply->user) }}"
                                                            class="font-semibold text-sm hover:underline">
                                                                {{ $reply->user->name }}
                                                            </a>
                                                        </div>

                                                        @if($editingCommentId === $reply->id)
                                                            <!-- Edit form for replies -->
                                                            <div class="mt-2">
                                                                <textarea wire:model="editingComment"
                                                                        class="w-full p-2 text-sm border rounded-lg focus:ring-pink-500 focus:border-pink-500 dark:bg-zinc-800 dark:border-zinc-700"
                                                                        rows="2"></textarea>
                                                                <div class="flex justify-end space-x-2 mt-2">
                                                                    <button wire:click="cancelEditing"
                                                                            class="text-xs text-gray-500 hover:text-gray-700">
                                                                        Cancel
                                                                    </button>
                                                                    <button wire:click="updateComment"
                                                                            class="text-xs text-pink-500 hover:text-pink-600 font-medium">
                                                                        Save
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        @else
                                                            <p class="text-sm mt-1">
                                                                @if($reply->mentioned_user)
                                                                    <span class="text-pink-500">{{ '@'.$reply->mentioned_user }}</span>
                                                                @endif
                                                                {{ $reply->body }}
                                                            </p>
                                                            <div class="flex items-center space-x-4 mt-2 text-xs text-gray-500">
                                                                <span>{{ Carbon::parse($reply->created_at)->diffForHumans() }}</span>
                                                                <div class="flex items-center space-x-1">
                                                                    @if($reply->isLikedBy(auth()->user()))
                                                                        <button wire:click="toggleCommentLike({{ $reply->id }})"
                                                                                class="text-pink-500 hover:text-pink-600">
                                                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                                                <path fill-rule="evenodd"
                                                                                    d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"
                                                                                    clip-rule="evenodd"/>
                                                                            </svg>
                                                                        </button>
                                                                    @else
                                                                        <button wire:click="toggleCommentLike({{ $reply->id }})"
                                                                                class="text-gray-400 hover:text-pink-500">
                                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round"
                                                                                    stroke-linejoin="round"
                                                                                    stroke-width="2"
                                                                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                                                            </svg>
                                                                        </button>
                                                                    @endif
                                                                    <span class="text-xs">{{ $reply->likes()->count() }}</span>
                                                                </div>
                                                                <button wire:click="setReplyTo('{{ $reply->user->name }}', {{ $reply->id }})"
                                                                        class="hover:text-gray-700">
                                                                    Reply
                                                                </button>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    @if($reply->user_id === auth()->id())
                                                        <div class="relative" x-data="{ open: false }">
                                                            <button @click="open = !open"
                                                                    class="text-gray-400 hover:text-gray-600 transition-colors">
                                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                                    <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                                                </svg>
                                                            </button>
                                                            <div x-show="open"
                                                                @click.away="open = false"
                                                                class="absolute right-0 mt-2 w-48 bg-white dark:bg-zinc-800 rounded-md shadow-lg py-1 z-50">
                                                                <button wire:click="startEditing({{ $reply->id }})"
                                                                        @click="open = false"
                                                                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-zinc-700">
                                                                    Edit
                                                                </button>
                                                                <button wire:click="deleteComment({{ $reply->id }})"
                                                                        @click="open = false"
                                                                        onclick="return confirm('Are you sure you want to delete this reply?')"
                                                                        class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 dark:hover:bg-zinc-700">
                                                                    Delete
                                                                </button>
                                                            </div>
                                                        </div>
                                                    @endif
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
                @empty
                    <div class="flex flex-col items-center justify-center py-8 text-center">
                        <svg class="w-16 h-16 text-gray-400 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <p class="text-gray-500 dark:text-gray-400">No comments yet. Be the first to comment!</p>
                    </div>
                @endforelse
            </div>

            <!-- In the comment input section -->
            <div class="p-4 border-t dark:border-zinc-700">
                @if($replyToUsername || $parentCommentId)
                    <div class="mb-2 text-sm">
                        <span class="text-gray-500">Replying to: </span>
                        <span class="text-pink-500">
                            {{'@'. $replyToUsername }}
                        </span>
                        <button wire:click="cancelReply"
                                class="text-xs text-gray-500 hover:text-gray-700 ml-2">
                            cancel
                        </button>
                    </div>
                @endif

                <div class="flex items-center space-x-2">
                    <div class="relative">
                        <button @click="emojiPicker = !emojiPicker"
                                type="button"
                                class="p-2 text-gray-500 hover:text-yellow-500 rounded-full hover:bg-gray-100 dark:hover:bg-zinc-700">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 100-2 1 1 0 000 2zm7-1a1 1 0 11-2 0 1 1 0 012 0zm-3.646 5.854a.5.5 0 01-.708 0l-2-2a.5.5 0 01.708-.708L10 13.793l1.646-1.647a.5.5 0 01.708.708l-2 2z" clip-rule="evenodd"/>
                            </svg>
                        </button>

                        <!-- Emoji Picker Panel -->
                        <div x-show="emojiPicker"
                            @click.away="emojiPicker = false"
                            class="absolute bottom-12 left-0 w-64 p-2 bg-white dark:bg-zinc-800 rounded-lg shadow-xl border dark:border-zinc-700 z-50">
                            <div class="grid grid-cols-8 gap-1">
                                <!-- Common emojis -->
                                <button @click="addEmoji('😊')" class="p-1 hover:bg-gray-100 dark:hover:bg-zinc-700 rounded">😊</button>
                                <button @click="addEmoji('😂')" class="p-1 hover:bg-gray-100 dark:hover:bg-zinc-700 rounded">😂</button>
                                <button @click="addEmoji('❤️')" class="p-1 hover:bg-gray-100 dark:hover:bg-zinc-700 rounded">❤️</button>
                                <button @click="addEmoji('👍')" class="p-1 hover:bg-gray-100 dark:hover:bg-zinc-700 rounded">👍</button>
                                <button @click="addEmoji('🎉')" class="p-1 hover:bg-gray-100 dark:hover:bg-zinc-700 rounded">🎉</button>
                                <button @click="addEmoji('✨')" class="p-1 hover:bg-gray-100 dark:hover:bg-zinc-700 rounded">✨</button>
                                <button @click="addEmoji('🔥')" class="p-1 hover:bg-gray-100 dark:hover:bg-zinc-700 rounded">🔥</button>
                                <button @click="addEmoji('🤔')" class="p-1 hover:bg-gray-100 dark:hover:bg-zinc-700 rounded">🤔</button>
                                <button @click="addEmoji('😎')" class="p-1 hover:bg-gray-100 dark:hover:bg-zinc-700 rounded">😎</button>
                                <button @click="addEmoji('🥰')" class="p-1 hover:bg-gray-100 dark:hover:bg-zinc-700 rounded">🥰</button>
                                <button @click="addEmoji('😍')" class="p-1 hover:bg-gray-100 dark:hover:bg-zinc-700 rounded">😍</button>
                                <button @click="addEmoji('🙌')" class="p-1 hover:bg-gray-100 dark:hover:bg-zinc-700 rounded">🙌</button>
                                <button @click="addEmoji('👌')" class="p-1 hover:bg-gray-100 dark:hover:bg-zinc-700 rounded">👌</button>
                                <button @click="addEmoji('🤗')" class="p-1 hover:bg-gray-100 dark:hover:bg-zinc-700 rounded">🤗</button>
                                <button @click="addEmoji('💫')" class="p-1 hover:bg-gray-100 dark:hover:bg-zinc-700 rounded">💫</button>
                                <button @click="addEmoji('💯')" class="p-1 hover:bg-gray-100 dark:hover:bg-zinc-700 rounded">💯</button>
                            </div>
                        </div>
                    </div>

                    <div class="flex-1 relative">
                        <input type="text"
                            wire:model.live="newComment"
                            wire:keydown.enter="addComment"
                            placeholder="Add a comment..."
                            class="w-full p-3 border-gray-200 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-900 pr-12 focus:ring-pink-500 focus:border-pink-500 text-sm rounded-full">
                        <button wire:click="addComment"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-pink-500 font-semibold text-sm transition-all duration-200"
                                :class="{'opacity-50 cursor-not-allowed': !$wire.newComment, 'hover:text-pink-600': $wire.newComment}"
                                :disabled="!$wire.newComment">
                            {{ $replyToUsername ? 'Reply' : 'Post' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
