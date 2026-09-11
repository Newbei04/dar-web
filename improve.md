# DAR-Webv2 — UI/UX Module Audit & Improvement Plan

> **Rating Scale:** 1 (Poor) – 5 (Excellent)
> **Date:** August 26, 2026
> **Auditor:** UI/UX Expert Review

---

## Overall Project Score: 3.2 / 5

**Summary:** The application has a solid CRUD foundation with consistent AJAX patterns, but suffers from template branding remnants, massive code duplication, missing loading states, and several modules that try to do too much in a single file. The core interaction patterns (DataTable + Modal + SweetAlert) are strong and consistent across modules.

---

## 1. AUTHENTICATION (Login / Register / Forgot Password / Lock Screen)

| Metric | Rating | Notes |
|--------|--------|-------|
| **Process Flow** | 3/5 | Login works cleanly with spinner + error feedback. Register and Forgot Password pages are non-functional stubs with hardcoded placeholder data. |
| **UI Design** | 2/5 | Login page still shows "The Evolution of DAR" which is a modified template tagline. Rainbow GIF background is jarring and unprofessional. |
| **User Interaction** | 3/5 | Login form has proper validation, button disable during submit, and error display. Password show/hide toggle works. |
| **Accessibility** | 2/5 | No `aria-label` on inputs, no focus management after error. Social login buttons (Google/Apple) are commented out but code remains. |

### Key Issues
- `page-login.php:23` — Rainbow GIF background is distracting and off-brand for a government app
- `page-login.php:36` — Description text "Login page allows users to enter login credentials..." is template filler, not helpful copy
- `page-register.php` — Hardcoded placeholder values `"hello@example.com"`, `"123456"` as password — looks unfinished
- `page-forgot-password.php` — "Already have an account?" checkbox text is misleading for a forgot-password flow
- `page-lock-screen.php` — Password pre-filled with `"123456"` — security concern
- All error pages branded as "Mophy" not DAR
- `layout.php:22-35` — Meta tags still reference "Mophy - Payment Admin Dashboard" and DexignZone

### Recommendations
- [ ] Replace rainbow GIF with a clean gradient or DAR-themed illustration
- [ ] Write proper tagline: "Department of Agrarian Reform — Resource Management System"
- [ ] Remove or implement social login buttons; remove dead code
- [ ] Remove hardcoded placeholder credentials from register/forgot-password pages
- [x] Update all meta tags in `layout.php` to DAR branding *(done — see Progress Log)*
- [ ] Update error pages to DAR branding
- [ ] Add `aria-label` attributes to all form inputs
- [ ] Add loading spinner state for forgot-password and register forms

---

## 2. DASHBOARD (Home)

| Metric | Rating | Notes |
|--------|--------|-------|
| **Process Flow** | 4/5 | Clean data aggregation: stat cards → charts → recent activity → alerts. Logical information hierarchy. |
| **UI Design** | 4/5 | Well-designed gradient hero banner, stat cards with hover effects, dark mode support. Professional look. |
| **User Interaction** | 3/5 | No skeleton loaders — charts flash empty then populate. Branch filter is hidden (`display:none`) — unclear if it's WIP or abandoned. |
| **Accessibility** | 3/5 | Charts have no text alternatives. Loading states are just "Loading..." text. |

### Key Issues
- `home.php:144-243` — 9 stat cards in a row; on `xl` screens this is fine but on `sm` they stack vertically creating a very long scroll
- `home.php:136` — Branch filter `style="display:none"` — dead UI code
- `home.php:312-313` — "Loading..." text instead of skeleton/spinner for activity feed
- `home.php:519` — `machine` variable interpolated without `escapeHtml()` in template literal — XSS risk
- Chart areas (`#chartTrend`, `#chartPie`, etc.) have no loading indicator

### Recommendations
- [ ] Add skeleton/spinner placeholders for chart and activity areas
- [ ] Consider a 2x3 or 3x3 stat card grid instead of 4x3 for better mobile flow
- [ ] Remove hidden branch filter or implement it properly
- [ ] Add `escapeHtml()` for all dynamic content in template literals
- [ ] Add `role="img"` and `aria-label` on chart containers

---

## 3. AGENCY

