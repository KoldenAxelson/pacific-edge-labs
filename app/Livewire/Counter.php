<?php

namespace App\Livewire;

use Livewire\Component;

/**
 * Test/demo Livewire component for verifying the TALL stack setup.
 *
 * Provides a simple counter with increment and decrement functionality
 * to verify that Tailwind, Alpine, Laravel, and Livewire are properly integrated.
 */
class Counter extends Component
{
    public $count = 0;

    /**
     * Increment the counter value by one.
     */
    public function increment()
    {
        $this->count++;
    }

    /**
     * Decrement the counter value by one.
     */
    public function decrement()
    {
        $this->count--;
    }

    /**
     * Render the component with the counter view.
     */
    public function render()
    {
        return view('livewire.counter');
    }
}
