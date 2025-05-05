<x-layouts.app :title="$user->name">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Enhanced Profile Header Card -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-sm overflow-hidden">
            <!-- Cover Photo -->
            <div class="h-32 sm:h-48 md:h-64 bg-gradient-to-r from-pink-500 via-red-500 to-orange-500"></div>

            <div class="p-6">
                <div class="flex flex-col md:flex-row items-center md:items-start space-y-6 md:space-y-0 md:space-x-8">
                    <!-- Enhanced Avatar with Status -->
                    <div class="relative -mt-20 md:-mt-24">
                        <div class="w-32 h-32 md:w-40 md:h-40 rounded-full bg-gradient-to-br from-pink-500 via-red-500 to-orange-500 p-[3px] shadow-xl ring-4 ring-white dark:ring-zinc-900">
                            <img src="{{ $user->avatar }}"
                                 alt="{{ $user->name }}"
                                 class="w-full h-full rounded-full object-cover">
                        </div>
                        <div class="absolute bottom-2 right-2 w-4 h-4 rounded-full bg-green-500 ring-2 ring-white dark:ring-zinc-900"></div>
                    </div>

                    <!-- Enhanced Profile Info -->
                    <div class="flex-1 w-full md:w-auto">
                        <!-- Name and Actions -->
                        <div class="flex flex-col sm:flex-row items-center justify-between mb-6">
                            <div class="text-center sm:text-left mb-4 sm:mb-0">
                                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">
                                    {{ $user->name }}
                                    @if($user->verified)
                                        <span class="inline-block align-middle ml-2">
                                            <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                        </span>
                                    @endif
                                </h1>
                                @if($user->occupation)
                                    <p class="text-gray-600 dark:text-gray-400 text-sm md:text-base font-medium">
                                        {{ $user->occupation }}
                                    </p>
                                @endif
                            </div>

                            <!-- Enhanced Action Buttons -->
                            <div class="flex items-center space-x-3">
                                @if(auth()->id() === $user->id)
                                    <a href="{{ route('settings.profile') }}"
                                       class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-zinc-800 rounded-lg text-sm font-medium hover:bg-gray-200 dark:hover:bg-zinc-700 transition-all duration-200 transform hover:scale-105">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Edit Profile
                                    </a>
                                @else
                                        <button class="inline-flex items-center px-6 py-2 bg-gradient-to-r from-pink-500 to-orange-500 text-white rounded-lg text-sm font-medium hover:from-pink-600 hover:to-orange-600 transition-all duration-200 transform hover:scale-105 hover:shadow-lg">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                            Follow
                                        </button>
                                        <button class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-zinc-800 rounded-lg text-sm font-medium hover:bg-gray-200 dark:hover:bg-zinc-700 transition-all duration-200 transform hover:scale-105">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                            </svg>
                                            Message
                                        </button>

                                @endif
                            </div>
                        </div>

                        <!-- Enhanced Stats -->
                        <div class="flex justify-center sm:justify-start space-x-8 mb-6">
                            @foreach([
                                'posts' => $user->posts()->count(),
                                'followers' => random_int(1, 100),
                                'following' => random_int(1, 100)
                            ] as $label => $count)
                                <button class="text-center sm:text-left group">
                                    <span class="block font-bold text-xl text-gray-900 dark:text-white group-hover:text-pink-500 transition-colors">
                                        {{ number_format($count) }}
                                    </span>
                                    <span class="text-gray-600 dark:text-gray-400 text-sm group-hover:text-pink-500 transition-colors">
                                        {{ Str::title($label) }}
                                    </span>
                                </button>
                            @endforeach
                        </div>

                        <!-- Enhanced Bio and Details -->
                        <div class="space-y-4 text-center sm:text-left max-w-2xl">
                            @if($user->bio)
                                <p class="text-gray-900 dark:text-white leading-relaxed">
                                    {{ $user->bio }}
                                </p>
                            @endif

                            <!-- Enhanced Additional Info -->
                            <div class="flex flex-wrap gap-4">
                                @if($user->location)
                                    <div class="flex items-center text-gray-600 dark:text-gray-400 hover:text-pink-500 dark:hover:text-pink-400 transition-colors">
                                        <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        </svg>
                                        <span>{{ $user->location }}</span>
                                    </div>
                                @endif
                                @if($user->website)
                                    <a href="{{ $user->website }}"
                                       target="_blank"
                                       class="flex items-center text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors">
                                        <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                        </svg>
                                        <span class="truncate">{{ str_replace(['https://', 'http://'], '', $user->website) }}</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Posts Navigation and Controls -->
        <div class="mt-8">
            <livewire:profile.content-tabs :user="$user" />
        </div>
    </div>
</x-layouts.app>
