# [TASK-0-003] Tailwind + Alpine + Livewire Configuration

## Overview
Configure and optimize the TALL stack (Tailwind, Alpine, Livewire, Laravel) frontend. Breeze already installed Tailwind and Alpine, but we need to customize the configuration and add Livewire for reactive components.

## Prerequisites
- [x] TASK-0-002 completed (Breeze installed with Tailwind + Alpine)
- [x] NPM dev server running (`sail npm run dev`)

## Goals
- Customize Tailwind configuration for Pacific Edge Labs branding
- Verify Alpine.js is working correctly
- Install and configure Livewire 3
- Create a test Livewire component
- Set up production build optimization

## Step-by-Step Instructions

### 1. Install Livewire 3

```bash
sail composer require livewire/livewire
```

### 2. Publish Livewire Configuration

```bash
sail artisan livewire:publish --config
```

This creates `config/livewire.php`.

### 3. Customize Tailwind Configuration

Edit `tailwind.config.js`:

```javascript
import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './app/Livewire/**/*.php', // Add Livewire components
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Pacific Edge Labs brand colors (will be refined in Phase 1)
                'pel-blue': {
                    50: '#eff6ff',
                    100: '#dbeafe',
                    200: '#bfdbfe',
                    300: '#93c5fd',
                    400: '#60a5fa',
                    500: '#3b82f6', // Primary
                    600: '#2563eb',
                    700: '#1d4ed8',
                    800: '#1e40af',
                    900: '#1e3a8a',
                    950: '#172554',
                },
                'pel-gray': {
                    50: '#f9fafb',
                    100: '#f3f4f6',
                    200: '#e5e7eb',
                    300: '#d1d5db',
                    400: '#9ca3af',
                    500: '#6b7280',
                    600: '#4b5563',
                    700: '#374151',
                    800: '#1f2937',
                    900: '#111827',
                    950: '#030712',
                },
            },
        },
    },

    plugins: [forms],
};
```

### 4. Update Livewire Configuration

Edit `config/livewire.php`:

```php
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Class Namespace
    |--------------------------------------------------------------------------
    */
    'class_namespace' => 'App\\Livewire',

    /*
    |--------------------------------------------------------------------------
    | View Path
    |--------------------------------------------------------------------------
    */
    'view_path' => resource_path('views/livewire'),

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    | Default layout for Livewire components
    */
    'layout' => 'layouts.app',

    /*
    |--------------------------------------------------------------------------
    | Lazy Loading Placeholder
    |--------------------------------------------------------------------------
    */
    'lazy_placeholder' => null,

    /*
    |--------------------------------------------------------------------------
    | Temporary File Uploads
    |--------------------------------------------------------------------------
    */
    'temporary_file_upload' => [
        'disk' => 'local',
        'rules' => null,
        'directory' => 'livewire-tmp',
        'middleware' => null,
        'preview_mimes' => [
            'png', 'gif', 'bmp', 'svg', 'wav', 'mp4',
            'mov', 'avi', 'wmv', 'mp3', 'm4a',
            'jpg', 'jpeg', 'mpga', 'webp', 'wma',
        ],
        'max_upload_time' => 5,
    ],

    /*
    |--------------------------------------------------------------------------
    | Render On Redirect
    |--------------------------------------------------------------------------
    */
    'render_on_redirect' => false,

    /*
    |--------------------------------------------------------------------------
    | Eloquent Model Binding
    |--------------------------------------------------------------------------
    */
    'legacy_model_binding' => false,

    /*
    |--------------------------------------------------------------------------
    | Auto Inject Assets
    |--------------------------------------------------------------------------
    */
    'inject_assets' => true,

    /*
    |--------------------------------------------------------------------------
    | Navigate (SPA mode)
    |--------------------------------------------------------------------------
    */
    'navigate' => [
        'show_progress_bar' => true,
        'progress_bar_color' => '#3b82f6', // pel-blue-500
    ],

    /*
    |--------------------------------------------------------------------------
    | HTML Minification
    |--------------------------------------------------------------------------
    */
    'minify_html' => false,

];
```

