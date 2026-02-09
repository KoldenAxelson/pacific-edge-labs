# [TASK-1-000] Phase 1 Overview: Design System & Brand Foundation

## Purpose
This is a conversational task to plan and generate all Phase 1 tasks with the user. Phase 1 focuses on establishing the visual design system, brand aesthetic, and reusable component library BEFORE building features. This front-loads design decisions and creates a `/design` showcase route for client presentation.

## Phase 1 Goals
- Define Pacific Edge Labs brand aesthetic
- Create `/design` route for component showcase
- Build reusable Blade components for entire site
- Establish typography scale and color palette
- Design button hierarchy and form elements
- Create product card designs
- Build compliance UI elements (age gates, checkboxes, disclaimers)
- Design CoA display components
- Establish layout containers and spacing system
- Create badge/tag components (batch numbers, purity %, "Research Only")

## Key Decisions Already Made

### Design Philosophy
- **Priority:** Design system FIRST, features SECOND
- **Showcase:** `/design` route shows all components to client before building features
- **Reusability:** Extract components after 2-3 uses, keep them dumb/simple
- **Reference:** VisorPlate's design.blade.php pattern (uploaded by user)

### Technical Constraints
- Tailwind CSS (user is comfortable with this)
- Blade components in `/resources/views/components/`
- Alpine.js for lightweight interactions
- No JavaScript frameworks (Vue/React)

### Brand Positioning
Pacific Edge Labs needs to convey:
- **Premium quality** (not budget/generic)
- **Scientific credibility** (testing, purity, transparency)
- **Trustworthiness** (compliance, safety, professionalism)
- **Accessibility** (clear information, not overly technical)

Target customer: Willing to spend $74-300/order, values quality and transparency.

### Aesthetic Direction (Based on Current Site Analysis)
Pacific Edge's current Wix site follows a **Clinical/Professional** approach:

**Current Pacific Edge Aesthetic:**
- Dark blues and clean whites (medical, scientific)
- Professional sans-serif fonts
- Minimal decoration, focus on clarity
- Emphasizes "USA Tested - Potency Verified - Purity Quantified"
- Feels like credible research laboratory

**NOT:** Glassmorphic luxury, warm earth tones, or elevated wellness boutique aesthetic

**Design Goal:** Match or elevate their current clinical/professional direction while adding modern polish. Think "Apple Store meets research lab" not "premium spa meets supplements."

**Decision:** Maintain clinical credibility while improving UX and visual consistency.

## Conversation Starters for AI

When a user starts this phase with you, have a conversation to:

1. **Brand Aesthetic Direction**
   - "Should we go Clinical Trust (sterile, medical) or Premium Wellness (elevated, warm)?"
   - "What feeling should the site evoke? Lab/pharmacy? Wellness boutique? Tech startup?"
   - "Any competitor sites you want to reference for aesthetic inspiration?"

2. **Color Palette**
   - "Primary color: Deep Navy, Charcoal, or Medical Blue?"
   - "Accent color: Teal, Gold, or Copper?"
   - "Success/warning/error colors: Standard green/amber/red or custom?"
   - "Background: Pure white, off-white, or subtle gray?"

3. **Typography**
   - "Heading font: Inter, DM Sans, or something else?"
   - "Body font: Same as headings or different?"
   - "Monospace for batch numbers/CoA data: JetBrains Mono or SF Mono?"
   - "Font weights: Light/Regular/Medium/Bold or specific weights?"

4. **Component Priorities**
   - "Which components are critical for demo and which can wait?"
   - "Product cards: Image-first or info-first layout?"
   - "Buttons: Solid fills, outlines, or gradient fills?"
   - "Forms: Outlined inputs, filled inputs, or floating labels?"

5. **CoA Display Design**
   - "How should CoAs appear on product pages? Expandable accordion? Separate modal? Always visible?"
   - "Show full PDF preview or just download link with metadata?"
   - "Highlight purity percentage prominently?"

6. **Compliance UI**
   - "Age gate: Full-page modal, slide-in modal, or inline banner?"
   - "Disclaimer banners: Sticky header, inline on product pages, or both?"
   - "Checkbox styling: Standard, custom icons, or emphasized backgrounds?"

7. **Layout Structure**
   - "Max content width: 1280px, 1440px, or full-width?"
   - "Spacing scale: 4px base, 8px base, or custom?"
   - "Card borders: 1px solid, 2px solid, or shadow-based depth?"

## Suggested Phase 1 Tasks to Generate

After conversation with user, create tasks like:

