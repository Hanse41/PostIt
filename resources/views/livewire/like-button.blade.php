<div class="flex items-center group" x-data>
    <button wire:click="toggleLike"
            class="inline-flex items-center justify-center p-1.5 text-gray-500 hover:text-pink-500 transition-colors hover:cursor-pointer focus:outline-none"
            wire:loading.class="opacity-50"
            wire:loading.attr="disabled">
        <svg class="w-7 h-7"
             :class="{
                'fill-pink-500 stroke-pink-500': $wire.isLiked,
                'fill-none stroke-current group-hover:stroke-pink-500': !$wire.isLiked
             }"
             stroke-width="2"
             viewBox="0 0 24 24">
            <path stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
        </svg>
        <span class="ml-1.5 text-sm font-medium" :class="{
            'text-pink-500': $wire.isLiked
            }">
            {{ $likesCount }}
        </span>
    </button>
</div>
