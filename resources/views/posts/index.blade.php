
<x-layouts.app :title="__('Posts')">
    <!-- Required Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js" type="module"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Alert Handling -->
    <script>
        @if (session('success'))
            Swal.fire({
                title: 'Success!',
                text: '{{ session('success') }}',
                icon: 'success',
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'bg-pink-500 hover:bg-pink-600 text-white px-4 py-2 rounded-lg'
                }
            });
        @endif

        @if (session('error'))
            Swal.fire({
                title: 'Error!',
                text: '{{ session('error') }}',
                icon: 'error',
                confirmButtonText: 'OK',
                customClass: {
                    confirmButton: 'bg-pink-500 hover:bg-pink-600 text-white px-4 py-2 rounded-lg'
                }
            });
        @endif
    </script>

    <!-- Main Content -->
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row justify-between items-center mb-8 gap-4">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Your Feed</h1>

            <!-- Sort Options -->
            <form action="{{ route('posts.index') }}" method="GET"
                  class="w-full sm:w-auto">
                <select name="sort"
                        onchange="this.form.submit()"
                        class="w-full sm:w-48 p-2.5 text-sm rounded-lg border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-colors duration-200">
                    <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Latest Posts</option>
                    <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Oldest Posts</option>
                    <option value="my_posts" {{ request('sort') === 'my_posts' ? 'selected' : '' }}>My Posts</option>
                </select>
            </form>
        </div>

        <!-- Posts Feed -->
        <div class="grid grid-cols-1 gap-6">
            @foreach($posts as $post)
                <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm overflow-hidden border border-gray-100 dark:border-zinc-700 hover:shadow-md transition-shadow duration-200">
                    <!-- Post Header -->
                    <div class="p-4 flex items-center justify-between border-b dark:border-zinc-700">
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('profile.show', $post->user) }}"
                               class="group">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-pink-500 via-red-500 to-orange-500 p-[2px] group-hover:from-pink-600 group-hover:via-red-600 group-hover:to-orange-600 transition">
                                    <img src="{{ $post->user->avatar }}"
                                         alt="{{ $post->user->name }}"
                                         class="w-full h-full rounded-full object-cover">
                                </div>
                            </a>
                            <div>
                                <a href="{{ route('profile.show', $post->user) }}"
                                   class="font-semibold text-sm text-gray-900 dark:text-white hover:underline">
                                    {{ $post->user->name }}
                                </a>
                                @if($post->created_at)
                                    <p class="text-xs text-gray-500">{{ $post->created_at->diffForHumans() }}</p>
                                @endif
                            </div>
                        </div>

                        @if($post->user_id === auth()->id())
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open"
                                        class="p-1 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 rounded-full hover:bg-gray-100 dark:hover:bg-zinc-700 transition-colors duration-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                    </svg>
                                </button>

                                <div x-show="open"
                                     @click.away="open = false"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     class="absolute right-0 mt-2 w-48 bg-white dark:bg-zinc-700 rounded-lg shadow-lg py-1 z-50">
                                    <a href="{{ route('posts.edit', $post) }}"
                                       class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-zinc-600">
                                        Edit Post
                                    </a>
                                    <form action="{{ route('posts.destroy', $post) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 dark:hover:bg-zinc-600">
                                            Delete Post
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Post Media -->
                    @include('posts.partials.media-carousel', ['post' => $post])

                    <!-- Post Actions -->
                    <div class="p-4">
                        <div class="flex justify-between items-center mb-4">
                            <div class="flex items-center space-x-4">
                                <livewire:like-button :post="$post" :wire:key="'like-'.$post->id" />
                                <livewire:comment-modal :post="$post" :wire:key="'comment-modal-'.$post->id" />
                                <button class="p-2 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 rounded-full hover:bg-gray-100 dark:hover:bg-zinc-700 transition-colors duration-200">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                                    </svg>
                                </button>
                            </div>
                            <livewire:bookmark-button :post="$post" :wire:key="'bookmark-'.$post->id" />
                        </div>

                        <!-- Caption and Tags -->
                        @if($post->caption)
                            <p class="text-sm text-gray-900 dark:text-gray-100 mb-2">
                                <span class="font-semibold">{{ $post->user->name }}</span>
                                <span class="ml-1">{{ $post->caption }}</span>
                            </p>
                        @endif

                        @if($post->tags)
                            <div class="flex flex-wrap gap-2 mb-4">
                                @foreach(json_decode($post->tags, true) as $tag)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-200">
                                        #{{ $tag }}
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        <!-- Comments Section -->
                        <div class="pt-4 border-t dark:border-zinc-700">
                            <livewire:comment-section :post="$post" :wire:key="'comment-'.$post->id" />
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $posts->links() }}
        </div>

        <!-- Create Post Button -->
        <div class="fixed bottom-6 right-6 z-50">
            <a href="{{ route('posts.create') }}"
               class="flex items-center space-x-2 px-6 py-3 bg-gradient-to-r from-pink-500 to-orange-500 text-white rounded-full shadow-lg hover:from-pink-600 hover:to-orange-600 transition-all duration-200 transform hover:scale-105">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span class="font-medium">Create Post</span>
            </a>
        </div>
    </div>

    <!-- Emoji Picker Script -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelector('emoji-picker')
                ?.addEventListener('emoji-click', event => {
                    const input = event.target.closest('.relative').querySelector('input');
                    const startPos = input.selectionStart;
                    const endPos = input.selectionEnd;
                    const value = input.value;

                    input.value = value.substring(0, startPos) + event.detail.unicode + value.substring(endPos);
                    input.dispatchEvent(new Event('input'));
                    input.focus();
                });
        });
    </script>
</x-layouts.app>

<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelector('emoji-picker')
        ?.addEventListener('emoji-click', event => {
            const input = event.target.closest('.relative').querySelector('input');
            const startPos = input.selectionStart;
            const endPos = input.selectionEnd;
            const value = input.value;

            input.value = value.substring(0, startPos) + event.detail.unicode + value.substring(endPos);
            input.dispatchEvent(new Event('input'));
            input.focus();
        });
});
</script>