| Metric | Rating | Notes |
|--------|--------|-------|
| **Process Flow** | 4/5 | Standard CRUD: List → Add/Edit Modal → View Modal → Delete. Clean and predictable. |
| **UI Design** | 3/5 | Consistent with other modules. No visual flair but functional. |
| **User Interaction** | 3/5 | DataTable loads fine. Modals work. SweetAlert feedback is consistent. |
| **Accessibility** | 3/5 | Standard Bootstrap accessibility. No custom a11y work. |

### Key Issues
- `agency.php` — Mixed Bootstrap 4 (`form-group`) and Bootstrap 5 (`form-label`) classes in same form
- Auto-generated `agency_code` is not visible to user until after save — could show preview

### Recommendations
- [ ] Standardize form classes to Bootstrap 5
- [ ] Show auto-generated code preview in the Add modal before submit

---

## 4. BRANCH

| Metric | Rating | Notes |
|--------|--------|-------|
| **Process Flow** | 4/5 | Mirrors Agency — consistent CRUD pattern. |
| **UI Design** | 3/5 | Same as Agency. Functional but generic. |
| **User Interaction** | 3/5 | Works as expected. |
| **Accessibility** | 3/5 | Same as Agency. |

### Recommendations
- [ ] Same as Agency — standardize Bootstrap classes, show code preview

---

## 5. BENEFICIARY (6 files, ~2,848 lines)

| Metric | Rating | Notes |
|--------|--------|-------|
| **Process Flow** | 4/5 | Comprehensive: List → Add (multi-section form) → Edit → View (ID card + Hi-Card) → Verify (webcam + document scan). |
| **UI Design** | 4/5 | Best-designed module. ID card view is polished. Avatar upload with preview. Cascading address dropdowns. |
| **User Interaction** | 3/5 | Multi-section form is long but manageable. Verify page mixes script blocks before/after `startSection` — non-standard. |
| **Accessibility** | 2/5 | Camera/webcam access needs permission prompts. No fallback for devices without cameras. |

### Key Issues
- `beneficiary-add.php` / `beneficiary-edit.php` — Cascading address dropdown logic (~150 lines) copy-pasted 8+ times across the codebase
- `beneficiary-verify.php` — `<script>` blocks placed before `startSection('content')` — breaks template pattern
- Avatar upload component duplicated across 5 files
- `beneficiary-view.php:763` — Very heavy single file mixing ID card display, wallet info, and status

### Recommendations
- [ ] Extract cascading address dropdown into a shared JS component (`/assets/js/address-cascader.js`)
- [ ] Extract avatar upload into a reusable component
- [ ] Refactor `beneficiary-verify.php` to follow standard template pattern
- [ ] Split `beneficiary-view.php` into tab-based sub-views

---

## 6. BOOKING (8 files, ~3,716 lines)

| Metric | Rating | Notes |
|--------|--------|-------|
| **Process Flow** | 3/5 | Complex workflow: Browse → Select Dates → Book → Approve → Checkout → Return → Rate. 8 views for one module is heavy. |
| **UI Design** | 4/5 | `booking-browse.php` has rich card gallery with image carousel, calendar grid, and reviews panel. Visually strong. |
| **User Interaction** | 3/5 | `booking-browse.php` at 989 lines is the heaviest view — single-file SPA with gallery, calendar, reviews, and booking form. Three nearly-identical review-loading patterns across views. |
| **Accessibility** | 2/5 | Calendar grid has no keyboard navigation. Image carousels lack `alt` text management. |

### Key Issues
- `booking-browse.php:989` — 989 lines, single file doing everything: card gallery, image carousel, calendar, reviews, booking form
- `booking-browse.php:3` — 60+ lines of custom CSS in inline `<style>` block
- Calendar grid rendering duplicated between `booking-available.php` and `booking-browse.php`
- Review loading pattern duplicated 3 times across different booking views
- `booking-declined.php` — 102 lines, purely read-only with no actions — could be a tab in booking-list

### Recommendations
- [ ] Extract calendar grid into a shared component (`/assets/js/calendar-grid.js`)
- [ ] Extract review loading/display into a shared component
- [ ] Combine `booking-declined.php` as a tab/filter in `booking-list.php`
- [ ] Move inline CSS from `booking-browse.php` to a shared stylesheet
- [ ] Add `alt` text to all machine images in carousels

