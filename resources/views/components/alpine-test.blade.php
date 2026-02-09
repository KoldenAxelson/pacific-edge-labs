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
