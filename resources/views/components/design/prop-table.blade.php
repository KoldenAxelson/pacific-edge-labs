@props([
    'props' => [],  // array of ['name', 'type', 'default', 'description']
])

<div x-data="{ open: false }">

    {{-- Toggle button --}}
    <button
        type="button"
        @click="open = !open"
        class="inline-flex items-center gap-1.5 text-body-sm text-brand-text-muted hover:text-brand-navy transition-smooth focus:outline-none"
    >
        <x-heroicon-o-table-cells class="w-4 h-4 shrink-0" aria-hidden="true" />
        <span x-text="open ? 'Hide props' : 'Show props'">Show props</span>
        <span :class="open ? 'rotate-180' : ''" class="transition-smooth inline-flex" aria-hidden="true">
            <x-heroicon-o-chevron-down class="w-3.5 h-3.5" />
        </span>
    </button>

    {{-- Collapsible prop table --}}
    <div x-show="open" x-collapse x-collapse.duration.300ms class="mt-2">
        <div class="rounded-lg overflow-hidden border border-brand-border">
            <table class="w-full text-body-sm">

                <thead>
                    <tr class="bg-brand-surface-2 border-b border-brand-border">
                        <th class="px-4 py-2.5 text-left text-label font-semibold text-brand-text-muted whitespace-nowrap">Prop</th>
                        <th class="px-4 py-2.5 text-left text-label font-semibold text-brand-text-muted whitespace-nowrap">Type</th>
                        <th class="px-4 py-2.5 text-left text-label font-semibold text-brand-text-muted whitespace-nowrap">Default</th>
                        <th class="px-4 py-2.5 text-left text-label font-semibold text-brand-text-muted">Description</th>
                    </tr>
                </thead>

                <tbody class="bg-brand-surface">
                    @foreach($props as $i => $prop)
                        <tr class="{{ $i > 0 ? 'border-t border-brand-border' : '' }}">
                            {{-- Prop name — cyan mono (design system convention) --}}
                            <td class="px-4 py-2.5 font-mono-data text-brand-cyan text-caption whitespace-nowrap align-top">{{ $prop['name'] }}</td>
                            {{-- Type — purple mono (IDE convention) --}}
                            <td class="px-4 py-2.5 font-mono-data text-caption text-purple-600 whitespace-nowrap align-top">{{ $prop['type'] }}</td>
                            {{-- Default — muted mono --}}
                            <td class="px-4 py-2.5 font-mono-data text-brand-text-muted text-caption whitespace-nowrap align-top">{{ $prop['default'] }}</td>
                            {{-- Description — regular body text --}}
                            <td class="px-4 py-2.5 text-brand-text-base align-top">{{ $prop['description'] }}</td>
                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>
    </div>

</div>