---

## 7. FACILITY (5 files, ~1,962 lines)

| Metric | Rating | Notes |
|--------|--------|-------|
| **Process Flow** | 4/5 | Clean module structure: List → Add/Edit (multi-section) → Facility Type CRUD → Facility Prices. |
| **UI Design** | 3/5 | Standard CRUD layout. Facility view has nice operating hours display and image gallery. |
| **User Interaction** | 3/5 | Cascading address dropdown duplicated between add/edit. Price management is a separate sub-module which is clean. |
| **Accessibility** | 3/5 | Standard Bootstrap accessibility. |

### Key Issues
- Cascading address dropdown logic duplicated between `facility-add.php` and `facility-edit.php` (~150 lines each)
- `facility-type.php` and `facility-prices.php` are separate pages — could be tabs in facility-list

### Recommendations
- [ ] Use shared address cascader component
- [ ] Consider merging facility-type and facility-prices as tabs in facility-list for fewer page navigations

---

## 8. LAND MONITORING (7 files, ~1,511 lines)

| Metric | Rating | Notes |
|--------|--------|-------|
| **Process Flow** | 3/5 | Three sub-modules (Parcels, Records, Monitoring Logs) split into 7 files. Navigation between them requires full page loads. |
| **UI Design** | 3/5 | Functional but plain. Log type badges are color-coded. |
| **User Interaction** | 2/5 | Inconsistent URL patterns (`$basePath` vs `$baseURL`). Role check uses string comparison `== '1'` while others use integer. |
| **Accessibility** | 2/5 | Login check exposed in client-side JS: `$_SESSION["IS_LOGIN"]` output in `<script>` — bypassable and leaks session state. |

### Key Issues
- `land-monitoring-parcel.php`, `land-monitoring-record.php`, `land-monitoring-logs.php` — Three separate pages that should be unified
- Mix of `$basePath` and `$baseURL` for navigation links
- Role check: `$_SESSION['role_id'] ?? ''` compared with `== '1'` (string) vs `== 1` (int) elsewhere
- Client-side login check `$_SESSION["IS_LOGIN"]` exposed in JS — security concern

### Recommendations
- [ ] Unify Parcels, Records, and Logs into a single page with tabs
- [x] Standardize URL construction to use `$baseURL` only *(done — see Progress Log)*
- [x] Fix role comparison to use consistent integer comparison *(done — see Progress Log)*
- [x] Remove client-side session checks — rely on server-side auth only *(done — see Progress Log)*

---

## 9. MACHINE (3 files, ~1,651 lines)

| Metric | Rating | Notes |
|--------|--------|-------|
| **Process Flow** | 4/5 | Clean structure: Machine List (with image gallery) → Machine Types → Maintenance Logs. |
| **UI Design** | 4/5 | `machine-list.php` has a polished image gallery viewer (Magnific Popup) with multi-image upload. |
| **User Interaction** | 3/5 | `machine-list.php` at 826 lines handles list + view + edit + gallery + upload all inline. |
| **Accessibility** | 3/5 | Image gallery has zoom capability. Standard accessibility. |

### Key Issues
- `machine-list.php` — Second-heaviest view (826 lines), handles too many concerns
- Image gallery viewer is embedded inline rather than a reusable component

### Recommendations
- [ ] Extract image gallery viewer into a reusable component
- [ ] Consider splitting machine-list into separate list + detail views

---

## 10. PRODUCT (10 files, ~5,511 lines)

| Metric | Rating | Notes |
|--------|--------|-------|
| **Process Flow** | 3/5 | Most complex module. Product lifecycle: Add → Price → Inventory → Stock Movements → Low Stock Alerts → Simulate → Browse (e-commerce) → Checkout. 10 views is the highest of any module. |
| **UI Design** | 4/5 | `product-details.php` (1,477 lines) is the most feature-rich view — gallery, barcode SVG, inventory table, pricing, price history, stock logs, inline editing. `product-available.php` has e-commerce card grid with shopping cart. |
| **User Interaction** | 2/5 | `product-details.php` is essentially a single-page application crammed into one PHP file. `product-available.php` at 1,345 lines mixes browsing, cart, simulation, and checkout. |
| **Accessibility** | 2/5 | Barcode SVGs have no text alternative. Shopping cart has no keyboard navigation for quantity controls. |