### 5. Create Test Livewire Component

```bash
sail artisan make:livewire Counter
```

This creates:
- `app/Livewire/Counter.php`
- `resources/views/livewire/counter.blade.php`

Edit `app/Livewire/Counter.php`:

```php
<?php

namespace App\Livewire;

use Livewire\Component;

class Counter extends Component
{
    public $count = 0;

    public function increment()
    {
        $this->count++;
    }

    public function decrement()
    {
        $this->count--;
    }

    public function render()
    {
        return view('livewire.counter');
    }
}
```

Edit `resources/views/livewire/counter.blade.php`:

```blade
<div class="p-6 bg-white rounded-lg shadow-lg">
    <h2 class="text-2xl font-bold text-gray-900 mb-4">Livewire Counter Test</h2>
    
    <div class="flex items-center space-x-4">
        <button 
            wire:click="decrement" 
            class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition"
        >
            -
        </button>
        
        <span class="text-4xl font-bold text-pel-blue-600">{{ $count }}</span>
        
        <button 
            wire:click="increment" 
            class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition"
        >
            +
        </button>
    </div>
    
    <p class="mt-4 text-sm text-gray-600">
        This is a Livewire component. Clicks update without page reload!
    </p>
</div>
```

### 6. Create Alpine.js Test Component

Create `resources/views/components/alpine-test.blade.php`:

```blade
<div 
    x-data="{ open: false, message: 'Hello from Alpine.js!' }" 
    class="p-6 bg-white rounded-lg shadow-lg"
>
    <h2 class="text-2xl font-bold text-gray-900 mb-4">Alpine.js Test</h2>
    
    <button 
        @click="open = !open" 
        class="px-4 py-2 bg-pel-blue-500 text-white rounded hover:bg-pel-blue-600 transition"
    >
        <span x-text="open ? 'Hide Message' : 'Show Message'"></span>
    </button>
    
    <div 
        x-show="open" 
        x-transition
        class="mt-4 p-4 bg-pel-blue-50 rounded border border-pel-blue-200"
    >
        <p class="text-pel-blue-900 font-medium" x-text="message"></p>
    </div>
</div>
```

### 7. Create Test Page Route

Edit `routes/web.php`:

```php
<?php

use App\Livewire\Counter;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Test page for Tailwind, Alpine, Livewire
Route::get('/test-components', function () {
    return view('test-components');
})->name('test-components');

require __DIR__.'/auth.php';
```

### 8. Create Test Components View

Create `resources/views/test-components.blade.php`:

```blade
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
```

### 9. Test Components in Browser

Visit: http://localhost/test-components

You should see:
1. **Tailwind CSS** - Colored boxes with brand colors
2. **Alpine.js** - Toggle button that shows/hides message with smooth transition
3. **Livewire** - Counter that increments/decrements without page reload

### 10. Optimize Vite Configuration

Edit `vite.config.js`:

```javascript
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: [
                'app/Livewire/**',
                'resources/views/**',
            ],
        }),
    ],
    server: {
        hmr: {
            host: 'localhost',
        },
    },
});
```

### 11. Create Production Build Script

Add to `package.json` scripts:

```json
{
  "scripts": {
    "dev": "vite",
    "build": "vite build",
    "preview": "vite preview",
    "build:production": "vite build --mode production"
  }
}
```

### 12. Test Production Build

```bash
sail npm run build
```

This should create optimized, minified assets in `public/build/`.

### 13. Add Custom CSS (Optional)

Edit `resources/css/app.css`:

```css
@tailwind base;
@tailwind components;
@tailwind utilities;

/* Custom Pacific Edge Labs styles */
@layer components {
    .btn-primary {
        @apply px-4 py-2 bg-pel-blue-500 text-white rounded-lg hover:bg-pel-blue-600 transition duration-150 ease-in-out;
    }
    
    .btn-secondary {
        @apply px-4 py-2 bg-pel-gray-200 text-pel-gray-700 rounded-lg hover:bg-pel-gray-300 transition duration-150 ease-in-out;
    }
    
    .card {
        @apply bg-white rounded-lg shadow-md overflow-hidden;
    }
    
    .input-text {
        @apply w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pel-blue-500 focus:border-transparent;
    }
}

/* Livewire loading states */
[wire\:loading] {
    opacity: 0.5;
    pointer-events: none;
}

/* Alpine.js transitions */
[x-cloak] {
    display: none !important;
}
```

