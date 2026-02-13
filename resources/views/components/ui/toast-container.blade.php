{{--
    Toast container — fixed bottom-right mount point for transient notifications.

    JS API (Phase 1 design demo + Phase 4/5 Livewire integration):
      window._showToast('success', 'Order placed!')
      window._showToast('error',   'Payment failed.', 6000)   ← optional duration (ms)
      window._showToast('warning', 'Session expiring soon.')
      window._showToast('info',    'New batch available.')

    WHY NO x-init:
    Alpine v3 treats the return value of x-init as a cleanup function and calls it.
    An assignment expression returns the assigned value — here, the arrow function — so
    Alpine would call add(undefined, undefined, undefined) on init, producing a blank toast
    that auto-dismisses after 4 s on every page load. window._showToast is registered in a
    <script> tag via alpine:initialized + Alpine.$data() instead.

    WHY INLINE SVGs:
    Blade renders <x-heroicon-o-*> as <svg> (foreign content). Some browsers push foreign
    content out of a <template> document fragment into the live DOM. Icons are inlined as
    raw SVG paths to avoid this. Do not replace them with <x-heroicon-o-*> components.
--}}
<div
    id="toast-mount"
    x-data="{
        toasts: [],
        add(variant, message, duration) {
            const id  = Date.now() + Math.random();
            const ms  = duration || 4000;
            this.toasts.push({ id, variant, message, duration: ms, show: true });
            setTimeout(() => this.dismiss(id), ms);
        },
        dismiss(id) {
            const t = this.toasts.find(t => t.id === id);
            if (t) t.show = false;
            setTimeout(() => { this.toasts = this.toasts.filter(t => t.id !== id); }, 400);
        }
    }"
    class="fixed bottom-4 right-4 z-50 flex flex-col gap-2 items-end w-80 pointer-events-none"
    aria-live="polite"
    aria-atomic="false"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-show="toast.show"
            x-transition:enter="animate-reveal-bottom"
            x-transition:leave="transition-medium"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="flex items-center gap-3 bg-brand-navy border border-brand-navy-700 text-white rounded-lg px-4 py-3 shadow-lg pointer-events-auto w-full"
            role="status"
        >
            {{-- check-circle — success --}}
            <svg x-show="toast.variant === 'success'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 shrink-0 text-emerald-400" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="m9 12.75 2.25 2.25L15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>

            {{-- exclamation-triangle — warning --}}
            <svg x-show="toast.variant === 'warning'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 shrink-0 text-amber-400" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
            </svg>

            {{-- x-circle — error --}}
            <svg x-show="toast.variant === 'error'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 shrink-0 text-red-400" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>

            {{-- information-circle — info (also the fallback) --}}
            <svg x-show="toast.variant === 'info' || !['success','warning','error'].includes(toast.variant)" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 shrink-0 text-cyan-400" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
            </svg>

            <p x-text="toast.message" class="text-body-sm flex-1 min-w-0"></p>
        </div>
    </template>
</div>

<script>
    document.addEventListener('alpine:initialized', () => {
        const mount = document.getElementById('toast-mount');
        if (mount) {
            window._showToast = (variant, message, duration) => {
                Alpine.$data(mount).add(variant, message, duration);
            };
        }
    });
</script>