### Key Issues
- `product-details.php:1,477` — Largest file in the codebase. 5 tab panes, multiple modals, inline forms, all in one file
- `product-available.php:1,345` — Full shopping cart + simulation + recording in one file
- Cart logic, simulation logic, and recording logic all co-located with no modular separation
- No skeleton loaders for product image gallery
- Inline CSS proliferation (60+ lines in `product-details.php` alone)

### Recommendations
- [ ] Split `product-details.php` into tab components (Info, Inventory, Pricing, History)
- [ ] Extract shopping cart logic into `/assets/js/cart.js`
- [ ] Extract simulation logic into `/assets/js/simulation.js`
- [ ] Move all product inline CSS to a shared `/assets/css/products.css`
- [ ] Add skeleton loaders for image gallery
- [ ] Add `aria-label` to barcode SVGs

---

## 11. PROGRAM (6 files, ~1,848 lines)

| Metric | Rating | Notes |
|--------|--------|-------|
| **Process Flow** | 4/5 | Well-structured: Program List → Add/Edit → Allocations → Beneficiary Assignment. Allocation has budget validation. |
| **UI Design** | 3/5 | Standard CRUD. Two-panel beneficiary assignment (selected + available) is a good UX pattern. |
| **User Interaction** | 3/5 | `program-list.php` has inline edit modal with allocation sub-tables. Budget validation is a nice touch. |
| **Accessibility** | 2/5 | `program-list.php:20` — Role check `$_SESSION["role_id"] == 1` without `isset()` guard — will throw PHP notice. |

### Key Issues
- Missing `isset()` guard on session variables — PHP notices possible
- Beneficiary assignment two-panel UI is good but could use search/filter within panels

### Recommendations
- [ ] Add `isset()` guards to all session variable access
- [ ] Add search/filter to beneficiary assignment panels
- [ ] Show budget remaining vs allocated in allocation list

---

## 12. TRAINING (4 files, ~2,491 lines)

| Metric | Rating | Notes |
|--------|--------|-------|
| **Process Flow** | 3/5 | Training List → Add → Available (beneficiary view) → Admission (master-detail). Admissions "Add" button is commented out (incomplete feature). |
| **UI Design** | 3/5 | Card-based training display. Admission page has master-detail pattern. |
| **User Interaction** | 2/5 | `training-list.php` at 1,246 lines is the third-largest file — embeds full edit modal with nested program management. |
| **Accessibility** | 3/5 | Standard Bootstrap accessibility. |

### Key Issues
- `training-list.php:1,246` — Heaviest training file, mixes list + edit + nested program CRUD
- `training-admission.php:21-23` — "Add Admission" button commented out — incomplete feature shipped
- File upload handling duplicated with land-monitoring

### Recommendations
- [ ] Split `training-list.php` into list view + detail/edit view
- [ ] Implement or remove the commented-out admission button
- [ ] Extract file upload logic into a shared component

---

## 13. USER MANAGEMENT (5 files, ~2,479 lines)

| Metric | Rating | Notes |
|--------|--------|-------|
| **Process Flow** | 4/5 | Complete user lifecycle: List → Add → Edit → View → Role Management (with drag-and-drop module ordering). |
| **UI Design** | 4/5 | `user-role.php` has Nestable2 drag-and-drop for module ordering — excellent UX for admin. View page shows assigned modules clearly. |
| **User Interaction** | 3/5 | Avatar upload duplicated from beneficiary forms. `users-edit.php` at 805 lines is heavy due to cascading address duplication. |
| **Accessibility** | 3/5 | Drag-and-drop has basic keyboard support via Nestable2. |

### Key Issues
- Avatar upload component copy-pasted (5th duplication)
- `users-edit.php:805` — Heavy file due to cascading address logic
- `user-role.php` and `module-list.php` both include identical Nestable2 CSS (~60 lines each)

### Recommendations
- [ ] Extract avatar upload to shared component
- [ ] Use shared address cascader
- [ ] Move Nestable2 CSS to a shared stylesheet (`/assets/css/nestable.css`)

---

## 14. WALLET

