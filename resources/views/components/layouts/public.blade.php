<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <!-- Public Header -->
        <header class="bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <a href="{{ route('home') }}" class="flex items-center space-x-2" wire:navigate>
                            <x-app-logo />
                        </a>
                    </div>

                    <!-- Navigation -->
                    <div class="flex items-center space-x-4">
                        @auth
                            <a href="{{ route('dashboard') }}"
                                class="text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white px-3 py-2 rounded-md text-sm font-medium transition-colors"
                                wire:navigate>
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                                class="text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white px-3 py-2 rounded-md text-sm font-medium transition-colors"
                                wire:navigate>
                                Login
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="min-h-screen">
            {{ $slot }}
        </main>

        @fluxScripts
    </body>
</html>
