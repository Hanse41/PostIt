<div class="px-4 sm:px-6 py-4 bg-white dark:bg-zinc-800"
     x-data="{
        emojiPicker: false,
        addEmoji(emoji) {
            @this.addEmoji(emoji);
            this.emojiPicker = false;
        }
     }">
    <!-- Reply Info -->
    @if($replyToUsername)
        <div class="mb-3 flex items-center text-sm">
            <span class="text-gray-500 dark:text-gray-400">Replying to</span>
            <span class="ml-1 text-pink-500 font-medium">{{'@'. $replyToUsername }}</span>
            <button wire:click="cancelReply"
                    class="ml-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                cancel
            </button>
        </div>
    @endif

    <!-- Input Section -->
    <div class="flex items-center space-x-2 sm:space-x-3">
        <!-- Emoji Button -->
        <div class="relative">
            <button @click="emojiPicker = !emojiPicker"
                    type="button"
                    class="p-2 text-gray-500 hover:text-yellow-500 rounded-full hover:bg-gray-100 dark:hover:bg-zinc-700 transition-colors">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 100-2 1 1 0 000 2zm7-1a1 1 0 11-2 0 1 1 0 012 0zm-.464 5.535a1 1 0 10-1.415-1.414 3 3 0 01-4.242 0 1 1 0 00-1.415 1.414 5 5 0 007.072 0z"/>
                </svg>
            </button>

            <!-- Emoji Picker Dropdown -->
            <div x-show="emojiPicker"
                 @click.away="emojiPicker = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 class="absolute bottom-12 left-0 sm:left-auto sm:right-0 w-64 p-3 bg-white dark:bg-zinc-800 rounded-lg shadow-xl border dark:border-zinc-700 z-50">
                <div class="grid grid-cols-8 gap-1">
                    @foreach(['😊', '😂', '❤️', '👍', '🎉', '✨', '🔥', '🤔', '😎', '🥰', '😍', '🙌', '👌', '🤗', '💫', '💯'] as $emoji)
                        <button @click="addEmoji('{{ $emoji }}')"
                                class="p-1.5 text-xl hover:bg-gray-100 dark:hover:bg-zinc-700 rounded-lg transition-colors">
                            {{ $emoji }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Comment Input -->
        <div class="flex-1">
            <input type="text"
                   wire:model.live="newComment"
                   wire:keydown.enter="addComment"
                   placeholder="{{ $replyToUsername ? 'Write a reply...' : 'Add a comment...' }}"
                   class="w-full px-4 py-2.5 text-sm sm:text-base bg-gray-100 dark:bg-zinc-900 border-0 rounded-full focus:ring-2 focus:ring-pink-500 placeholder-gray-500 dark:placeholder-gray-400">
        </div>

        <!-- Post Button -->
        <button wire:click="addComment"
                class="px-4 sm:px-6 py-2 text-sm font-medium text-white bg-pink-500 rounded-full hover:bg-pink-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                :disabled="!$wire.newComment">
            {{ $replyToUsername ? 'Reply' : 'Post' }}
        </button>
    </div>
</div>
