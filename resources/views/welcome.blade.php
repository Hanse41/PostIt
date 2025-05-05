<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>PostIt - Share Your Moments</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,800" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased bg-gray-50 dark:bg-gray-900">
        <!-- Navbar -->
        <nav class="fixed top-0 w-full bg-white dark:bg-gray-800 shadow-sm z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <x-app-logo class="h-8 w-8" />
                    </div>

                    <!-- Navigation -->
                    <div class="flex items-center space-x-4">
                        @auth
                            <a href="{{ url('/dashboard') }}"
                               class="inline-flex items-center px-4 py-2 border border-transparent rounded-full text-sm font-medium text-white bg-gradient-to-r from-pink-500 to-orange-500 hover:from-pink-600 hover:to-orange-600">
                                Dashboard
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <div class="relative pt-16 pb-32 flex content-center items-center justify-center min-h-screen">
            <div class="container mx-auto px-4">
                <div class="flex flex-wrap items-center">
                    <!-- Left Column -->
                    <div class="w-full md:w-6/12 px-4 md:px-8 text-center md:text-left">
                        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 dark:text-white mb-6">
                            Share Moments That Matter
                        </h1>
                        <p class="mt-4 text-lg text-gray-600 dark:text-gray-300">
                            Join millions of creators sharing their stories through photos. Connect with friends, discover amazing content, and build your own community.
                        </p>
                        <div class="lg:flex justify-center mt-8 space-x-4">
                            <a href="{{ route('login') }}"
                               class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-full text-white bg-gradient-to-r from-pink-500 to-orange-500 hover:from-pink-600 hover:to-orange-600">
                                Get Started
                            </a>
                            <a href="#features"
                               class="inline-flex items-center px-6 py-3 border-2 border-gray-300 dark:border-gray-700 text-base font-medium rounded-full text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800">
                                Learn More
                            </a>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="w-full md:w-6/12 px-4 mt-12 md:mt-0">
                        <div class="relative">
                            <!-- App Preview Grid -->
                            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-2 md:p-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <!-- Image 1 - Photography -->
                                    <div class="aspect-square rounded-lg overflow-hidden">
                                        <img src="https://images.unsplash.com/photo-1516035069371-29a1b244cc32"
                                            alt="Aesthetic photo 1"
                                            class="w-full h-full object-cover transform hover:scale-105 transition-transform duration-300">
                                    </div>

                                    <!-- Image 2 - People -->
                                    <div class="aspect-square rounded-lg overflow-hidden">
                                        <img src="https://images.unsplash.com/photo-1517841905240-472988babdf9"
                                            alt="Aesthetic photo 2"
                                            class="w-full h-full object-cover transform hover:scale-105 transition-transform duration-300">
                                    </div>

                                    <!-- Image 3 - Nature -->
                                    <div class="aspect-square rounded-lg overflow-hidden">
                                        <img src="https://images.unsplash.com/photo-1433086966358-54859d0ed716"
                                            alt="Aesthetic photo 3"
                                            class="w-full h-full object-cover transform hover:scale-105 transition-transform duration-300">
                                    </div>

                                    <!-- Image 4 - Urban -->
                                    <div class="aspect-square rounded-lg overflow-hidden">
                                        <img src="https://images.unsplash.com/photo-1449034446853-66c86144b0ad"
                                            alt="Aesthetic photo 4"
                                            class="w-full h-full object-cover transform hover:scale-105 transition-transform duration-300">
                                    </div>
                                </div>
                            </div>

                            <!-- Decorative Elements -->
                            <div class="absolute -bottom-6 -right-6 w-32 h-32 bg-gradient-to-br from-pink-500 to-orange-500 rounded-full opacity-20 blur-2xl"></div>
                            <div class="absolute -top-6 -left-6 w-32 h-32 bg-gradient-to-br from-pink-500 to-orange-500 rounded-full opacity-20 blur-2xl"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <div id="features" class="py-24 bg-white dark:bg-gray-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white">
                        Why Choose PostIt?
                    </h2>
                    <p class="mt-4 text-gray-600 dark:text-gray-400">
                        Everything you need to share your story with the world
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div class="p-6 border border-gray-200 dark:border-gray-700 rounded-xl">
                        <div class="w-12 h-12 bg-gradient-to-br from-pink-500 to-orange-500 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Beautiful Filters</h3>
                        <p class="mt-2 text-gray-600 dark:text-gray-400">Make your photos stand out with our collection of custom filters.</p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="p-6 border border-gray-200 dark:border-gray-700 rounded-xl">
                        <div class="w-12 h-12 bg-gradient-to-br from-pink-500 to-orange-500 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Community First</h3>
                        <p class="mt-2 text-gray-600 dark:text-gray-400">Connect with like-minded people and build your following.</p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="p-6 border border-gray-200 dark:border-gray-700 rounded-xl">
                        <div class="w-12 h-12 bg-gradient-to-br from-pink-500 to-orange-500 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Instant Sharing</h3>
                        <p class="mt-2 text-gray-600 dark:text-gray-400">Share your moments instantly with your followers.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
            <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center">
                    <p class="text-gray-500 dark:text-gray-400">
                        © {{ date('Y') }} PostIt. All rights reserved.
                    </p>
                    <div class="flex space-x-6">
                        <a href="#" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                            <span class="sr-only">Instagram</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </footer>
    </body>
</html>
