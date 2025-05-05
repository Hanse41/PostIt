<div class="relative aspect-square bg-gray-100 dark:bg-zinc-900"
     x-data="{
        currentSlide: 0,
        isLoading: true,
        init() {
            this.$watch('currentSlide', () => {
                // Only show loading for a brief moment during transition
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
    <div class="relative w-full pb-[100%]">
        <div class="absolute inset-0">
            @foreach($mediaItems as $index => $media)
                <div x-show="currentSlide === {{ $index }}"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform scale-95"
                     x-transition:enter-end="opacity-100 transform scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 transform scale-100"
                     x-transition:leave-end="opacity-0 transform scale-95"
                     class="absolute inset-0 flex items-center justify-center bg-black">
                    @if($media->media_type === 'video')
                        <video class="absolute inset-0 w-full h-full object-contain"
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
                             class="absolute inset-0 w-full h-full object-contain"
                             @load="isLoading = false"
                             loading="lazy"
                             onerror="this.onerror=null; this.src='{{ asset('images/placeholder.png') }}';">
                    @endif
                </div>
            @endforeach
        </div>
    </div>


    @if($mediaCount > 1)
        <!-- Navigation Controls (Always visible but conditionally enabled) -->
        <div class="absolute inset-0 flex items-center justify-between p-2">
            <!-- Previous Button -->
            <button @click="currentSlide = (currentSlide - 1 + {{ $mediaCount }}) % {{ $mediaCount }}"
                    :disabled="isLoading"
                    class="p-2 rounded-full bg-black/50 text-white hover:bg-black/75 transition-all duration-200 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed"
                    :class="{ 'hover:bg-black/75': !isLoading, 'cursor-not-allowed': isLoading }">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>

            <!-- Next Button -->
            <button @click="currentSlide = (currentSlide + 1) % {{ $mediaCount }}"
                    :disabled="isLoading"
                    class="p-2 rounded-full bg-black/50 text-white hover:bg-black/75 transition-all duration-200 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed"
                    :class="{ 'hover:bg-black/75': !isLoading, 'cursor-not-allowed': isLoading }">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </div>

        <!-- Slide Indicators -->
        <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2 z-20">
            @foreach($mediaItems as $index => $media)
                <button @click="currentSlide = {{ $index }}"
                        :disabled="isLoading"
                        class="w-2.5 h-2.5 rounded-full transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                        :class="{
                            'bg-pink-500 scale-100': currentSlide === {{ $index }},
                            'bg-white/50 hover:bg-white/75 scale-90': currentSlide !== {{ $index }}
                        }">
                </button>
            @endforeach
        </div>

        <!-- Slide Counter -->
        <div class="absolute top-4 right-4 px-2.5 py-1 rounded-md bg-black/50 text-white text-sm font-medium">
            <span x-text="currentSlide + 1"></span>/<span>{{ $mediaCount }}</span>
        </div>
    @endif

    <!-- Keyboard Navigation -->
    <div x-effect="
        function handleKeydown(e) {
            if (e.key === 'ArrowLeft') {
                currentSlide = (currentSlide - 1 + {{ $mediaCount }}) % {{ $mediaCount }};
            } else if (e.key === 'ArrowRight') {
                currentSlide = (currentSlide + 1) % {{ $mediaCount }};
            }
        }
        window.addEventListener('keydown', handleKeydown);
        return () => window.removeEventListener('keydown', handleKeydown);
    "></div>
</div>

<style>
.media-fit{
    max-height: min(600px, 100vw);
}
</style>
