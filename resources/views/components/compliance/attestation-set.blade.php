{{--
  [TASK-1-010] Research Attestation Set
  Grouped checkout compliance checkboxes. Amber treatment signals a distinct,
  more serious zone on the checkout page — doing the work a red warning would
  do on a less refined site.
  Props:
    fieldErrors — associative array of field-key => error-message strings.
             Used for Phase 4 server-side validation display. Cannot be named
             `errors` — that name is reserved by Laravel as a ViewErrorBag instance.
             Keys: attest_researcher, attest_research_use, attest_age, attest_terms.
  Enforcement (all four required before checkout proceeds) is Phase 4.
--}}
@props([
    'fieldErrors' => [],   {{-- Note: cannot use 'errors' — reserved by Laravel as ViewErrorBag --}}
])

<div {{ $attributes->merge(['class' => 'rounded-2xl border border-brand-amber-border bg-brand-amber-bg/50 p-6']) }}>

    {{-- ── HEADER ─────────────────────────────────────────────────────────── --}}
    <div class="flex items-start gap-4 mb-5">

        {{-- Document-check icon in translucent amber circle --}}
        <div class="w-10 h-10 rounded-full bg-brand-amber/15 flex items-center justify-center flex-shrink-0 mt-0.5">
            <x-heroicon-o-document-check class="w-5 h-5 text-brand-amber" aria-hidden="true" />
        </div>

        <div>
            {{-- Title: amber small-caps, clearly says "Required" --}}
            <p class="text-caption font-semibold uppercase tracking-widest text-brand-amber mb-1">
                Research Attestation Required
            </p>
            {{-- Explanatory sentence --}}
            <p class="text-body-sm text-brand-text-muted leading-snug">
                All four statements must be confirmed before proceeding to checkout.
            </p>
        </div>

    </div>

    {{-- ── COMPLIANCE CHECKBOXES ───────────────────────────────────────────── --}}
    <div class="flex flex-col gap-3">

        <x-ui.form.checkbox
            :compliance="true"
            id="attest-researcher"
            name="attest_researcher"
            label="I am a qualified researcher or research professional."
            :error="$fieldErrors['attest_researcher'] ?? null"
        />

        <x-ui.form.checkbox
            :compliance="true"
            id="attest-research-use"
            name="attest_research_use"
            label="I confirm these products are for research purposes only — not for human consumption."
            :error="$fieldErrors['attest_research_use'] ?? null"
        />

        <x-ui.form.checkbox
            :compliance="true"
            id="attest-age"
            name="attest_age"
            label="I confirm I am 21 years of age or older."
            :error="$fieldErrors['attest_age'] ?? null"
        />

        <x-ui.form.checkbox
            :compliance="true"
            id="attest-terms"
            name="attest_terms"
            label="I agree to the Terms of Service, Research Use Policy, and Privacy Policy."
            :error="$fieldErrors['attest_terms'] ?? null"
        />

    </div>

</div>
