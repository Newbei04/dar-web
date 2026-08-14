# DAR Web V2 — Process Improvement Review

> Scope: **business process only**. No security review. Each module's workflow is mapped, rated on process quality/maturity, and given improvement suggestions.

---

## Ranking Summary

| Rank | Module | Rating | Verdict |
|---|---|---|---|
| 1 | Booking (machinery rental) | **8/10** | Best state machine — guarded transitions + audit log |
| 2 | Machinery + Maintenance | **7/10** | Clean lifecycle, maintenance auto-restores availability |
| 3 | Inventory / Product (stock ops) | **6.5/10** | Rich action model + logs; sales/reservation not coupled |
| 4 | Training | **6.5/10** | Two-axis lifecycle; missing capacity + date rules |
| 5 | Pricing (facility prices) | **6/10** | Append-only history is great; doesn't sync to batches |
| 6 | Users / Auth / Profile | **6/10** | Solid login + profile update; dead/broken bits |
| 7 | Roles / Access | **6/10** | Module matrix via CSV; ok, admin-gated |
| 8 | Beneficiary | **5.5/10** | Good onboarding; verification backend missing |
| 9 | Program / Allocation | **5/10** | Budget math broken; capacity accounting flawed |
| 10 | Checkout (POS) | **4/10** | Orphaned API, no order lifecycle, no UI wiring |

**Best overall pattern to copy: Booking** — explicit status states, each transition guarded by `SELECT ... FOR UPDATE` + expected-status check (idempotent, double-click safe), and a single `booking_logs` audit row that stamps who/when at every step. **Machinery + Maintenance** is the second-best example (clean `0 = Maintenance / 1 = Available` lifecycle with automatic restore on completion).

---

## Per-Module Process Maps

### 1. Booking (Machinery Rental) — ⭐ Best — 8/10

**Process flow**
1. **Browse availability** — a machine is listed only if `machinery.status = 1` **and** it has no active booking (`booking.status IN (0,1,2)` via `NOT EXISTS`). Availability is derived, never stored.
2. **Book** — staff/admin pick a beneficiary; beneficiary (role 3) books for itself only. Calendar picks start/end; cost = `daily_rate × days` (computed client-side).
3. **Submit** (`ADD_BOOKING`) — DB transaction: overlap re-check on `booking_schedules` → insert `booking` (`status=0`) → generate booking number `BK-…` + Luhn check digit → insert `booking_logs` + `booking_schedules`.
4. **Approve / Decline** (`APPROVE_BOOKING` / `DECLINE_BOOKING`) — `0→1` or `0→4`, guarded by `WHERE id=? AND status='0' FOR UPDATE`. Decline stores remarks.
5. **Checkout** (`CHECKOUT_BOOKING`) — `1→2` (physical pickup), same status guard.
6. **Return** (`RETURN_BOOKING`) — `2→3` (terminal).
7. **Monitor** — beneficiary sees a status timeline; declined shows reason.

**Status lifecycle:** `0 Pending → 1 Approved → 2 Checkout → 3 Returned` (terminal) · `0 → 4 Declined` (terminal). No paths out of 3/4.

**What's good**
- Every transition is an idempotent, state-guarded operation (double-submit safe).
- Single audit trail (`booking_logs`: booked_by, approved_at/staff, declined_at/staff/remarks, checkout_at, returned_at).
- Overlap conflict detection on schedules; declined bookings never block the machine.

**What's missing / weak**
- **No cancellation** — beneficiary can't withdraw; staff can't cancel an *approved* booking; no reschedule.
- **Pending bookings block the machine indefinitely** — no auto-expiry of stale pending/approved.
- **No overdue/overstay tracking** — checkout/return don't enforce dates, no late fees.
- **Race on `ADD_BOOKING`** — overlap SELECT + INSERT not locked together → concurrent double-book.
- **`APPROVE_BOOKING` doesn't re-check availability** → two raced pendings can both be approved.
- **Cost is client-supplied** — server never recomputes `unit_price`/`total_cost`; one entry point stores 0.
- `booking_schedules.status` is vestigial (filtered `!= 2` but never set to 2).