| Metric | Rating | Notes |
|--------|--------|-------|
| **Process Flow** | 3/5 | Summary cards → List → Detail modal → Cash-in. Freeze/Unfreeze workflow. |
| **UI Design** | 3/5 | Summary cards at top are nice. Detail modal shows balance + transaction logs. |
| **User Interaction** | 3/5 | Action dropdown (View/Freeze/Unfreeze) is clean. Cash-in flow is simple. |
| **Accessibility** | 2/5 | No auth check in controller — any unauthenticated request can cash-in. |

### Key Issues
- `ctrl-wallet.php` — No auth check on any endpoint
- Balance changes with row locking but no confirmation dialog for freeze/unfreeze
- Cash-in success shows `new_balance` at top level instead of in `data` — inconsistent response format

### Recommendations
- [ ] Add auth guard to wallet controller
- [ ] Add SweetAlert confirmation for freeze/unfreeze actions
- [ ] Standardize JSON response format
- [ ] Add cash-in amount limits or validation feedback

---

## 15. MODULE MANAGEMENT

| Metric | Rating | Notes |
|--------|--------|-------|
| **Process Flow** | 4/5 | Drag-and-drop module ordering (Nestable2) with CRUD. Icon picker is a nice touch. |
| **UI Design** | 3/5 | Functional. Icon picker is a good UX addition for menu customization. |
| **User Interaction** | 3/5 | `module-list.php` has 7 `showLoader()` calls — suggests many separate AJAX operations that could be batched. |
| **Accessibility** | 3/5 | Nestable2 has basic keyboard support. |

### Key Issues
- `module-list.php:880` — 7 separate `showLoader()` calls = many blocking full-screen overlays
- Nestable2 CSS duplicated from `user-role.php`

### Recommendations
- [ ] Batch related AJAX operations to reduce loader calls
- [ ] Use shared Nestable2 stylesheet

---

## 16. ROLE MANAGEMENT

| Metric | Rating | Notes |
|--------|--------|-------|
| **Process Flow** | 4/5 | Role CRUD with module assignment checkboxes and drag-and-drop ordering. |
| **UI Design** | 3/5 | Permission toggles are clear. Module assignment is checkbox-based. |
| **User Interaction** | 3/5 | Works well but could use "Select All" / "Deselect All" for module permissions. |
| **Accessibility** | 3/5 | Standard checkbox accessibility. |

### Recommendations
- [ ] Add "Select All" / "Deselect All" toggle for module permissions
- [ ] Show module hierarchy in permission checkboxes (indented)

---

## 17. LOGS

| Metric | Rating | Notes |
|--------|--------|-------|
| **Process Flow** | 4/5 | Clean audit trail: aggregates 7 tables into one unified feed with module filter. |
| **UI Design** | 3/5 | Simple DataTable with filter dropdown. Functional and clean. |
| **User Interaction** | 4/5 | Module filter dropdown works well. Proper `escapeHtml()` usage — best security practice in the codebase. |
| **Accessibility** | 3/5 | Standard DataTable accessibility. |

### Key Issues
- Log aggregation is done in PHP (not SQL UNION) — performance concern at scale
- No date range filter — only module filter

### Recommendations
- [ ] Add date range filter for logs
- [ ] Consider SQL UNION or database view for better performance at scale
- [ ] Add export functionality (CSV/PDF)

---

## 18. PRICE HISTORY

| Metric | Rating | Notes |
|--------|--------|-------|
| **Process Flow** | 4/5 | Append-only audit log with facility filter. Info banner explaining read-only nature. |
| **UI Design** | 3/5 | Clean and simple. The info banner is a good UX pattern for explaining read-only views. |
| **User Interaction** | 3/5 | Facility filter works. No date range filter. |
| **Accessibility** | 3/5 | Standard accessibility. |

### Recommendations
- [ ] Add date range filter
- [ ] Add export functionality

---

## 19. PROFILE

| Metric | Rating | Notes |
|--------|--------|-------|
| **Process Flow** | 3/5 | View mode → Edit mode toggle. Password change section. Wallet section. |
| **UI Design** | 4/5 | Polished profile header with gradient cover, avatar with active status dot, stat cards. Visually strong. |
| **User Interaction** | 3/5 | `profile.php:1,325` — Very large file mixing view/edit modes with inline CSS and JS. |
| **Accessibility** | 3/5 | Standard accessibility. |

