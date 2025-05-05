<x-layouts.app :title="__('PostIt')">
    <!-- Main Feed Container -->
    <div class="max-w-4xl mx-auto px-4 py-8">
        <!-- Stories Section (Optional) -->
        <div class="mb-8 overflow-x-auto">
            <div class="flex space-x-4 p-2">
                @for ($i = 1; $i <= 10; $i++)
                    <div class="flex-shrink-0">
                        <div class="w-16 h-16 rounded-full ring-2 ring-pink-500 p-1">
                            <div class="bg-zinc-200 w-full h-full rounded-full"></div>
                        </div>
                        <p class="text-xs text-center mt-1 text-gray-600">User {{$i}}</p>
                    </div>
                @endfor
            </div>
        </div>

        @php
            header("Location: " . route('posts.index'));
            exit();
        @endphp

        <!-- Load More -->
        <div class="mt-8 text-center">
            <button class="px-6 py-3 bg-gradient-to-r from-pink-500 to-orange-500 text-white rounded-full hover:from-pink-600 hover:to-orange-600 transition-all duration-200">
                Load More
            </button>
        </div>
    </div>

    <!-- Right Sidebar (Optional) - For larger screens -->
    <div class="hidden lg:block fixed right-0 top-20 w-80 p-6">
        <!-- User Suggestions -->
        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm p-4">
            <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Suggested for you</h3>
            @foreach(range(1, 5) as $suggestion)
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full bg-zinc-200 mr-3"></div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">user_{{$suggestion}}</p>
                            <p class="text-xs text-gray-500">Suggested for you</p>
                        </div>
                    </div>
                    <button class="text-pink-500 text-sm font-semibold">Follow</button>
                </div>
            @endforeach
        </div>
    </div>
</x-layouts.app>
