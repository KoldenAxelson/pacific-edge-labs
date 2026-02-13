@props([
    'title',
    'subtitle' => null,
    'eyebrow'  => null,
])

<div {{ $attributes->merge(['class' => 'bg-brand-surface border-b border-brand-border']) }}>
    <x-ui.container>
        <div class="py-6 md:py-8">

            {{-- Breadcrumb slot — populated in Phase 2, not required now --}}
            @isset($breadcrumb)
                <div class="mb-3 text-body-sm text-brand-text-muted">
                    {{ $breadcrumb }}
                </div>
            @endisset

            {{-- Eyebrow — small-caps cyan, section/category label --}}
            @if($eyebrow)
                <p class="text-xs font-semibold uppercase tracking-widest text-brand-cyan mb-2 font-body">
                    {{ $eyebrow }}
                </p>
            @endif

            <h1 class="text-h1 font-semibold leading-tight">{{ $title }}</h1>

            @if($subtitle)
                <p class="mt-2 text-body-lg text-brand-text-muted">{{ $subtitle }}</p>
            @endif

        </div>
    </x-ui.container>
</div>