### Key Issues
- `profile.php:1,325` — 1,325 lines mixing view/edit modes, inline styles, and scripts
- Cascading address logic duplicated again
- Avatar upload component duplicated (6th time)

### Recommendations
- [ ] Split into view mode and edit mode as separate includes
- [ ] Use shared address cascader and avatar upload components
- [ ] Move inline CSS to shared stylesheet

---

## 20. DASHBOARD RECENT LOGS WIDGET

| Metric | Rating | Notes |
|--------|--------|-------|
| **Process Flow** | 4/5 | Shows 7 most recent inventory events on dashboard. Good for at-a-glance monitoring. |
| **UI Design** | 3/5 | Simple list with colored badges. |
| **User Interaction** | 3/5 | No click-through to full logs page. |
| **Accessibility** | 3/5 | Standard accessibility. |

### Recommendations
- [ ] Add "View All Logs" link at bottom of widget
- [ ] Make individual log items clickable to navigate to the relevant module

---

## Cross-Cutting Issues (Affects All Modules)

### CRITICAL

| Issue | Severity | Affected Files | Recommendation |
|-------|----------|----------------|----------------|
| **No auth guard on 29/31 controllers** | CRITICAL | All controllers except `ctrl-booking.php` and `ctrl-logs.php` | Add session auth check to every controller endpoint |
| **XSS in template literals** | HIGH | `home.php`, `booking-browse.php`, `product-details.php` | Use `escapeHtml()` for all dynamic content in JS template literals |
| ~~**Template meta tags**~~ | MEDIUM | `layout.php:22-35` | ~~Update all meta tags to DAR branding~~ **DONE** |

### HIGH

| Issue | Severity | Affected Files | Recommendation |
|-------|----------|----------------|----------------|
| **Avatar upload duplicated 6x** | HIGH | beneficiary-add/edit, users-add/edit, profile | Extract to `/assets/js/avatar-upload.js` |
| **Cascading address duplicated 8x+** | HIGH | beneficiary-add/edit, facility-add/edit, program-add, training-add/edit, users-edit, profile | Extract to `/assets/js/address-cascader.js` |
| **Nestable2 CSS duplicated 2x** | MEDIUM | module-list.php, user-role.php | Move to `/assets/css/nestable.css` |
| **Calendar grid duplicated 2x** | MEDIUM | booking-available.php, booking-browse.php | Extract to `/assets/js/calendar-grid.js` |
| **Review loading duplicated 3x** | MEDIUM | booking-list/browse/beneficiary | Extract to shared component |

### MEDIUM

| Issue | Severity | Affected Files | Recommendation |
|-------|----------|----------------|----------------|
| **No skeleton loaders** | MEDIUM | All DataTable pages, dashboard charts | Add skeleton/spinner components |
| **Mixed Bootstrap 4/5 classes** | MEDIUM | agency.php, others | Standardize to Bootstrap 5 classes |
| **Inline CSS proliferation** | MEDIUM | Every view file | Move common patterns to shared stylesheets |
| **No loading state for images** | MEDIUM | All image-heavy pages | Add image skeleton loaders |
| **File sizes > 800 lines** | MEDIUM | product-details (1,477), product-available (1,345), training-list (1,246), profile (1,325), booking-browse (989), machine-list (826), module-list (880) | Split into smaller focused components |

### LOW

| Issue | Severity | Affected Files | Recommendation |
|-------|----------|----------------|----------------|
| ~~**Inconsistent URL patterns**~~ | LOW | Land monitoring views | ~~Standardize to `$baseURL`~~ **DONE** |
| ~~**Inconsistent role comparison**~~ | LOW | Land monitoring (`== '1'`) vs others (`== 1`) | ~~Standardize to integer comparison~~ **DONE** |
| **Missing `isset()` guards** | LOW | program-list.php, others | Add `isset()` to all session access |
| ~~**Copyright says 2024**~~ | LOW | `layout.php:94` | ~~Update to dynamic year~~ **DONE** |

---

## Priority Action Plan

