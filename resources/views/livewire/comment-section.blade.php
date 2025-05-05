<div class="space-y-3">
    @if($post->comments_count > 0)
        <div class="mt-2">
            <div class="space-y-2 mt-2">
                @foreach($comments->take(1) as $comment)
                    <div class="group flex items-start space-x-2">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-pink-500 via-red-500 to-orange-500 p-[2px]">
                            <img src="{{ $comment->user->avatar }}"
                                 alt="{{ $comment->user->name }}"
                                 class="w-full h-full rounded-full object-cover">
                        </div>
                        <div class="flex-1 bg-gray-100 dark:bg-zinc-900 rounded-2xl px-4 py-2 relative group">
                            <div class="gap-2 flex flex-col">
                                <div class="flex items-center justify-between sm:flex flex-col">
                                    <a href="{{ route('profile.show', $comment->user) }}" class="font-semibold text-sm hover:underline">
                                        {{ $comment->user->name }}
                                    </a>
                                    <span class="text-xs text-gray-500 mt-1">
                                        {{ $comment->created_at }}
                                    </span>
                                </div>
                                <p class="text-sm">{{ $comment->body }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>
