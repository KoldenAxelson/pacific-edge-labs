{{--
  [TASK-1-012 rev.1] Site Footer
  Changes from initial build:
    - link-underline class added to all column nav links (Products, Company, Legal)
    - NOT applied to wordmark link or credential lines — underline is for text nav only
  Structure:
    1. <x-compliance.disclaimer-banner variant="footer"> — amber strip at top
    2. max-w-7xl 4-column grid: Brand | Products | Company | Legal
    3. Bottom bar: copyright + research-use-only note
--}}
<footer class="bg-brand-navy">

    {{-- 1. Compliance disclaimer strip --}}
    <x-compliance.disclaimer-banner variant="footer" />

    {{-- 2. Main grid --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10 lg:gap-12">

            {{-- ── Brand column ──────────────────────────────────────────── --}}
            <div class="sm:col-span-2 lg:col-span-1">

                <a href="/" class="inline-flex items-center gap-3 mb-5">
                    <div class="w-8 h-8 bg-brand-navy-800 rounded-lg flex items-center justify-center flex-shrink-0">
                        <x-heroicon-o-beaker class="w-4 h-4 text-brand-cyan" aria-hidden="true" />
                    </div>
                    <div class="flex flex-col leading-none">
                        <span class="font-heading font-semibold text-white text-base leading-tight">Pacific Edge</span>
                        <span class="font-mono text-[10px] tracking-[0.2em] uppercase text-brand-navy-500 leading-tight mt-0.5">Labs</span>
                    </div>
                </a>

                {{-- Tagline — verbatim from Wix site --}}
                <p class="text-slate-400 text-body-sm leading-relaxed mb-5">
                    U.S.A. Tested · Potency Verified · Purity Quantified
                </p>

                {{-- Credentials --}}
                <ul class="space-y-2 text-caption text-slate-400" aria-label="Quality credentials">
                    <li class="flex items-start gap-2">
                        <x-heroicon-o-check-badge class="w-3.5 h-3.5 text-brand-cyan flex-shrink-0 mt-0.5" aria-hidden="true" />
                        ISO 17025 Accredited Testing
                    </li>
                    <li class="flex items-start gap-2">
                        <x-heroicon-o-check-badge class="w-3.5 h-3.5 text-brand-cyan flex-shrink-0 mt-0.5" aria-hidden="true" />
                        Third-Party CoA on Every Batch
                    </li>
                    <li class="flex items-start gap-2">
                        <x-heroicon-o-check-badge class="w-3.5 h-3.5 text-brand-cyan flex-shrink-0 mt-0.5" aria-hidden="true" />
                        Research Use Only · Not for Human Consumption
                    </li>
                </ul>
            </div>

            {{-- ── Products column ──────────────────────────────────────── --}}
            <div>
                <h3 class="text-white uppercase tracking-wider text-body-sm font-semibold mb-4">
                    Products
                </h3>
                <ul class="space-y-3" role="list">
                    <li><a href="#" class="link-underline text-slate-400 hover:text-brand-cyan text-body-sm transition-smooth">Peptides</a></li>
                    <li><a href="#" class="link-underline text-slate-400 hover:text-brand-cyan text-body-sm transition-smooth">Research Chemicals</a></li>
                    <li><a href="#" class="link-underline text-slate-400 hover:text-brand-cyan text-body-sm transition-smooth">Solvents &amp; Accessories</a></li>
                    <li><a href="#" class="link-underline text-slate-400 hover:text-brand-cyan text-body-sm transition-smooth">Bundles</a></li>
                    <li><a href="#" class="link-underline text-slate-400 hover:text-brand-cyan text-body-sm transition-smooth">New Arrivals</a></li>
                </ul>
            </div>

            {{-- ── Company column ───────────────────────────────────────── --}}
            <div>
                <h3 class="text-white uppercase tracking-wider text-body-sm font-semibold mb-4">
                    Company
                </h3>
                <ul class="space-y-3" role="list">
                    <li><a href="#" class="link-underline text-slate-400 hover:text-brand-cyan text-body-sm transition-smooth">About Us</a></li>
                    <li><a href="#" class="link-underline text-slate-400 hover:text-brand-cyan text-body-sm transition-smooth">Our Testing Process</a></li>
                    <li><a href="#" class="link-underline text-slate-400 hover:text-brand-cyan text-body-sm transition-smooth">FAQ</a></li>
                    <li><a href="#" class="link-underline text-slate-400 hover:text-brand-cyan text-body-sm transition-smooth">Contact</a></li>
                    <li><a href="{{ route('design') }}" class="link-underline text-slate-400 hover:text-brand-cyan text-body-sm transition-smooth">Design System</a></li>
                </ul>
            </div>

            {{-- ── Legal column ─────────────────────────────────────────── --}}
            <div>
                <h3 class="text-white uppercase tracking-wider text-body-sm font-semibold mb-4">
                    Legal
                </h3>
                <ul class="space-y-3" role="list">
                    <li><a href="#" class="link-underline text-slate-400 hover:text-brand-cyan text-body-sm transition-smooth">Terms of Service</a></li>
                    <li><a href="#" class="link-underline text-slate-400 hover:text-brand-cyan text-body-sm transition-smooth">Privacy Policy</a></li>
                    <li><a href="#" class="link-underline text-slate-400 hover:text-brand-cyan text-body-sm transition-smooth">Refund Policy</a></li>
                    <li><a href="#" class="link-underline text-slate-400 hover:text-brand-cyan text-body-sm transition-smooth">Shipping Policy</a></li>
                    <li><a href="#" class="link-underline text-slate-400 hover:text-brand-cyan text-body-sm transition-smooth">Research Use Policy</a></li>
                </ul>
            </div>

        </div>
    </div>

    {{-- 3. Bottom bar --}}
    <div class="border-t border-brand-navy-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-2 text-caption text-slate-400">
                <p>&copy; {{ date('Y') }} Pacific Edge Labs. All rights reserved.</p>
                <p>All products for research use only. Not for human consumption.</p>
            </div>
        </div>
    </div>

</footer>