### Phase 1 — Security & Critical Fixes (1-2 days)
1. Add auth guard middleware to all controller endpoints
2. Add `escapeHtml()` to all JS template literal interpolations
3. Update `layout.php` meta tags to DAR branding
4. Update login page design (remove rainbow GIF, fix copy)
5. Update error pages to DAR branding

### Phase 2 — Component Extraction (3-5 days)
1. Create `/assets/js/address-cascader.js` — replaces 8+ duplications
2. Create `/assets/js/avatar-upload.js` — replaces 6 duplications
3. Create `/assets/js/calendar-grid.js` — replaces 2 duplications
4. Create `/assets/css/nestable.css` — replaces 2 duplications
5. Create skeleton/spinner components

### Phase 3 — Module Refactoring (5-7 days)
1. Split `product-details.php` into tab components
2. Split `product-available.php` cart/simulation into JS modules
3. Combine land monitoring into single tabbed page
4. Split `profile.php` into view/edit modes
5. Merge `booking-declined.php` into booking-list as a filter

### Phase 4 — UX Polish (3-4 days)
1. Add skeleton loaders to all DataTable and chart areas
2. Add date range filters to logs and price history
3. Add export functionality to logs
4. Add "View All" links to dashboard widgets
5. Add "Select All" toggle to role management permissions
6. Standardize all form classes to Bootstrap 5

---

## File Size Guide (Refactoring Targets)

| File | Lines | Priority | Action |
|------|-------|----------|--------|
| `product-details.php` | 1,477 | HIGH | Split into tab components |
| `product-available.php` | 1,345 | HIGH | Extract cart + simulation JS |
| `profile.php` | 1,325 | HIGH | Split view/edit modes |
| `training-list.php` | 1,246 | HIGH | Split list from edit |
| `booking-browse.php` | 989 | MEDIUM | Extract gallery + calendar |
| `module-list.php` | 880 | MEDIUM | Reduce AJAX call count |
| `machine-list.php` | 826 | MEDIUM | Extract image gallery |
| `users-edit.php` | 805 | MEDIUM | Use shared address cascader |
| `beneficiary-view.php` | 763 | LOW | Split into tab views |
| `beneficiary-edit.php` | 696 | LOW | Use shared components |

---

## Positive Patterns Worth Preserving

| Pattern | Where Used | Why It Works |
|---------|------------|--------------|
| AJAX-first architecture | All modules | No full page reloads — smooth SPA-like experience |
| SweetAlert for all feedback | All modules | Consistent, professional notification system |
| DataTables responsive | All list views | Mobile-friendly tables out of the box |
| showLoader/closeLoader | All AJAX calls | Consistent loading feedback |
| escapeHtml() | Newer views | XSS protection for dynamic content |
| Role-based UI hiding | All admin views | Server-side conditional rendering |
| Breadcrumb navigation | All layout views | Clear location context |
| Modals for quick actions | CRUD modules | Faster workflow than page navigation |
| Dark mode support | All views | Full theme toggle support |
| Gradient stat cards | Dashboard | Modern, visually appealing design |

---

## Progress Log

> Log of completed improvements (tick items above and update scores as work is finished).

### Date: September 8, 2026

**Phase 1 — Branding & Meta (Section 1, Cross-Cutting)**
- [x] Updated `layout.php` meta tags (title, keywords, description, OG/Twitter) to DAR branding, dropping all "Mophy"/"DexignZone" references.
- [x] Updated footer copyright to a dynamic year with DAR branding (was hardcoded 2024 + DexignZone).

**Land Monitoring (Section 8)**
- [x] Standardized all navigation/redirect URLs to use `$baseURL` only across `land-monitoring-parcel.php`, `land-monitoring-record.php`, `land-monitoring-logs.php`, `land-monitoring-parcel-edit.php`, and `land-monitoring-record-edit.php` (removed `$basePath`).
- [x] Fixed role comparison in `land-monitoring-parcel.php` from string `== '1'` to integer `== 1`.
- [x] Removed client-side session checks (`IS_LOGIN` in JS) from `land-monitoring-parcel.php`, `land-monitoring-record.php`, and `land-monitoring-logs.php`, since server-side auth in `index.php:90` already guards these routes.

**Remaining Phase 1 items (not yet applied):** auth guard middleware on 29/31 controllers, `escapeHtml()` in template literals, login page redesign, and error page DAR branding.