**Suggestions**
- Add a **Cancel** state (only from Pending/Approved) + re-open the slot.
- Auto-expire pending after X days; enforce dates server-side (`end >= start`, future start).
- Re-check availability inside `APPROVE_BOOKING`; lock schedules in `ADD_BOOKING` (or add a unique constraint on machine+date range).
- Compute price server-side from `daily_rate`.
- Track overdue (expected return vs actual return) and show a "returned late" flag.

---

### 2. Machinery + Maintenance — 7/10

**Process flow**
1. **Machine types** — CRUD (`ctrl-machine-type.php`), hard delete.
2. **Create machine** (`ADD_MACHINERY`) — branch + type + name required; images uploaded, first = primary.
3. **Edit machine** (`UPDATE_MACHINERY`) — dynamic field update; replaces images.
4. **Maintenance** (`ADD_MAINTENANCE`) — admin-only; machinery_id + facility + type + priority required; inserts `status=1 Scheduled`, then **sets `machinery.status = 0` (Under Maintenance)**.
5. **Complete** (`UPDATE_MAINTENANCE_STATUS` → status 3) — when Completed, **sets `machinery.status = 1` (Available)**.
6. **Status labels** — 1 Scheduled, 2 In Progress (derived from date window), 3 Completed, 4 Awaiting Parts. `ui_status` is recomputed from dates and can disagree with stored status.

**What's good**
- Simple, clear `0 Maintenance / 1 Available` lifecycle with automatic restore on completion.
- Branch-scoped listing for non-admins.

**What's missing / weak**
- **No maintenance history/audit** — only current record; past jobs not logged as a history list.
- **Only "Done" is wired in the UI** — no explicit "Start" or "Awaiting Parts" action (those rely on date auto-labels).
- **Split status sources** — `machinery.status` (maintenance) vs derived booking availability; completion can set "Available" while an active booking exists (display divergence only).

**Suggestions**
- Add a maintenance **history ledger** (one row per completed job, cost, technician).
- Wire explicit In-Progress / Awaiting-Parts transitions.
- Decide a single availability authority (recommend: machine is "Available" only if `status=1` AND no active booking — one computed field).

---

### 3. Inventory / Product (Stock Operations) — 6.5/10

**Process flow**
1. **Catalog** — category → create product (name required, duplicate-name blocked, SKU auto-generated `BRAND-CAT-YYMMDD-RANDOM`, up to 10 images, first = primary).
2. **Receive stock** (`ADD_INVENTORY` / `RECEIVE_INVENTORY`) — facility + product required; `reserved_stock <= current_stock`. **Same batch** (facility+product+batch+cost+selling+expiry) → `current_stock += qty`; else new batch row (`received_stock = current_stock = qty`).
3. **Reserve** — `reserved_stock += qty`, max `available = current − reserved`.
4. **Release** — `reserved_stock -= qty`, max `reserved_stock`.
5. **Adjust** — `current_stock = new_stock` (≥ reserved), logs delta.
6. **Transfer** — deduct source `current_stock`, create new destination batch (`orig-T<ts>`), dual logs IN/OUT sharing `reference_id`.
7. **Sale** (`PLACE_CHECKOUT`) — FIFO/FEFO (oldest expiry first) or by batch; deducts `current_stock` only; all-or-nothing rollback.
8. **Monitoring** — low stock (`current_stock > 0 AND <= reorder_level`), expiring (≤60 days), ledger/movement reports (11 report types server-side).

**Stock fields & rules**
- `available = current_stock − reserved_stock` — always computed, never stored.
- 10 log action types: 1 Receive, 2 Sale, 3 Return, 4 Adjustment, 5 Expired, 6 Damaged, 7 Price Change, 8 Reserve, 9 Release, 10 Transfer.

**What's good**
- Full audit log (`product_inventory_logs`) on every stock action with quantity + new balance + who.
- Reserve/Release/Adjust/Transfer all validate against availability.
- FEFO/FIFO allocation preserves per-batch pricing.