### 14. Restart Vite Dev Server

In the terminal running `sail npm run dev`, press `Ctrl+C` then:

```bash
sail npm run dev
```

### 15. Commit Changes

```bash
git add .
git commit -m "Configure TALL stack: Tailwind customization, Alpine.js, Livewire 3"
git push
```

## Validation Checklist

- [ ] http://localhost/test-components loads without errors
- [ ] Custom Tailwind colors (pel-blue, pel-gray) render correctly
- [ ] Alpine.js toggle button shows/hides message smoothly
- [ ] Livewire counter increments/decrements without page reload
- [ ] Browser console has no errors (check DevTools)
- [ ] Vite dev server hot-reloads on file changes
- [ ] `sail npm run build` completes successfully
- [ ] Custom CSS classes (btn-primary, card) work correctly
- [ ] Page is responsive (test mobile view in DevTools)

## Common Issues & Solutions

### Issue: Livewire styles not loading
**Solution:**
Make sure `@livewireStyles` and `@livewireScripts` are in your layout (Breeze includes this by default).

### Issue: Alpine.js not working
**Solution:**
```bash
sail npm install
sail npm run dev
```

Check that `resources/js/app.js` includes Alpine:
```javascript
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();
```

### Issue: Tailwind colors not showing
**Solution:**
Clear Tailwind cache:
```bash
rm -rf public/build
sail npm run dev
```

### Issue: "Vite manifest not found" in production
**Solution:**
Always run `sail npm run build` before deploying to production.

### Issue: Hot reload not working
**Solution:**
Check `vite.config.js` HMR settings and restart dev server.

## Component Architecture Reference

### Livewire Components
**Location:** `app/Livewire/`  
**Views:** `resources/views/livewire/`  
**Usage in Blade:** `<livewire:component-name />`

**Best Practices:**
- Use Livewire for interactive components with server state
- Keep components focused and single-responsibility
- Use wire:model for two-way binding
- Leverage wire:loading for better UX

### Alpine.js Components
**Location:** Inline in Blade views or `resources/views/components/`  
**Usage:** `x-data`, `x-show`, `x-on`, etc.

**Best Practices:**
- Use Alpine for simple client-side interactions
- No server round-trip needed
- Great for toggles, modals, dropdowns
- Combine with Livewire when server state is needed

### When to Use Which?
- **Livewire:** Shopping cart, user profile, product filtering, checkout forms
- **Alpine.js:** Mobile menu toggle, modal open/close, form validation feedback, image galleries
- **Both:** Complex product configurator (Alpine for UI, Livewire for price calculation)

## Performance Optimization Tips

1. **Lazy Load Livewire Components:**
```blade
<livewire:heavy-component lazy />
```

2. **Defer Livewire Loading:**
```blade
<livewire:non-critical-component wire:init="loadData" />
```

3. **Use Alpine for UI-only Interactions:**
Don't use Livewire if you don't need server state.

4. **Optimize Tailwind for Production:**
The `build` command automatically purges unused CSS.

5. **Enable Livewire Polling Sparingly:**
Only poll when absolutely necessary (e.g., real-time order status).

## Next Steps

Once all validation items pass:
- ✅ Mark TASK-0-003 as complete
- ➡️ Proceed to TASK-0-004 (S3 Bucket Configuration)

## Time Estimate
**30-45 minutes**

## Success Criteria
- Livewire 3 installed and working
- Alpine.js verified and functional
- Custom Tailwind colors configured
- Test components page showing all three technologies
- Production build compiles successfully
- All changes committed and pushed to GitHub

---
**Phase:** 0 (Environment & Foundation)  
**Dependencies:** TASK-0-002  
**Blocks:** TASK-0-005 (Filament uses Livewire)  
**Priority:** Critical
