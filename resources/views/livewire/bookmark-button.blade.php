<div class="flex items-center group" x-data>
    <button wire:click="toggleBookmark"
            class="text-gray-500 hover:text-yellow-500 transition-colors focus:outline-none"
            wire:loading.class="opacity-50"
            wire:loading.attr="disabled">
        <svg xmlns="http://www.w3.org/2000/svg"
             class="w-7 h-7"
             :class="{
                'fill-yellow-400 stroke-yellow-500': $wire.isBookmarked,
                'fill-none stroke-current': !$wire.isBookmarked
             }"
             viewBox="0 0 24 24"
             stroke-width="2"
             stroke-linecap="round"
             stroke-linejoin="round">
            <path d="m19 21-7-4-7 4V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16z"/>
        </svg>
    </button>
</div>
