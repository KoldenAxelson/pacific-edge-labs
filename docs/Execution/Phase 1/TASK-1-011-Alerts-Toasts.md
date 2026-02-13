# [TASK-1-011] Alert & Notification Components

## Objective
Build inline alerts and a toast notification system. Alerts are permanent page-level messages. Toasts are transient confirmations that auto-dismiss. Both use the semantic color palette from TASK-1-001.

## Deliverables

### `resources/views/components/ui/alert.blade.php`
Props: `variant` (info/success/warning/error), `title` (optional), `dismissible` (bool).

Structure: icon left-aligned, title (if provided) bold above body text, optional X dismiss button right-aligned. `role="alert"`.

Variant styles pull from semantic tokens:
- info — `bg-brand-info-bg border-cyan-200 text-cyan-900`, information-circle icon
- success — `bg-brand-success-bg border-emerald-200 text-emerald-900`, check-circle icon
- warning — `bg-brand-warning-bg border-amber-200 text-amber-900`, exclamation-triangle icon
- error — `bg-brand-error-bg border-red-200 text-red-900`, x-circle icon

Dismissible: `x-data="{ show: true }"` `x-show="show"`. X button calls `show = false`. No animation needed on dismiss — instant is fine here.

### `resources/views/components/ui/flash-messages.blade.php`
Renders Laravel session flash as alerts. Checks for `session('success')`, `session('error')`, `session('warning')`, `session('info')`. Also renders `$errors->any()` as an error alert with a list of all validation errors. All flash alerts are `dismissible`. Add `<x-ui.flash-messages />` to `app.blade.php` at the top of `<main>`.

### `resources/views/components/ui/toast.blade.php`
Auto-dismiss notification. Props: `variant`, `message`, `duration` (default 4000ms).

`x-init="setTimeout(() => show = false, duration)"` — fires on mount.
Enter transition: `animate-reveal-bottom` from TASK-1-004.
Exit: plain opacity fade out.
`role="status"` `aria-live="polite"`.

Dark background for all toast variants (navy base) — makes them stand out against the white page surface. Variant icon and accent color still applied. The dark background with a colored icon reads cleaner than trying to adapt the full light-variant styles to a toast.

### `resources/views/components/ui/toast-container.blade.php`
Fixed bottom-right, `z-50`, `flex flex-col gap-2 items-end`. This is the mount point. Phase 4 and 5 will inject toasts via Livewire dispatch events.

## Acceptance Criteria
- [ ] All 4 alert variants render with correct icon and color
- [ ] `dismissible` alert hides on X click
- [ ] Flash messages component renders session values as styled alerts
- [ ] Toast enters with `animate-reveal-bottom` transition
- [ ] Toast auto-dismisses after 4 seconds
- [ ] Toast container is fixed bottom-right, doesn't overlap page content on mobile
- [ ] Flash messages wired into `app.blade.php`

## Notes
The toast uses dark backgrounds by design — they need to be noticeable against the off-white page. A pale success-green toast on an off-white background is easy to miss. Dark navy with an emerald icon is unmistakable.

---
**Sequence:** 11 of 15 — depends on TASK-1-004, TASK-1-005
**Estimated time:** 2–3 hours