**What's missing / weak**
- **Reserved stock is never consumed on sale** — checkout deducts `current_stock` only → available can go negative; reservations not auto-released on sale.
- **No Return / Expired / Damaged flows** — action types 3, 5, 6 exist as labels only; stock can't be written off.
- **No expiry/stock-out enforcement at sale** — expired batches can be sold; zero-stock batches excluded from low-stock alerts.
- **`received_stock` not incremented on restock** → "received to date" underreports.
- **`product-details.php` UI is broken in several spots** (posts wrong trans names / wrong field names → "Invalid transaction"; see cross-cutting).
- **Two sources of selling price** (batch vs facility) that never sync.
- No duplicate-barcode check; batch status toggle writes no log; `UPDATE_INVENTORY` (full audit edit) is dead code.

**Suggestions**
- On sale from a reserved quantity, **decrement both `current_stock` and `reserved_stock`** (or auto-release the reservation).
- Add **Return / Expired / Damaged** stock movements that write action 3/5/6 logs.
- Block sale of expired batches; include zero-stock batches in alerts.
- Fix `received_stock` accumulation; add restock suggestion when `current <= reorder_level`.
- Pick **one source of truth** for selling price (recommend `product_facility_price`, push through to batches on change).
- Fix/wire the `product-details.php` batch + receive + archive flows.

---

### 4. Training — 6.5/10

**Process flow**
1. **Create training** (`ADD_TRAINING`) — title + accreditation required; `reference_no = TRN-<time>`; `approval_status=0`.
2. **Define sessions** (training_program) — topic + speaker + time slots with overlap validation.
3. **Approve / Reject** (`APPROVE_TRAINING` / `REJECT_TRAINING`, admin) — writes append-only `training_approval` log (action, reason, approved_by).
4. **Start** (`START_TRAINING`) — only when approved + not started; `status=1`.
5. **Enroll** — two channels: beneficiary **self-enroll** (approved + not-closed only, duplicate check, `doc_num` auto-copied) or **staff bulk-enroll** (approved + started/closed trainings only, duplicate skip).
6. **Attendance** (`UPDATE_ADMISSION_STATUS`) — Present (`attendance_at` stamped) / Absent.
7. **Close** (`CLOSE_TRAINING`) — `status=2`, locks attendance UI.
8. CRUD: edit / view / delete.

**Status lifecycle (two axes):**
- `approval_status`: 0 Pending → 1 Approved / 2 Rejected (no re-approval path).
- `status`: NULL Not Started → 1 Started → 2 Closed (no re-open).
- `training_admission`: 0 Pending/Registered → 1 Present / 2 Absent.

**What's good**
- Two-axis lifecycle (approval then operational status) with an approval audit log.
- Duplicate enrollment blocked; only approved trainings exposed to enrollment.

**What's missing / weak**
- **No capacity/slot limit** — unlimited enrollment.
- **No date validation** — enrollment allowed after start; no `end >= start` check.
- **API doesn't enforce preconditions** — direct calls can enroll into pending trainings, or set attendance on a non-started training (rules are UI-only).
- **Hard delete with no guard** — approved/started/closed trainings can be deleted leaving orphan admissions/sessions.
- `training_program.status` is dead (no session-completion concept); no certificate/completion step.

**Suggestions**
- Add a **max_participants** cap enforced at enroll (server-side) with a waiting-list flag.
- Enforce "enroll only while approved + not started" server-side; validate dates.
- Guard deletes (block if started/closed or cascade-clean admissions).
- Add a completion milestone → auto-generate attendance/certificate report.

---

### 5. Program / Allocation (Assistance) — 5/10

**Process flow**
1. **Create program** (`ADD_PROGRAM`, admin) — agency must exist; asset type Cash (0) or Units (1, product required); `remaining_budget = total_budget`.
2. **Edit program** — budgets/status/remaining manually editable.
3. **Create allocation** (`ADD_ALLOCATION`) — program + branch + amount/qty + max-per-beneficiary (+ product for units). Inserts `reserved = allocated`, `distributed = 0`, **status = 1 (In-progress)**.
4. **Enroll beneficiaries** (`ADD_PROGRAM_BENEFICIARY`) — active beneficiaries not already in the allocation; duplicate + capacity checks; insert `program_beneficiary` (status 0 FOR RELEASE) and `distributed_budget += 1`.
5. **Fulfillment** (`UPDATE_PROGRAM_BENEFICIARY_STATUS`) — RELEASE (0→1) → RECEIVE (1→2, stamps date, sets `recieved_budget = max_per_beneficiary`) or CANCEL (→3). Completion = all received.

