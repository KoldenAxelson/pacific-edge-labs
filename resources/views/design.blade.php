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

    <x-ui.divider />

    {{-- ── ANIMATION VOCABULARY ────────────────────────────────────────── --}}
    <x-ui.section spacing="tight">
        <x-ui.container>

            <h2 class="text-h2 mb-6">Animation Vocabulary</h2>

            {{-- x-collapse smoke test --}}
            <p class="text-label font-semibold text-brand-text-muted uppercase tracking-widest mb-4">x-collapse — Container + content choreography</p>
            <div class="mb-10 bg-brand-surface rounded-lg border border-brand-border overflow-hidden">
                <div x-data="{ open: false }">
                    <button
                        @click="open = !open"
                        class="w-full flex items-center justify-between px-6 py-4 text-body font-medium text-brand-navy transition-smooth hover:bg-brand-surface-2"
                    >
                        <span>Toggle collapse (click to test x-collapse plugin)</span>
                        <span x-text="open ? '↑ Close' : '↓ Open'" class="font-mono-data text-brand-cyan text-caption"></span>
                    </button>
                    <div x-show="open" x-collapse x-collapse.duration.400ms>
                        <div :class="open ? 'animate-reveal-left' : ''" class="px-6 py-4 border-t border-brand-border bg-brand-cyan-subtle">
                            <p class="text-body-sm text-brand-navy">
                                ✅ <strong>x-collapse is working.</strong> The container height animated (via <code class="font-mono-data">x-collapse</code>),
                                then this content faded in from the left with a 150ms delay (via <code class="font-mono-data">animate-reveal-left</code>).
                                That's the two-phase choreography.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Animation class reference --}}
            {{--
                NOTE: These cards show each animation firing once on page load.
                Replaying CSS animations from Alpine requires a forced browser reflow
                (element.offsetWidth trick) which is too messy for a reference page.
                To re-see an animation, just reload the page.
            --}}
            <p class="text-label font-semibold text-brand-text-muted uppercase tracking-widest mb-4">Animation classes — fires on page load (reload to replay)</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-10">
                @foreach([
                    ['animate-reveal-left',   'revealFromLeft',   '400ms · delay 150ms',            'CoA accordion, FAQ answers'],
                    ['animate-reveal-bottom', 'revealFromBottom', '350ms · delay 100ms',            'Modals, drawers, mobile nav'],
                    ['animate-scale-in',      'scaleIn',          '200ms · no delay',               'Badges, toasts, tooltips'],
                    ['animate-fade-in',       'fadeIn',           '300ms · no delay',               'Overlays, subtle transitions'],
                    ['animate-stagger',       'revealFromBottom', '400ms · --stagger-index × 60ms', 'Product grid cards'],
                ] as [$class, $keyframe, $timing, $usage])
                    <div class="bg-brand-surface border border-brand-border rounded-lg p-4 flex flex-col gap-3">
                        <code class="font-mono-data text-brand-cyan text-caption">{{ $class }}</code>
                        <div class="{{ $class }} bg-brand-cyan-subtle rounded px-3 py-2">
                            <span class="text-body-sm font-medium text-brand-cyan-dark">{{ $keyframe }}</span>
                        </div>
                        <div class="text-caption text-brand-text-muted space-y-1">
                            <p><span class="font-medium">Timing:</span> {{ $timing }}</p>
                            <p><span class="font-medium">Used for:</span> {{ $usage }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Transition utilities --}}
            <p class="text-label font-semibold text-brand-text-muted uppercase tracking-widest mb-4">Transition utilities — hover to feel</p>
            <div class="flex flex-wrap gap-4 mb-4">
                @foreach([
                    ['transition-smooth', '200ms'],
                    ['transition-medium', '300ms'],
                    ['transition-slow',   '400ms'],
                ] as [$class, $duration])
                    <div class="{{ $class }} bg-brand-surface hover:bg-brand-cyan hover:text-white border border-brand-border rounded-lg px-5 py-3 cursor-default">
                        <code class="font-mono-data text-caption">{{ $class }}</code>
                        <p class="text-caption text-brand-text-muted mt-1">{{ $duration }} ease</p>
                    </div>
                @endforeach
            </div>
            <p class="text-caption text-brand-text-muted">Hover each card — the background transitions at different speeds using the shared cubic-bezier curve.</p>

        </x-ui.container>
    </x-ui.section>

    <x-ui.divider />

    {{-- ── BUTTON COMPONENTS ───────────────────────────────────────────── --}}
    <x-ui.section spacing="tight">
        <x-ui.container>

            <h2 class="text-h2 mb-6">Button Components</h2>

            {{-- All 5 variants --}}
            <p class="text-label font-semibold text-brand-text-muted uppercase tracking-widest mb-4">x-ui.button — variants</p>
            <div class="flex flex-wrap gap-3 mb-10 p-6 bg-brand-surface rounded-lg border border-brand-border">
                <x-ui.button variant="primary">Primary</x-ui.button>
                <x-ui.button variant="secondary">Secondary</x-ui.button>
                <x-ui.button variant="outline">Outline</x-ui.button>
                <x-ui.button variant="danger">Danger</x-ui.button>
                <x-ui.button variant="ghost">Ghost</x-ui.button>
            </div>

            {{-- 3 sizes --}}
            <p class="text-label font-semibold text-brand-text-muted uppercase tracking-widest mb-4">x-ui.button — sizes</p>
            <div class="flex flex-wrap items-center gap-3 mb-10 p-6 bg-brand-surface rounded-lg border border-brand-border">
                <x-ui.button size="sm">Small</x-ui.button>
                <x-ui.button size="md">Medium (default)</x-ui.button>
                <x-ui.button size="lg">Large</x-ui.button>
            </div>

            {{-- href renders as <a> --}}
            <p class="text-label font-semibold text-brand-text-muted uppercase tracking-widest mb-4">x-ui.button — href renders as &lt;a&gt;</p>
            <div class="flex flex-wrap gap-3 mb-10 p-6 bg-brand-surface rounded-lg border border-brand-border">
                <x-ui.button href="#" variant="primary">Link (primary)</x-ui.button>
                <x-ui.button href="#" variant="secondary">Link (secondary)</x-ui.button>
                <x-ui.button href="#" variant="outline">Link (outline)</x-ui.button>
                <x-ui.button href="#" variant="ghost">Link (ghost)</x-ui.button>
            </div>

            {{-- Disabled state --}}
            <p class="text-label font-semibold text-brand-text-muted uppercase tracking-widest mb-4">x-ui.button — disabled</p>
            <div class="flex flex-wrap gap-3 mb-10 p-6 bg-brand-surface rounded-lg border border-brand-border">
                <x-ui.button variant="primary" disabled>Primary</x-ui.button>
                <x-ui.button variant="secondary" disabled>Secondary</x-ui.button>
                <x-ui.button variant="outline" disabled>Outline</x-ui.button>
                <x-ui.button variant="danger" disabled>Danger</x-ui.button>
                <x-ui.button variant="ghost" disabled>Ghost</x-ui.button>
            </div>

            {{-- Button groups --}}
            <p class="text-label font-semibold text-brand-text-muted uppercase tracking-widest mb-4">x-ui.button-group</p>
            <div class="space-y-6 mb-10 p-6 bg-brand-surface rounded-lg border border-brand-border">
                <div>
                    <p class="text-caption text-brand-text-muted mb-3">gap="3" (default) — typical action bar</p>
                    <x-ui.button-group>
                        <x-ui.button variant="primary">Save Changes</x-ui.button>
                        <x-ui.button variant="outline">Cancel</x-ui.button>
                        <x-ui.button variant="ghost">Reset</x-ui.button>
                    </x-ui.button-group>
                </div>
                <div>
                    <p class="text-caption text-brand-text-muted mb-3">gap="2" — tighter, small buttons</p>
                    <x-ui.button-group gap="2">
                        <x-ui.button variant="primary" size="sm">Export</x-ui.button>
                        <x-ui.button variant="secondary" size="sm">Import</x-ui.button>
                        <x-ui.button variant="ghost" size="sm">Refresh</x-ui.button>
                    </x-ui.button-group>
                </div>
            </div>

            {{-- Icon button --}}
            <p class="text-label font-semibold text-brand-text-muted uppercase tracking-widest mb-4">x-ui.icon-button — variants</p>
            <div class="flex flex-wrap items-center gap-3 mb-6 p-6 bg-brand-surface rounded-lg border border-brand-border">
                <x-ui.icon-button icon="magnifying-glass" label="Search" variant="ghost" />
                <x-ui.icon-button icon="magnifying-glass" label="Search" variant="primary" />
                <x-ui.icon-button icon="trash" label="Delete" variant="danger" />
                <x-ui.icon-button icon="magnifying-glass" label="Search (disabled)" variant="ghost" disabled />
            </div>

            <p class="text-label font-semibold text-brand-text-muted uppercase tracking-widest mb-4">x-ui.icon-button — sizes (all ≥ 44px touch target)</p>
            <div class="flex flex-wrap items-center gap-4 mb-6 p-6 bg-brand-surface rounded-lg border border-brand-border">
                @foreach([
                    ['sm', 'Small — 44px',  'x-mark'],
                    ['md', 'Medium — 44px', 'pencil'],
                    ['lg', 'Large — 56px',  'plus'],
                ] as [$size, $label, $icon])
                    <div class="flex flex-col items-center gap-2">
                        <x-ui.icon-button :icon="$icon" :label="$label" :size="$size" variant="ghost" />
                        <span class="text-caption text-brand-text-muted font-mono-data">{{ $label }}</span>
                    </div>
                @endforeach
            </div>

            <p class="text-label font-semibold text-brand-text-muted uppercase tracking-widest mb-4">x-ui.icon-button — href renders as &lt;a&gt;</p>
            <div class="flex flex-wrap items-center gap-3 p-6 bg-brand-surface rounded-lg border border-brand-border">
                <x-ui.icon-button href="#" icon="arrow-left" label="Go back" variant="ghost" />
                <x-ui.icon-button href="#" icon="arrow-top-right-on-square" label="Open in new tab" variant="ghost" />
            </div>

        </x-ui.container>
    </x-ui.section>

    <x-ui.divider />

    {{-- ── FORM ELEMENTS ──────────────────────────────────────────────── --}}
    <x-ui.section spacing="tight">
        <x-ui.container>

            <h2 class="text-h2 mb-6">Form Elements</h2>

            {{-- ── Input ── --}}
            <p class="text-label font-semibold text-brand-text-muted uppercase tracking-widest mb-4">x-ui.form.input — variants</p>
            <div class="grid grid-cols-1 gap-5 mb-10 p-6 bg-brand-surface rounded-lg border border-brand-border sm:grid-cols-2">

                <x-ui.form.group label="Standard input" for="demo-input-standard" hint="Helper text appears here when no error is present.">
                    <x-ui.form.input id="demo-input-standard" type="text" placeholder="Enter value…" />
                </x-ui.form.group>

                <x-ui.form.group label="Required field" for="demo-input-required" required>
                    <x-ui.form.input id="demo-input-required" type="text" placeholder="Cannot be blank" />
                </x-ui.form.group>

                <x-ui.form.group label="With leading icon" for="demo-input-leading">
                    <x-ui.form.input id="demo-input-leading" type="email" placeholder="you@example.com">
                        <x-slot:leading>
                            <x-heroicon-o-envelope class="w-4 h-4" aria-hidden="true" />
                        </x-slot:leading>
                    </x-ui.form.input>
                </x-ui.form.group>

                <x-ui.form.group label="With trailing text" for="demo-input-trailing">
                    <x-ui.form.input id="demo-input-trailing" type="number" placeholder="0.00">
                        <x-slot:trailing>
                            <span class="text-body-sm text-brand-text-muted select-none">USD</span>
                        </x-slot:trailing>
                    </x-ui.form.input>
                </x-ui.form.group>

                <x-ui.form.group label="Batch number — mono data" for="demo-input-mono">
                    <x-ui.form.input id="demo-input-mono" type="text" class="font-mono-data" placeholder="PEL-2026-04-A0000" />
                </x-ui.form.group>

                <x-ui.form.group label="Error state" for="demo-input-error" error="This field is required.">
                    <x-ui.form.input id="demo-input-error" type="text" :error="true" value="bad input" />
                </x-ui.form.group>

            </div>

            {{-- ── Textarea ── --}}
            <p class="text-label font-semibold text-brand-text-muted uppercase tracking-widest mb-4">x-ui.form.textarea</p>
            <div class="grid grid-cols-1 gap-5 mb-10 p-6 bg-brand-surface rounded-lg border border-brand-border sm:grid-cols-2">

                <x-ui.form.group label="Notes" for="demo-textarea" hint="Visible to your team only.">
                    <x-ui.form.textarea id="demo-textarea" placeholder="Add any relevant notes…" />
                </x-ui.form.group>

                <x-ui.form.group label="Description (error)" for="demo-textarea-error" error="Please provide a description of at least 20 characters.">
                    <x-ui.form.textarea id="demo-textarea-error" :error="true" rows="4">Too short.</x-ui.form.textarea>
                </x-ui.form.group>

            </div>

            {{-- ── Select ── --}}
            <p class="text-label font-semibold text-brand-text-muted uppercase tracking-widest mb-4">x-ui.form.select — custom chevron, no browser arrow</p>
            <div class="grid grid-cols-1 gap-5 mb-10 p-6 bg-brand-surface rounded-lg border border-brand-border sm:grid-cols-2">

                <x-ui.form.group label="Purity grade" for="demo-select">
                    <x-ui.form.select id="demo-select" name="purity_grade">
                        <option value="">Select grade…</option>
                        <option value="research">Research Grade (≥ 99.9%)</option>
                        <option value="analytical">Analytical Grade (≥ 99.5%)</option>
                        <option value="technical">Technical Grade (≥ 98%)</option>
                    </x-ui.form.select>
                </x-ui.form.group>

                <x-ui.form.group label="Shipping region (error)" for="demo-select-error" error="Please select a shipping region.">
                    <x-ui.form.select id="demo-select-error" name="region" :error="true">
                        <option value="">Select region…</option>
                        <option value="us">United States</option>
                        <option value="ca">Canada</option>
                    </x-ui.form.select>
                </x-ui.form.group>

            </div>

            {{-- ── Checkbox ── --}}
            <p class="text-label font-semibold text-brand-text-muted uppercase tracking-widest mb-4">x-ui.form.checkbox — standard</p>
            <div class="space-y-4 mb-6 p-6 bg-brand-surface rounded-lg border border-brand-border">

                <x-ui.form.checkbox
                    id="demo-checkbox-1"
                    label="Subscribe to order updates"
                    description="We'll email you when your order ships and is delivered."
                />

                <x-ui.form.checkbox
                    id="demo-checkbox-2"
                    label="Remember this device"
                />

                <x-ui.form.checkbox
                    id="demo-checkbox-error"
                    label="Accept terms of service"
                    error="You must accept the terms to continue."
                />

            </div>

            <p class="text-label font-semibold text-brand-text-muted uppercase tracking-widest mb-4">x-ui.form.checkbox — compliance (amber)</p>
            <div class="space-y-4 mb-10 p-6 bg-brand-surface rounded-lg border border-brand-border">

                <x-ui.form.checkbox
                    id="demo-compliance-1"
                    compliance
                    label="I confirm the intended use is research only"
                    description="I certify that this product will be used solely for research purposes and will not be used in humans, animals, or for any diagnostic or therapeutic application."
                />

                <x-ui.form.checkbox
                    id="demo-compliance-2"
                    compliance
                    label="I am a qualified researcher or licensed professional"
                    description="I hold the appropriate credentials and institutional authorizations required to handle research-grade chemical compounds."
                />

            </div>

            {{-- ── Radio ── --}}
            <p class="text-label font-semibold text-brand-text-muted uppercase tracking-widest mb-4">x-ui.form.radio</p>
            <div class="space-y-3 mb-10 p-6 bg-brand-surface rounded-lg border border-brand-border">

                <x-ui.form.radio
                    id="demo-radio-standard"
                    name="shipping"
                    value="standard"
                    label="Standard shipping (5–7 business days)"
                    description="Ships in a plain, unmarked box via USPS Priority Mail."
                />

                <x-ui.form.radio
                    id="demo-radio-express"
                    name="shipping"
                    value="express"
                    label="Express shipping (2 business days)"
                    description="Signature required on delivery."
                />

                <x-ui.form.radio
                    id="demo-radio-overnight"
                    name="shipping"
                    value="overnight"
                    label="Overnight (next business day)"
                />

            </div>

            {{-- ── Composed example ── --}}
            <p class="text-label font-semibold text-brand-text-muted uppercase tracking-widest mb-4">Composed form example</p>
            <div class="mb-10 p-6 bg-brand-surface rounded-lg border border-brand-border">
                <form class="space-y-5 max-w-lg" onsubmit="return false;">

                    <x-ui.form.group label="Full name" for="demo-composed-name" required>
                        <x-ui.form.input id="demo-composed-name" type="text" placeholder="Dr. Jane Smith" />
                    </x-ui.form.group>

                    <x-ui.form.group label="Institutional email" for="demo-composed-email" required hint="Must be a .edu or research institution address.">
                        <x-ui.form.input id="demo-composed-email" type="email" placeholder="jsmith@university.edu">
                            <x-slot:leading>
                                <x-heroicon-o-envelope class="w-4 h-4" aria-hidden="true" />
                            </x-slot:leading>
                        </x-ui.form.input>
                    </x-ui.form.group>

                    <x-ui.form.group label="Intended application" for="demo-composed-application" required>
                        <x-ui.form.select id="demo-composed-application" name="application">
                            <option value="">Select application…</option>
                            <option value="synthesis">Chemical synthesis</option>
                            <option value="assay">Assay development</option>
                            <option value="reference">Reference standard</option>
                            <option value="other">Other research use</option>
                        </x-ui.form.select>
                    </x-ui.form.group>

                    <x-ui.form.group label="Additional notes" for="demo-composed-notes">
                        <x-ui.form.textarea id="demo-composed-notes" placeholder="Describe your research application…" rows="3" />
                    </x-ui.form.group>

                    <x-ui.form.checkbox
                        id="demo-composed-compliance-1"
                        compliance
                        label="I confirm research-only use"
                        description="This product will not be used in humans, animals, or for diagnostic or therapeutic purposes."
                    />

                    <x-ui.form.checkbox
                        id="demo-composed-compliance-2"
                        compliance
                        label="I confirm I am a qualified researcher"
                        description="I hold appropriate credentials and institutional authorizations to handle research-grade compounds."
                    />

                    <x-ui.button-group>
                        <x-ui.button type="submit" variant="primary">Submit order</x-ui.button>
                        <x-ui.button type="button" variant="outline">Cancel</x-ui.button>
                    </x-ui.button-group>

                </form>
            </div>

        </x-ui.container>
    </x-ui.section>

    <x-ui.divider />

    {{-- ── BADGE COMPONENTS ───────────────────────────────────────────── --}}
    <x-ui.section spacing="tight">
        <x-ui.container>

            <h2 class="text-h2 mb-6">Badge Components</h2>

            {{-- ── Research badge ── --}}
            <p class="text-label font-semibold text-brand-text-muted uppercase tracking-widest mb-4">x-product.badge — research (compliance signal)</p>
            <div class="flex flex-wrap items-center gap-4 mb-10 p-6 bg-brand-surface rounded-lg border border-brand-border">
                <x-product.badge variant="research" />
                <x-product.badge variant="research" size="xs" />
                <x-product.badge variant="research" label="For Research Use Only" />
            </div>

            {{-- ── Purity badge ── --}}
            <p class="text-label font-semibold text-brand-text-muted uppercase tracking-widest mb-4">x-product.badge — purity (primary trust differentiator)</p>
            <div class="flex flex-wrap items-center gap-4 mb-10 p-6 bg-brand-surface rounded-lg border border-brand-border">
                <x-product.badge variant="purity" value="99.9%" />
                <x-product.badge variant="purity" value="98.4%" />
                <x-product.badge variant="purity" value="99.9%" size="xs" />
            </div>

            {{-- ── Batch badge ── --}}
            <p class="text-label font-semibold text-brand-text-muted uppercase tracking-widest mb-4">x-product.badge — batch (secondary metadata)</p>
            <div class="flex flex-wrap items-center gap-4 mb-10 p-6 bg-brand-surface rounded-lg border border-brand-border">
                <x-product.badge variant="batch" value="PEL-2026-04-A0291" />
                <x-product.badge variant="batch" value="PEL-2026-01-B0047" size="xs" />
            </div>

            {{-- ── Stock badges ── --}}
            <p class="text-label font-semibold text-brand-text-muted uppercase tracking-widest mb-4">x-product.badge — stock status</p>
            <div class="flex flex-wrap items-center gap-4 mb-10 p-6 bg-brand-surface rounded-lg border border-brand-border">
                <x-product.badge variant="in_stock" />
                <x-product.badge variant="low_stock" />
                <x-product.badge variant="out_of_stock" />
                <x-product.badge variant="in_stock" size="xs" />
                <x-product.badge variant="low_stock" size="xs" />
                <x-product.badge variant="out_of_stock" size="xs" />
            </div>

            {{-- ── Category + New + Sale badges ── --}}
            <p class="text-label font-semibold text-brand-text-muted uppercase tracking-widest mb-4">x-product.badge — category, new & sale</p>
            <div class="flex flex-wrap items-center gap-4 mb-10 p-6 bg-brand-surface rounded-lg border border-brand-border">
                <x-product.badge variant="category" value="Peptides" />
                <x-product.badge variant="category" value="Nootropics" />
                <x-product.badge variant="category" value="Reference Standards" />
                <x-product.badge variant="new" />
                <x-product.badge variant="new" size="xs" />
                <x-product.badge variant="sale" />
                <x-product.badge variant="sale" size="xs" />
            </div>

            {{-- ── Size comparison ── --}}
            <p class="text-label font-semibold text-brand-text-muted uppercase tracking-widest mb-4">Size comparison — sm (default) vs xs</p>
            <div class="flex flex-wrap items-end gap-6 mb-10 p-6 bg-brand-surface rounded-lg border border-brand-border">
                <div class="flex flex-col gap-2">
                    <p class="text-caption text-brand-text-muted">sm</p>
                    <div class="flex flex-wrap items-center gap-2">
                        <x-product.badge variant="research" size="sm" />
                        <x-product.badge variant="purity" value="99.9%" size="sm" />
                        <x-product.badge variant="batch" value="PEL-2026-04-A0291" size="sm" />
                    </div>
                </div>
                <div class="flex flex-col gap-2">
                    <p class="text-caption text-brand-text-muted">xs</p>
                    <div class="flex flex-wrap items-center gap-2">
                        <x-product.badge variant="research" size="xs" />
                        <x-product.badge variant="purity" value="99.9%" size="xs" />
                        <x-product.badge variant="batch" value="PEL-2026-04-A0291" size="xs" />
                    </div>
                </div>
            </div>

            {{-- ── Badge group — composed product card context ── --}}
            <p class="text-label font-semibold text-brand-text-muted uppercase tracking-widest mb-4">x-product.badge-group — product card context</p>
            <div class="space-y-6 mb-6 p-6 bg-brand-surface rounded-lg border border-brand-border">

                {{-- Full badge set (sm) --}}
                <div>
                    <p class="text-caption text-brand-text-muted mb-2">Standard product card (sm)</p>
                    <x-product.badge-group>
                        <x-product.badge variant="purity" value="99.9%" />
                        <x-product.badge variant="research" />
                        <x-product.badge variant="batch" value="PEL-2026-04-A0291" />
                        <x-product.badge variant="in_stock" />
                    </x-product.badge-group>
                </div>

                {{-- Compact card (xs) --}}
                <div>
                    <p class="text-caption text-brand-text-muted mb-2">Compact card (xs)</p>
                    <x-product.badge-group gap="gap-1.5">
                        <x-product.badge variant="purity" value="98.4%" size="xs" />
                        <x-product.badge variant="research" size="xs" />
                        <x-product.badge variant="batch" value="PEL-2026-01-B0047" size="xs" />
                        <x-product.badge variant="low_stock" size="xs" />
                    </x-product.badge-group>
                </div>

                {{-- New product --}}
                <div>
                    <p class="text-caption text-brand-text-muted mb-2">New product with category</p>
                    <x-product.badge-group>
                        <x-product.badge variant="new" />
                        <x-product.badge variant="purity" value="99.5%" />
                        <x-product.badge variant="research" />
                        <x-product.badge variant="category" value="Peptides" />
                        <x-product.badge variant="in_stock" />
                    </x-product.badge-group>
                </div>

            </div>

        </x-ui.container>
    </x-ui.section>

    <x-ui.divider />

    {{-- ── PRODUCT CARD ────────────────────────────────────────────────── --}}
    <x-ui.section spacing="tight">
        <x-ui.container>

            <h2 class="text-h2 mb-6">Product Card</h2>

            {{-- ── 3-column grid — all corner badge states + in stock (no badge) ── --}}
            <p class="text-label font-semibold text-brand-text-muted uppercase tracking-widest mb-4">x-product.card — 3-col grid (hover each card)</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">

                {{-- In stock, no badge — clean state, corner is empty --}}
                <x-product.card
                    name="BPC-157"
                    category="Peptides"
                    price="$89.00"
                    purity="99.9%"
                    batch-number="PEL-2026-04-A0291"
                    batch-status="in_stock"
                    href="#"
                    research-summary="BPC-157 has been studied for tissue repair and gut lining regeneration in preclinical animal models."
                />

                {{-- Low stock — amber badge in corner, long name exercises line-clamp-2 --}}
                <x-product.card
                    name="TB-500 (Thymosin Beta-4 Fragment)"
                    category="Peptides"
                    price="$124.00"
                    purity="99.5%"
                    batch-number="PEL-2026-03-B0047"
                    batch-status="low_stock"
                    href="#"
                    research-summary="TB-500 has been studied for actin regulation and cellular migration in preclinical wound-healing models."
                />

                {{-- Out of stock — gray badge in corner, disabled Notify Me CTA --}}
                <x-product.card
                    name="Epithalon"
                    category="Peptides"
                    price="$67.00"
                    purity="98.4%"
                    batch-status="out_of_stock"
                    href="#"
                />

            </div>

            {{-- ── Sale pricing — slash display + green Sale badge in corner ── --}}
            <p class="text-label font-semibold text-brand-text-muted uppercase tracking-widest mb-4">x-product.card — sale pricing (original-price triggers slash display)</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">

                {{-- Sale + in stock — green badge corner, struck original, green sale price --}}
                <x-product.card
                    name="Hospira Bacteriostatic Water"
                    category="Solvents"
                    price="$18.95"
                    original-price="$25.95"
                    purity="USP Grade"
                    batch-status="in_stock"
                    href="#"
                />

                {{-- Sale + low stock — sale badge wins corner over low stock --}}
                <x-product.card
                    name="Semax"
                    category="Nootropics"
                    price="$44.00"
                    original-price="$54.00"
                    purity="99.1%"
                    batch-number="PEL-2026-02-C0183"
                    batch-status="low_stock"
                    href="#"
                    research-summary="Semax has been studied for neuroprotective effects and BDNF upregulation in rodent cognitive models."
                />

            </div>

            {{-- ── Skeleton ── --}}
            <p class="text-label font-semibold text-brand-text-muted uppercase tracking-widest mb-4">x-product.card-skeleton — pulse placeholder (3-col)</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                <x-product.card-skeleton />
                <x-product.card-skeleton />
                <x-product.card-skeleton />
            </div>

        </x-ui.container>
    </x-ui.section>

    <x-ui.divider />

    {{-- ── COA ACCORDION ──────────────────────────────────────────────────── --}}
    <x-ui.section spacing="tight">
        <x-ui.container>

            <h2 class="text-h2 mb-6">CoA Accordion</h2>

            {{-- ── x-coa.accordion-list + x-coa.card — full accordion stack ── --}}
            <p class="text-label font-semibold text-brand-text-muted uppercase tracking-widest mb-4">x-coa.accordion-list — stacked with multiple batches</p>
            <div class="max-w-2xl mb-10">
                <x-coa.accordion-list>

                    {{-- Current batch — starts expanded --}}
                    <x-coa.card
                        batch-number="PEL-2026-02-C0211"
                        test-date="2026-01-28"
                        purity="99.4%"
                        lab="Janoshik Analytical"
                        download-href="#"
                        :is-current="true"
                        :is-open="true"
                    />

                    {{-- Previous batch — collapsed --}}
                    <x-coa.card
                        batch-number="PEL-2025-11-C0183"
                        test-date="2025-11-03"
                        purity="98.9%"
                        lab="Janoshik Analytical"
                        download-href="#"
                    />

                    {{-- Older batch — collapsed --}}
                    <x-coa.card
                        batch-number="PEL-2025-08-C0149"
                        test-date="2025-07-22"
                        purity="99.1%"
                        lab="Core Lab Sciences"
                        download-href="#"
                    />

                </x-coa.accordion-list>
            </div>

            {{-- ── x-coa.summary-strip — compact inline, order confirmation context ── --}}
            <p class="text-label font-semibold text-brand-text-muted uppercase tracking-widest mb-4">x-coa.summary-strip — non-interactive, order confirmation use</p>
            <div class="max-w-2xl mb-6">
                <x-coa.summary-strip
                    batch-number="PEL-2026-02-C0211"
                    test-date="2026-01-28"
                    purity="99.4%"
                    lab="Janoshik Analytical"
                />
            </div>

        </x-ui.container>
    </x-ui.section>

    <x-ui.divider />

    {{-- ── COMPLIANCE UI ───────────────────────────────────────────────────── --}}
    <x-ui.section spacing="tight">
        <x-ui.container>

            <h2 class="text-h2 mb-6">Compliance UI</h2>

            {{-- ── Disclaimer banner — page-top variant ── --}}
            <p class="text-label font-semibold text-brand-text-muted uppercase tracking-widest mb-4">x-compliance.disclaimer-banner — page-top variant (default)</p>
            <div class="mb-8 rounded-xl overflow-hidden border border-brand-border">
                <x-compliance.disclaimer-banner />
            </div>

            {{-- ── Disclaimer banner — footer variant ── --}}
            <p class="text-label font-semibold text-brand-text-muted uppercase tracking-widest mb-4">x-compliance.disclaimer-banner — footer variant</p>
            <div class="mb-8 rounded-xl overflow-hidden border border-brand-border">
                <x-compliance.disclaimer-banner variant="footer" />
            </div>

            {{-- ── Disclaimer banner — compact ── --}}
            <p class="text-label font-semibold text-brand-text-muted uppercase tracking-widest mb-4">x-compliance.disclaimer-banner — compact</p>
            <div class="mb-10 rounded-xl overflow-hidden border border-brand-border">
                <x-compliance.disclaimer-banner :compact="true" />
            </div>

            {{-- ── Attestation set ── --}}
            <p class="text-label font-semibold text-brand-text-muted uppercase tracking-widest mb-4">x-compliance.attestation-set — checkout compliance checkboxes</p>
            <div class="max-w-xl mb-10">
                <x-compliance.attestation-set />
            </div>

            {{-- ── Age gate trigger ── --}}
            <p class="text-label font-semibold text-brand-text-muted uppercase tracking-widest mb-4">x-compliance.age-gate — full-viewport modal</p>
            <div class="flex flex-wrap items-center gap-4 mb-3">
                <x-ui.button
                    variant="secondary"
                    size="md"
                    icon="shield-exclamation"
                    onclick="window._showAgeGate && window._showAgeGate()"
                >
                    Preview Age Gate
                </x-ui.button>
                <x-ui.button
                    variant="ghost"
                    size="sm"
                    icon="arrow-path"
                    onclick="document.cookie='age_verified=; max-age=0; path=/'; window.location.reload()"
                >
                    Clear cookie &amp; reload
                </x-ui.button>
            </div>
            <p class="text-caption text-brand-text-muted mb-6">
                "Preview Age Gate" re-shows the modal without clearing your cookie — useful for visual inspection.
                "Clear cookie &amp; reload" deletes the <code class="font-mono-data">age_verified</code>
                cookie in-browser and reloads so the gate appears as a fresh visitor would see it.
            </p>

        </x-ui.container>
    </x-ui.section>

    <x-ui.section spacing="tight">
        <x-ui.container>
            <p class="text-caption text-brand-text-faint text-center">
                PEL Design System · Phase 1 · TASK-1-001 through TASK-1-010
            </p>
        </x-ui.container>
    </x-ui.section>

</x-app-layout>