- **TASK-1-001:** Define Color Palette & CSS Variables
- **TASK-1-002:** Typography Scale & Font Configuration
- **TASK-1-003:** Layout Containers & Spacing System
- **TASK-1-004:** Button Component Library (Primary, Secondary, Tertiary, Danger)
- **TASK-1-005:** Form Elements (Input, Select, Checkbox, Radio)
- **TASK-1-006:** Product Card Component
- **TASK-1-007:** Badge/Tag Components (Batch #, Purity %, Status)
- **TASK-1-008:** CoA Display Card Component
- **TASK-1-009:** Compliance UI Components (Age Gate Modal, Disclaimer Banners, Attestation Checkboxes)
- **TASK-1-010:** Navigation Components (Header, Footer, Category Nav)
- **TASK-1-011:** Alert/Notification Components (Success, Warning, Error, Info)
- **TASK-1-012:** `/design` Showcase Route & Blade File
- **TASK-1-013:** Component Documentation in `/design`

## AI Prompt Template
```
I'm starting Phase 1 of Pacific Edge Labs - the Design System & Brand Foundation phase.

Pacific Edge is a peptide research chemical vendor. They need a premium, trustworthy aesthetic that conveys scientific credibility without feeling sterile or cheap.

Phase 1 goals:
1. Define brand aesthetic (color palette, typography, spacing)
2. Build reusable Blade components (buttons, forms, cards, badges)
3. Create compliance UI elements (age gates, disclaimers, checkboxes)
4. Design CoA display components
5. Build `/design` showcase route to present everything to the client

Key context:
- VisorPlate used glassmorphism with copper accents (luxury feel)
- Pacific Edge needs to feel premium but also scientifically credible
- Target customer spends $74-300/order, values quality and transparency
- Can't advertise on Google/Facebook, so SEO and trust are critical
- Compliance is non-negotiable (payment processors are watching)

I've uploaded VisorPlate's design.blade.php as a reference for the `/design` route pattern.

Let's start by discussing the brand aesthetic direction. Should we go Clinical Trust (sterile, medical blues) or Premium Wellness (elevated, warmer palette)?
```

## Important Reminders

### For Design Decisions:
- Every component should serve a clear purpose
- Avoid over-designing - keep it clean and functional
- Think about mobile responsiveness from the start
- Consider accessibility (color contrast, focus states, screen readers)

### For Component Library:
- Store components in `/resources/views/components/`
- Use Blade component syntax: `<x-button>`, `<x-product-card>`, etc.
- Keep components "dumb" - they receive props, render markup
- Avoid business logic in components (that goes in Livewire/controllers)

### For `/design` Route:
- Create route: `Route::get('/design', ...)`
- Showcase ALL components on one page (or tabbed sections)
- Show component variations (primary button, secondary button, etc.)
- Include code snippets showing how to use each component
- Make it look polished - this is a client presentation tool

### For Color Palette:
- Define CSS variables in `app.css` or `tailwind.config.js`
- Use semantic naming (primary, secondary, accent, success, warning, error)
- Test color contrast for accessibility (WCAG AA minimum)
- Consider dark mode if client wants it (probably not needed for demo)

### For Typography:
- Import fonts via CDN or self-host (self-host for performance)
- Define scale: text-xs, text-sm, text-base, text-lg, text-xl, etc.
- Set line-height and letter-spacing appropriately
- Use font-weight consistently (avoid too many weights)

### For Compliance UI:
- Age gate modal should be unavoidable (no close button)
- Disclaimer banners should be prominent but not obnoxious
- Attestation checkboxes should be clearly worded and required
- "Research Only" badges should appear on product cards and detail pages

## Success Criteria for Phase 1

At the end of Phase 1, you should have:
- [ ] Color palette defined (CSS variables or Tailwind config)
- [ ] Typography scale configured (fonts imported, sizes defined)
- [ ] Layout containers created (max-width, padding, margins)
- [ ] Button components built (at least 3 variants)
- [ ] Form input components built (text, select, checkbox)
- [ ] Product card component designed and built
- [ ] Badge components created (batch #, purity %, status)
- [ ] CoA display card component built
- [ ] Age gate modal component built
- [ ] Disclaimer banner component built
- [ ] Navigation components built (header, footer)
- [ ] `/design` route accessible and showcasing all components
- [ ] Components documented with usage examples
- [ ] Client can view `/design` and approve aesthetic before features are built

**No database models or business logic yet** - this phase is pure visual design.

---
**Next Phase:** TASK-2-000 (Product Catalog & Pages)  
**Previous Phase:** TASK-0-000 (Environment & Foundation)  
**Phase:** 1 (Design System & Brand Foundation)  
**Approach:** Conversational - discuss aesthetic, then generate component tasks  
**Estimated Duration:** 2-3 days of focused work  
**Priority:** High - front-loads design decisions, prevents rework later
