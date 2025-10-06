## UI Consistency TODO (Tailwind + Blade Components)

Purpose: Central tracker for making the UI consistent and mobile-friendly using reusable Tailwind utilities and Blade components. This file will be updated as work progresses.

### Foundations
- [ ] Configure Tailwind tokens and theme in `tailwind.config.js`
- [ ] Add Prettier + Tailwind plugin and project formatting config

### Reusable Components (Blade)
- [ ] `x-filters.panel` – mobile-collapsible, desktop-visible filter container
- [ ] `x-table.wrapper` – overflow wrapper + `min-w-max` table shell (consistent paddings/alignments)
- [ ] `x-list.card` – mobile card item with title, meta, and actions slots
- [ ] `x-form.actions` – stacked on mobile, inline on `sm+`
- [ ] Button utilities – `.btn`, `.btn-primary`, `.btn-edit`, `.btn-delete`, etc. via `@apply`
- [ ] `x-page.header` – back-chevron + title (+ optional subtitle/actions)

### Retrofits (Priority Order)
- [ ] Orders › Show – use `x-page.header`, `x-table.wrapper`, and mobile cards where applicable
- [x] Orders › Index – mobile cards + desktop table; collapsible filters
- [x] Orders › Create – mobile actions + responsive items
- [x] Orders › Edit – mobile actions + responsive items
- [ ] Sales › Index – apply same list/table patterns and filters
- [ ] Sales › Create – responsive items, actions, and header
- [ ] Inventory › Index – table → mobile cards; collapsible filters
- [ ] Inventory › Low Stock – mobile cards; clear actions

### Documentation
- [ ] Update `docs/design-system.md` with UI conventions and Definition of Done (DoD)

### Definition of Done (for each retrofitted page)
- Uses `x-page.header` (or rationale if not applicable)
- Filters are mobile-collapsible and visible on desktop
- Mobile card list provided; desktop tables wrapped in `x-table.wrapper`
- Actions are reachable on mobile; buttons use shared `.btn*` utilities