**Status lifecycle:**
- `program`: 1 Active / 2 Closed / 3 Paused (free-form).
- `program_allocation`: 0 Pending / 1 In-progress / 2 Processed (new = 1, free-form changes).
- `program_beneficiary`: 0 FOR RELEASE → 1 RELEASED → 2 RECEIVED / 3 CANCELLED.

**What's good**
- Enroll → Release → Receive fulfillment chain with a completion concept.
- Capacity checks exist (`floor(reserved / max_per_beneficiary)`, `allocated − distributed >= 1`).

**What's missing / weak**
- **Budget is never enforced or reconciled** — allocation never checks program budget; the post-enroll recompute queries a non-existent `program_allocation.remaining_budget` column and silently fails; `program.remaining_budget` is only ever edited by hand.
- **`distributed_budget` counts beneficiaries, not money/qty** — "Distributed/Reserved" are slot counts, not pesos/units.
- **No ACID** — enroll + allocation update + budget recompute are separate queries.
- **Cancel loses capacity** — never decrements `distributed_budget`, and cancelled records are skipped on re-enroll.
- **Received beneficiaries can be re-enrolled** (reactivated to status 0) → repeated receipt of same subsidy.
- **No transition validation** — RECEIVE from status 0 or CANCEL from any status allowed at API level.
- **Branch not enforced** — can enroll beneficiaries from other branches.

**Suggestions**
- Add a real **remaining_budget** on allocation; enforce `sum(allocations) <= program.total_budget` at allocation time; reconcile on enroll/receive.
- Track **distributed value** (money or qty) separately from beneficiary count.
- Wrap enroll in a DB transaction; free capacity on cancel; block re-enrollment after RECEIVED.
- Enforce the allocation's branch when listing eligible beneficiaries.

---

### 6. Beneficiary — 5.5/10

**Process flow**
1. **Register** (`ADD_BENEFICIARY`) — username/email/mobile/doc_num required + unique; PDO transaction: create `users` (type=1, status=1) → upload photo → auto-generate 9-digit `doc_num` → insert `beneficiary` with **status = 0 (For Verification)** → create wallet (16-digit Luhn account number).
2. **Verify** (two-step UI) — Step 1 facial scan (placeholder Swal), Step 2 Hi-Card NFC via WebSocket `ws://localhost:8347` → `ctrl-patient.php` `PATIENT_SCAN`.
3. **Status lifecycle** — 0 For Verification → 1 Verified / 2 Inactive / 3 Deactivated.
4. **Edit / Update** — `UPDATE_BENEFICIARY` can set status/doc_num directly (no dedicated verification transaction).
5. **Listing** — branch-scoped for non-admin roles; wallet number shown.

**What's good**
- Onboarding generates a unique doc number + Luhn-valid wallet account automatically.
- Transactional create (users + profile + wallet all-or-nothing).

