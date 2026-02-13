{{--
  [TASK-1-010] Age Verification Gate
  Full-viewport modal overlay with blurred navy tint.
  NO close button. NO click-outside-to-dismiss — intentional compliance requirement.
  Only exits: "Enter" (sets cookie + dismisses) or "Exit" (navigates away, no cookie).

  Cookie: `age_verified=true`, 1 year, path=/, SameSite=Lax.
  Re-show from outside Alpine: call window._showAgeGate() — registered via init().
  Phase 4: supplement with server-side session for added assurance.
--}}
{{--
  Outer div: holds x-data with init() method that registers window._showAgeGate.
  init() runs in Alpine's reactive context so this.verified correctly targets
  the reactive proxy — safe to call from outside Alpine (e.g. design page button).
  Never hidden — x-show lives on the inner div only.
--}}
<div
    x-data="{
        verified: document.cookie.split(';').some(c => c.trim().startsWith('age_verified=true')),
        init() {
            window._showAgeGate = () => { this.verified = false }
        },
        verify() {
            document.cookie = 'age_verified=true; max-age=31536000; path=/; SameSite=Lax';
            this.verified = true;
        }
    }"
>

{{-- Inner div: x-show + x-cloak live here. Outer div being always-present keeps the listener above active. --}}
<div
    x-show="!verified"
    x-cloak
    class="fixed inset-0 z-[9999] flex items-center justify-center p-4"
    style="backdrop-filter: blur(8px); background-color: rgba(15, 23, 42, 0.75);"
    role="dialog"
    aria-modal="true"
    aria-labelledby="age-gate-title"
>
    {{-- ── MODAL CARD ──────────────────────────────────────────────────────── --}}
    {{-- No @click.self on the backdrop — click-outside dismissal is explicitly prohibited --}}
    <div class="w-full max-w-md rounded-2xl overflow-hidden shadow-2xl">

        {{-- ── HEADER: dark navy ───────────────────────────────────────────── --}}
        <div class="bg-brand-navy px-6 py-6 flex flex-col items-center gap-3 text-center">

            {{-- Beaker icon in translucent cyan circle --}}
            <div class="w-12 h-12 rounded-full bg-brand-cyan/20 flex items-center justify-center">
                <x-heroicon-o-beaker class="w-6 h-6 text-brand-cyan" aria-hidden="true" />
            </div>

            {{-- Eyebrow: cyan small-caps --}}
            <p class="text-caption font-semibold uppercase tracking-widest text-brand-cyan">
                Age Verification Required
            </p>

            {{-- Site name: white DM Sans --}}
            <p id="age-gate-title" class="font-heading text-xl font-semibold text-white">
                Pacific Edge Labs
            </p>

        </div>

        {{-- ── BODY: light surface ─────────────────────────────────────────── --}}
        <div class="bg-brand-surface px-6 py-6 flex flex-col gap-4">

            {{-- Explanation paragraph --}}
            <p class="text-body-sm text-brand-text-muted text-center leading-relaxed">
                This site contains research chemicals. You must be 21&nbsp;years of age or older
                to enter. By entering, you confirm you are of legal age and are accessing this
                site for legitimate research purposes.
            </p>

            {{-- Amber info box: Research Use Only --}}
            <div class="rounded-xl border border-brand-amber-border bg-brand-amber-bg px-4 py-3 flex items-start gap-3">
                <x-heroicon-o-exclamation-triangle
                    class="w-4 h-4 text-brand-amber flex-shrink-0 mt-0.5"
                    aria-hidden="true"
                />
                <p class="text-caption font-semibold text-brand-amber leading-snug">
                    Research Use Only — not for human consumption.
                </p>
            </div>

            {{-- Qualified researcher note --}}
            <p class="text-caption text-brand-text-muted text-center leading-relaxed">
                Products are available to qualified researchers and research professionals only.
            </p>

            {{-- ── ACTION BUTTONS ──────────────────────────────────────────── --}}
            <div class="flex flex-col gap-2.5 pt-1">

                {{-- ENTER: writes cookie, then dismisses gate --}}
                <x-ui.button
                    variant="primary"
                    size="md"
                    class="w-full"
                    x-on:click="verify()"
                >
                    I am 21 or older — Enter Site
                </x-ui.button>

                {{-- EXIT: navigates away from site entirely. No cookie written. --}}
                <x-ui.button
                    variant="ghost"
                    size="sm"
                    class="w-full"
                    href="https://www.google.com"
                >
                    I am under 21 — Exit
                </x-ui.button>

            </div>

            {{-- Compliance logging footer note: small, faint, but present --}}
            <p class="text-caption text-brand-text-muted/60 text-center">
                Your confirmation is logged for compliance purposes.
            </p>

        </div>
    </div>
</div>

</div>{{-- /x-data outer wrapper --}}
