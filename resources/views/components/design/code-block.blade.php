@props([
    'label'    => 'Show usage',
    'language' => 'Blade',
])

<div x-data="{ open: false, copied: false }">

    {{-- Toggle button --}}
    <button
        type="button"
        @click="open = !open"
        class="inline-flex items-center gap-1.5 text-body-sm text-brand-text-muted hover:text-brand-navy transition-smooth focus:outline-none"
    >
        <x-heroicon-o-code-bracket class="w-4 h-4 shrink-0" aria-hidden="true" />
        <span x-text="open ? 'Hide usage' : '{{ $label }}'">{{ $label }}</span>
        <span :class="open ? 'rotate-180' : ''" class="transition-smooth inline-flex" aria-hidden="true">
            <x-heroicon-o-chevron-down class="w-3.5 h-3.5" />
        </span>
    </button>

    {{-- Collapsible code block --}}
    <div x-show="open" x-collapse x-collapse.duration.300ms class="mt-2">
        <div class="rounded-lg overflow-hidden border border-brand-navy-700">

            {{-- Header bar --}}
            <div class="flex items-center justify-between px-4 py-2 bg-brand-navy-800">
                <span class="text-caption font-mono-data text-brand-navy-500">{{ $language }}</span>
                <button
                    type="button"
                    @click="
                        navigator.clipboard.writeText($refs.code.textContent.trim()).then(() => {
                            copied = true;
                            setTimeout(() => copied = false, 2000);
                        });
                    "
                    class="inline-flex items-center gap-1.5 text-caption transition-smooth focus:outline-none"
                    :class="copied ? 'text-brand-cyan' : 'text-brand-navy-500 hover:text-slate-200'"
                    :aria-label="copied ? 'Copied' : 'Copy to clipboard'"
                >
                    <template x-if="!copied">
                        <x-heroicon-o-clipboard-document class="w-3.5 h-3.5" aria-hidden="true" />
                    </template>
                    <template x-if="copied">
                        <x-heroicon-o-check class="w-3.5 h-3.5" aria-hidden="true" />
                    </template>
                    <span x-text="copied ? 'Copied!' : 'Copy'">Copy</span>
                </button>
            </div>

            {{--
                Code block — navy background, slate-200 text, monospace.
                $slot content must be HTML-escaped (&lt; &gt;) since it renders
                inside HTML. navigator.clipboard reads $refs.code.textContent
                which automatically decodes entities for the clipboard.
            --}}
            <pre class="bg-brand-navy text-slate-200 p-4 text-body-sm font-mono-data overflow-x-auto leading-relaxed"><code x-ref="code">{{ $slot }}</code></pre>

        </div>
    </div>

</div>
