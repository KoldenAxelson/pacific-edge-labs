<x-app-layout>

    <x-slot name="header">
        <x-ui.page-header
            eyebrow="Pacific Edge Labs"
            title="Design System"
            subtitle="Visual reference for Phase 1 tokens, typography, and layout components."
        />
    </x-slot>

    {{-- ── COLOR PALETTE ───────────────────────────────────────────────── --}}
    <x-ui.section spacing="tight">
        <x-ui.container>

            <h2 class="text-h2 mb-6">Color Palette</h2>

            {{-- Navy scale --}}
            <p class="text-label font-semibold text-brand-text-muted uppercase tracking-widest mb-3">Navy</p>
            <div class="flex flex-wrap gap-3 mb-8">
                @foreach([
                    ['bg-brand-navy',     'navy',     '#0F172A'],
                    ['bg-brand-navy-800', 'navy-800', '#1E293B'],
                    ['bg-brand-navy-700', 'navy-700', '#334155'],
                    ['bg-brand-navy-600', 'navy-600', '#475569'],
                    ['bg-brand-navy-500', 'navy-500', '#64748B'],
                ] as [$bg, $name, $hex])
                    <div class="flex flex-col gap-1">
                        <div class="{{ $bg }} rounded w-20 h-12 border border-brand-border"></div>
                        <span class="text-caption text-brand-text-muted font-mono-data">{{ $name }}</span>
                        <span class="text-caption text-brand-text-faint font-mono-data">{{ $hex }}</span>
                    </div>
                @endforeach
            </div>

            {{-- Cyan --}}
            <p class="text-label font-semibold text-brand-text-muted uppercase tracking-widest mb-3">Cyan (accent)</p>
            <div class="flex flex-wrap gap-3 mb-8">
                @foreach([
                    ['bg-brand-cyan',        'cyan',        '#06B6D4'],
                    ['bg-brand-cyan-dark',   'cyan-dark',   '#0891B2'],
                    ['bg-brand-cyan-light',  'cyan-light',  '#67E8F9'],
                    ['bg-brand-cyan-subtle', 'cyan-subtle', '#ECFEFF'],
                ] as [$bg, $name, $hex])
                    <div class="flex flex-col gap-1">
                        <div class="{{ $bg }} rounded w-20 h-12 border border-brand-border"></div>
                        <span class="text-caption text-brand-text-muted font-mono-data">{{ $name }}</span>
                        <span class="text-caption text-brand-text-faint font-mono-data">{{ $hex }}</span>
                    </div>
                @endforeach
            </div>

            {{-- Surfaces & borders --}}
            <p class="text-label font-semibold text-brand-text-muted uppercase tracking-widest mb-3">Surfaces & Borders</p>
            <div class="flex flex-wrap gap-3 mb-8">
                @foreach([
                    ['bg-brand-bg',          'bg',           '#F8F9FA'],
                    ['bg-brand-surface',     'surface',      '#FFFFFF'],
                    ['bg-brand-surface-2',   'surface-2',    '#F1F5F9'],
                    ['bg-brand-border',      'border',       '#E2E8F0'],
                    ['bg-brand-border-dark', 'border-dark',  '#CBD5E1'],
                ] as [$bg, $name, $hex])
                    <div class="flex flex-col gap-1">
                        <div class="{{ $bg }} rounded w-20 h-12 border border-brand-border-dark"></div>
                        <span class="text-caption text-brand-text-muted font-mono-data">{{ $name }}</span>
                        <span class="text-caption text-brand-text-faint font-mono-data">{{ $hex }}</span>
                    </div>
                @endforeach
            </div>

            {{-- Semantic --}}
            <p class="text-label font-semibold text-brand-text-muted uppercase tracking-widest mb-3">Semantic</p>
            <div class="flex flex-wrap gap-3 mb-8">
                @foreach([
                    ['bg-brand-success',     'success',     '#10B981'],
                    ['bg-brand-success-bg',  'success-bg',  '#ECFDF5'],
                    ['bg-brand-warning',     'warning',     '#F59E0B'],
                    ['bg-brand-warning-bg',  'warning-bg',  '#FFFBEB'],
                    ['bg-brand-error',       'error',       '#EF4444'],
                    ['bg-brand-error-bg',    'error-bg',    '#FEF2F2'],
                ] as [$bg, $name, $hex])
                    <div class="flex flex-col gap-1">
                        <div class="{{ $bg }} rounded w-20 h-12 border border-brand-border"></div>
                        <span class="text-caption text-brand-text-muted font-mono-data">{{ $name }}</span>
                        <span class="text-caption text-brand-text-faint font-mono-data">{{ $hex }}</span>
                    </div>
                @endforeach
            </div>

            {{-- Compliance / amber --}}
            <p class="text-label font-semibold text-brand-text-muted uppercase tracking-widest mb-3">Compliance (amber — restricted use)</p>
            <div class="flex flex-wrap gap-3">
                @foreach([
                    ['bg-brand-amber',        'amber',        '#D97706'],
                    ['bg-brand-amber-bg',     'amber-bg',     '#FEF3C7'],
                    ['bg-brand-amber-border', 'amber-border', '#FCD34D'],
                ] as [$bg, $name, $hex])
                    <div class="flex flex-col gap-1">
                        <div class="{{ $bg }} rounded w-20 h-12 border border-brand-border"></div>
                        <span class="text-caption text-brand-text-muted font-mono-data">{{ $name }}</span>
                        <span class="text-caption text-brand-text-faint font-mono-data">{{ $hex }}</span>
                    </div>
                @endforeach
            </div>

        </x-ui.container>
    </x-ui.section>

    <x-ui.divider />

    {{-- ── TYPOGRAPHY ──────────────────────────────────────────────────── --}}
    <x-ui.section spacing="tight">
        <x-ui.container>

            <h2 class="text-h2 mb-6">Typography</h2>

            {{-- DM Sans headings --}}
            <p class="text-label font-semibold text-brand-text-muted uppercase tracking-widest mb-4">DM Sans — Headings</p>
            <div class="space-y-4 mb-10 bg-brand-surface rounded-lg border border-brand-border p-6">
                <p class="text-display font-heading font-bold leading-none">Display / Hero text</p>
                <h1>H1 — Product Name or Page Title</h1>
                <h2>H2 — Section Heading</h2>
                <h3>H3 — Subsection or Card Title</h3>
                <h4>H4 — Label Heading</h4>
            </div>

            {{-- Inter body --}}
            <p class="text-label font-semibold text-brand-text-muted uppercase tracking-widest mb-4">Inter — Body</p>
            <div class="space-y-3 mb-10 bg-brand-surface rounded-lg border border-brand-border p-6">
                <p class="text-body-lg">Body Large — Introductory paragraphs, hero subtext. Reads comfortably at scan speed.</p>
                <p class="text-body">Body — Default. Used for all standard content, navigation labels, form inputs, and descriptions.</p>
                <p class="text-body-sm">Body Small — Supporting text, helper copy, secondary descriptions within cards.</p>
                <p class="text-label font-medium tracking-wide">Label — Form labels, section eyebrows, metadata keys.</p>
                <p class="text-caption text-brand-text-muted">Caption — Timestamps, fine print, version info, image credits.</p>
            </div>

            {{-- JetBrains Mono --}}
            <p class="text-label font-semibold text-brand-text-muted uppercase tracking-widest mb-4">JetBrains Mono — Data</p>
            <div class="space-y-3 bg-brand-surface rounded-lg border border-brand-border p-6">
                <p>Batch number: <span class="font-mono-data text-brand-navy">PEL-2026-04-A0291</span></p>
                <p>Purity: <span class="font-mono-data text-brand-cyan">99.4%</span></p>
                <p>Order ID: <span class="font-mono-data text-brand-text-muted">ORD-00018472</span></p>
            </div>

        </x-ui.container>
    </x-ui.section>

    <x-ui.divider />

    {{-- ── LAYOUT COMPONENTS ───────────────────────────────────────────── --}}
    <x-ui.section spacing="tight">
        <x-ui.container>

            <h2 class="text-h2 mb-6">Layout Components</h2>

            {{-- Page header variants --}}
            <p class="text-label font-semibold text-brand-text-muted uppercase tracking-widest mb-4">x-ui.page-header</p>
            <div class="space-y-4 mb-10 rounded-lg overflow-hidden border border-brand-border">
                <x-ui.page-header
                    eyebrow="Category Label"
                    title="Page Title with Eyebrow"
                    subtitle="Optional subtitle providing context for the page content below."
                />
                <x-ui.page-header
                    title="Page Title Only"
                />
            </div>

            {{-- Grid --}}
            <p class="text-label font-semibold text-brand-text-muted uppercase tracking-widest mb-4">x-ui.grid</p>
            <div class="mb-4">
                <p class="text-caption text-brand-text-muted mb-2">cols="3" (default) — 1 → 2 → 3</p>
                <x-ui.grid cols="3">
                    @foreach(range(1, 6) as $i)
                        <div class="bg-brand-surface border border-brand-border rounded-lg p-4 text-body-sm text-brand-text-muted">
                            Grid item {{ $i }}
                        </div>
                    @endforeach
                </x-ui.grid>
            </div>
            <div class="mb-10">
                <p class="text-caption text-brand-text-muted mb-2">cols="2" — 1 → 2</p>
                <x-ui.grid cols="2">
                    @foreach(range(1, 4) as $i)
                        <div class="bg-brand-surface border border-brand-border rounded-lg p-4 text-body-sm text-brand-text-muted">
                            Grid item {{ $i }}
                        </div>
                    @endforeach
                </x-ui.grid>
            </div>

            {{-- Container sizes --}}
            <p class="text-label font-semibold text-brand-text-muted uppercase tracking-widest mb-4">x-ui.container sizes</p>
            <div class="space-y-2 mb-10">
                @foreach(['sm' => 'max-w-3xl', 'default' => 'max-w-6xl', 'lg' => 'max-w-7xl'] as $size => $note)
                    <x-ui.container size="{{ $size }}" class="bg-brand-cyan-subtle border border-brand-cyan rounded py-2">
                        <span class="text-caption font-mono-data text-brand-cyan-dark">size="{{ $size }}" — {{ $note }}</span>
                    </x-ui.container>
                @endforeach
            </div>

            {{-- Dividers --}}
            <p class="text-label font-semibold text-brand-text-muted uppercase tracking-widest mb-4">x-ui.divider</p>
            <div class="bg-brand-surface rounded-lg border border-brand-border p-6">
                <p class="text-body-sm text-brand-text-muted">spacing="tight"</p>
                <x-ui.divider spacing="tight" />
                <p class="text-body-sm text-brand-text-muted">spacing="default"</p>
                <x-ui.divider spacing="default" />
                <p class="text-body-sm text-brand-text-muted">spacing="loose"</p>
                <x-ui.divider spacing="loose" />
                <p class="text-body-sm text-brand-text-muted">end</p>
            </div>

        </x-ui.container>
    </x-ui.section>

    <x-ui.section spacing="tight">
        <x-ui.container>
            <p class="text-caption text-brand-text-faint text-center">
                PEL Design System · Phase 1 · TASK-1-001 through TASK-1-003
            </p>
        </x-ui.container>
    </x-ui.section>

</x-app-layout>
