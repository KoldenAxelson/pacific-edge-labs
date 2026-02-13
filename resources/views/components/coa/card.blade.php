@props([
    'batchNumber'  => '',
    'testDate'     => '',
    'purity'       => '',
    'lab'          => '',
    'downloadHref' => '#',
    'isCurrent'    => false,
    'isOpen'       => false,
])

<div
    x-data="{ open: @js($isOpen) }"
    {{ $attributes->merge(['class' => 'border border-brand-border rounded-2xl overflow-hidden bg-brand-surface']) }}
>
    {{-- ── HEADER — always visible ─────────────────────────────────────── --}}
    <button
        type="button"
        @click="open = !open"
        :aria-expanded="open.toString()"
        class="w-full flex items-center justify-between gap-3 px-5 py-4 text-left transition-smooth hover:bg-brand-surface-2"
    >
        {{-- Left: batch number + purity badge + current badge --}}
        <div class="flex flex-wrap items-center gap-2 min-w-0">
            <span class="font-mono-data text-brand-navy">{{ $batchNumber }}</span>
            <x-product.badge variant="purity" :value="$purity" size="xs" />
            @if($isCurrent)
                <x-product.badge variant="new" label="Current Batch" size="xs" />
            @endif
        </div>

        {{-- Right: chevron rotates 180° when open --}}
        <x-heroicon-o-chevron-down
            ::class="{ 'rotate-180': open }"
            class="w-4 h-4 text-brand-text-muted flex-shrink-0 transition-smooth"
            aria-hidden="true"
        />
    </button>

    {{-- ── EXPANDED CONTENT — x-collapse handles height, animate-reveal-left fires on open ── --}}
    <div x-show="open" x-collapse x-collapse.duration.400ms>
        <div
            :class="open ? 'animate-reveal-left' : ''"
            class="px-5 pt-4 pb-5 border-t border-brand-border"
        >
            {{-- Metadata grid: 2 cols mobile, 4 cols md+ --}}
            <dl class="grid grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-4 mb-5">

                {{-- Purity — hero value: larger mono cyan --}}
                <div>
                    <dt class="text-caption font-medium uppercase tracking-widest text-brand-text-muted mb-1">Purity</dt>
                    <dd class="font-mono text-brand-cyan text-2xl font-semibold leading-none">{{ $purity }}</dd>
                </div>

                {{-- Test Date --}}
                <div>
                    <dt class="text-caption font-medium uppercase tracking-widest text-brand-text-muted mb-1">Test Date</dt>
                    <dd><span class="font-mono-data text-brand-navy">{{ $testDate }}</span></dd>
                </div>

                {{-- Laboratory --}}
                <div>
                    <dt class="text-caption font-medium uppercase tracking-widest text-brand-text-muted mb-1">Laboratory</dt>
                    <dd><span class="font-mono-data text-brand-navy">{{ $lab }}</span></dd>
                </div>

                {{-- Batch --}}
                <div>
                    <dt class="text-caption font-medium uppercase tracking-widest text-brand-text-muted mb-1">Batch</dt>
                    <dd><span class="font-mono-data text-brand-navy">{{ $batchNumber }}</span></dd>
                </div>

            </dl>

            {{-- KNOWN ISSUE (low priority): The download button exhibits a visual glitch during
                 the x-collapse open animation. x-collapse uses overflow:hidden to animate height,
                 which progressively clips children top-to-bottom — making the button appear to
                 resize/reflow as it emerges from the clip region. Attempted fixes: transform-gpu,
                 sibling div with animate-fade-in, inline opacity transition, moving button outside
                 x-collapse entirely. None fully resolved it. Likely requires a custom JS solution
                 (e.g. listening for the collapse transition end event before rendering the button)
                 or rethinking the layout so no interactive element sits at the bottom of a
                 collapsible height-animated container. Defer to Phase 2 or 3. --}}
            <x-ui.button
                variant="primary"
                size="sm"
                :href="$downloadHref"
                target="_blank"
                rel="noopener noreferrer"
                icon-start="arrow-down-tray"
            >
                Download CoA
            </x-ui.button>
        </div>
    </div>

</div>