**What's missing / weak**
- **Verification backend is missing** (`controller/ctrl-patient.php` doesn't exist) → biometric/NFC verify is broken; the only path to "Verified" is editing the record directly.
- **No verification audit** — no who/when record of verification.
- Role ambiguity: beneficiaries created with `role_id=2`, same as Cooperative employee.
- Doc/email/mobile uniqueness only in the add path.

**Suggestions**
- Either implement the verification backend or replace with a documented **manual verification step** (staff marks verified with approver + timestamp, written to a verification log).
- Enforce the 0→1 transition server-side (verify endpoint), not via the edit form.
- Fix the beneficiary role to a distinct `role_id`.

---

### 7. Users / Auth / Profile — 6/10

**Process flow**
1. **Login** (`LOGIN`) — username + password; `password_verify()`; legacy plaintext auto-upgraded to `password_hash`; requires `users.status = 1`; session loads profile from employee/beneficiary.
2. **Logout** — clears session → login.
3. **Add user** (`ADD_USER`) — username/role required, unique username/email/mobile; default password `123456`; creates `users` + `employee` (`reference_no = EMP-{id}`); optional photo.
4. **Edit user / Profile** — `UPDATE_USER` (duplicated in ctrl-users and ctrl-beneficiary): updates `users` + employee/beneficiary profile; location cascade (region→province→city→barangay, NCR/district handling); photo via base64.
5. **Roles** — role access stored as CSV of module IDs; admin-only editing.

**What's good**
- Clean login with legacy-password migration; profile update with full location cascade.

**What's missing / weak**
- **`DELETE_USER` is called but doesn't exist** → delete always fails.
- **`CHANGE_PASSWORD` checks `$_SESSION['user_id']`** but login sets `$_SESSION['users_id']` → self-edit guard broken for real sessions.
- **Silent default password `123456`** for new users.
- Duplicated `UPDATE_USER` logic; inconsistent success codes across controllers (0 vs 1).

**Suggestions**
- Implement `DELETE_USER` or remove the button; fix the session-key bug; require first-login password change; unify success-code convention and single `UPDATE_USER`.

---

### 8. Roles / Access — 6/10

**Process flow**
1. List roles with access counts; detail shows module matrix (`user_modules` allowed flags).
2. Add role (duplicate-title check) → access stored as sorted CSV of module IDs → `UPDATE_ROLE_ACCESS`.
3. Admin-only (`role_id == 1`).

**What's good**
- Module-level permission matrix with a persisted access list.

**What's missing / weak**
- Access model is a CSV string, not relational — hard to query/filter by capability.
- Admin gate depends on session role being reliably set (in dev it's hardcoded).

**Suggestions**
- Normalize role→module access into a join table; build a reusable `can(module, action)` check.

---

## Cross-Cutting Improvements (apply to all modules)

1. **Enforce state transitions server-side.** Today most rules are UI-gated (buttons hidden) but the API accepts invalid transitions (e.g., attendance on non-started training, RECEIVE from status 0, enroll into pending training). The **Booking** module is the model to copy — guard every transition with an expected-current-status check.

2. **Wrap multi-step mutations in DB transactions.** Enroll + allocation update + budget recompute, and similar flows, run as loose sequential queries. Booking already uses transactions; roll it out.

3. **Fix the broken/unwired UI flows** (dead `trans` calls):
   - `product-details.php`: archive/restore, batch actions (reserve/release/adjust/transfer/status), receive-stock field names.
   - `users-list.php`: `DELETE_USER`.
   - `product-details.php`: `UPDATE_PRODUCT_STATUS`.
   - `ctrl-checkout.php`: POS API has no UI caller at all.
   - `GET_REPORT` (11 report types) has no consuming view.

4. **One source of truth per domain.**
   - Selling price: `product_facility_price` → push to batches.
   - Machine availability: derive a single computed field.
   - Budget math: separate *beneficiary counts* from *monetary/quantity values*.

5. **Add the missing terminal/middle steps.**
   - Booking: cancel + overdue tracking.
   - Inventory: return / expired / damaged write-offs; consume reserved on sale.
   - Training: capacity limit.
   - Program: real budget enforcement + reconciliation.

6. **Audit everywhere.** Booking and inventory already log; add logs for beneficiary verification, maintenance jobs, batch status toggles, and program status changes.

---

## Recommended Priority (quick wins first)

| Priority | Item | Effort |
|---|---|---|
| P1 | Fix dead/broken UI→controller wiring (product-details, users delete, receive stock) | Low |
| P1 | Enforce server-side state guards on training admission & program beneficiary transitions | Low |
| P1 | Booking: re-check availability at approval | Low |
| P2 | Booking: cancel flow + stale-pending auto-expiry | Medium |
| P2 | Inventory: consume `reserved_stock` on sale; add return/expired/damaged write-offs | Medium |
| P2 | Program: real `remaining_budget` on allocation + enforcement | Medium |
| P2 | Beneficiary: verification log + server-side 0→1 transition | Medium |
| P3 | Training capacity limit; single price source; POS order header + idempotency | Medium/High |
