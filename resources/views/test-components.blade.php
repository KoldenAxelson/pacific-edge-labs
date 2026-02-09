<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Component Tests - Pacific Edge Labs</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gray-50">
    <div class="min-h-screen py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-8">
                TALL Stack Component Tests
            </h1>
            
            <!-- Tailwind Test -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Tailwind CSS</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="p-4 bg-pel-blue-500 text-white rounded-lg">
                        Primary Blue
                    </div>
                    <div class="p-4 bg-pel-blue-600 text-white rounded-lg">
                        Darker Blue
                    </div>
                    <div class="p-4 bg-pel-gray-700 text-white rounded-lg">
                        Brand Gray
                    </div>
                </div>
            </div>
            
            <!-- Alpine.js Test -->
            <div class="mb-8">
                <x-alpine-test />
            </div>
            
            <!-- Livewire Test -->
            <div class="mb-8">
                <livewire:counter />
            </div>
            
            <!-- Back Link -->
            <div class="mt-8">
                <a href="/" class="text-pel-blue-600 hover:text-pel-blue-800 font-medium">
                    ← Back to Home
                </a>
            </div>
        </div>
    </div>
</body>
</html>
