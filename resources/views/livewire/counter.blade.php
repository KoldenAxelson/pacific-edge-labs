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
