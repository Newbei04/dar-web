# DAR Web App v2 — Module Test Results

**Test date:** 2026-08-19
**Environment:** Laragon on Windows (PHP 8.3.30, MySQL 8.4.3, Apache 2.4.66)
**App URL:** http://localhost/dar-webv2
**Database:** dar_dbv4 (50 tables), user root, no password
**Test method:** HTTP POST/GET against live controllers + page routes; responses verified (JSON code 0/1) and DB persistence checked. Test users: admin (id=1), coop (2), staff (3), ben1-3 (4-6).

---

## Executive Summary

- **~70 page routes render cleanly** (HTTP 200, no PHP errors/warnings) — except `dashboard-v2` which returns **HTTP 404** (no page file exists; only `controller/ctrl-dashboard-v2.php`).
- **All module add/edit flows verified** — 13 functional bugs were found and **all 13 have been fixed** (see "Bugs Fixed" below). Re-testing after the fixes confirms each previously-broken flow now works.
- **No auth checks** on most controllers (only `ctrl-booking.php` and `ctrl-logs.php` enforce `IS_LOGIN`). **Pervasive SQL injection** via string-interpolated user input. **No CSRF protection.** (Security hardening not part of this fix pass.)

---

## Page Routes (all checked with admin session)

All PASS (HTTP 200, no embedded PHP errors) except:

| Route | Result | Note |
|---|---|---|
| `dashboard-v2` | **404** | No `dashboard-v2.php` page exists; only `ctrl-dashboard-v2.php` (which works). |

Passing routes (71): home, dashboard, user-list, add-user, edit-user, view-user, profile, module-list, user-roles, machine-list, machine-types, machine-maintenance, branch-list, agency, beneficiary-list, beneficiary-verify-list, beneficiary-verify, beneficiary-view, beneficiary-add, beneficiary-edit, facility-type, list-facility, add-facility, edit-facility, booking-browse, booking-beneficiary, booking-available, booking-training, booking-approval, booking-completed, booking-declined, booking-list, list-programs, add-program, list-allocations, add-allocation, edit-allocation, list-program-beneficiary, list-training, add-training, training-admission, training-available, list-land-monitoring, list-land-parcel, add-land-parcel, edit-land-parcel, list-land-records, add-land-records, edit-land-records, list-products, add-product, edit-product, product-details, product-category, product-available, list-inventory, add-inventory, stock-movements, low-stock, simulation, facility-prices, price-history, logs, wallet-list, page-login, page-register, page-forgot-password, page-lock-screen, page-error-400/403/404/500/503.

---

## Module-by-Module Results

### 1. Authentication (`ctrl-auth.php`) — PASS
- LOGIN admin/coop/staff works; wrong password rejected (`code:1`); LOGOUT works.

### 2. Users (`ctrl-users.php`) — PASS (fixed)
| Action | Result |
|---|---|
| LIST_USERS, GET_USER, DELETE_USER, GET_USER_DETAILS | PASS |
| ADD_USER | **PASS** (fixed — works without facility_id/birthday) |
| GET_INVOICE_DATA | **PASS** (fixed) |

### 3. Beneficiary (`ctrl-beneficiary.php`) — PASS (fixed)
| Action | Result |
|---|---|
| LIST_BENEFICIARY (all statuses), VERIFY_BENEFICIARY, DELETE_BENEFICIARY, GET_BENEFICIARY_DETAILS | PASS |
| ADD_BENEFICIARY | **PASS** (fixed — minimal fields now register successfully, wallet auto-created) |
| GET_ALL_USER_ROLE | **PASS** (fixed — valid JSON) |

### 4. Booking (`ctrl-booking.php`) — PASS (fixed)
| Action | Result |
|---|---|
| ADD_BOOKING (with or without branch_id), APPROVE, CHECKOUT, RETURN, DECLINE, LIST_* | PASS |
| ADD_BOOKING for admin (no branch_id) | **PASS** (fixed) |
| SUBMIT_MACHINERY_RATING | Needs beneficiary session profile; "Missing required fields" when called as admin (design limitation). |

### 5. Machinery (`ctrl-machinery.php`) — PASS (fixed)
| Action | Result |
|---|---|
| LIST, GET, ADD_MACHINERY (returns last_id), UPDATE_MACHINERY | PASS |
| DELETE_MACHINERY | **PASS** (fixed — handler added; blocks while active bookings, removes images + maintenance, verifies delete) |

### 6. Machinery Maintenance (`ctrl-machinery-maintenance.php`) — PASS (fixed)
| Action | Result |
|---|---|
| ADD_MAINTENANCE, UPDATE_MAINTENANCE_STATUS, LIST_MAINTENANCE | PASS (persistence verified: status 3, machinery restored to available; response codes now standard — success `code:0`) |

### 7. Facility / Branch / Facility Type / Location — PASS
- ADD_FACILITY, UPDATE_FACILITY, DELETE_FACILITY (persistence verified), GET_FACILITY, GET_FACILITY_BY_EMPLOYEE, LIST_FACILITY_BY_TYPE, branch & facility-type CRUD, ctrl-location city/barangay queries all work.

### 8. Agency (`ctrl-agency.php`) — PASS
- ADD, EDIT, DELETE, LIST all work; ADD/EDIT return `data.id`.

### 9. Programs (`ctrl-program.php`) — PASS (fixed)
| Action | Result |
|---|---|
| ADD_PROGRAM, DELETE_PROGRAM, UPDATE_STATUS_PROGRAM, LIST_PROGRAMS | PASS |
| EDIT_PROGRAM | **PASS** (fixed — works with partial fields; NULL-safe agency_id/product_id/dates; preserves remaining_budget) |

### 10. Allocations & Program Beneficiaries — PASS
- ADD_ALLOCATION (returns id), GET, EDIT, EDIT_STATUS, LIST; ADD_PROGRAM_BENEFICIARY (enrolls, persists), LIST_PROGRAM_BENEFICIARY all work.

### 11. Training (`ctrl-training.php`) — PASS (fixed)
| Action | Result |
|---|---|
| ADD_TRAINING, GET_TRAINING, APPROVE/START/CLOSE, ADD_PROGRAM, GET_TRAINING_PROGRAMS, DELETE_TRAINING, ADD_ADMISSION (duplicates rejected) | PASS |
| ADD_TRAINING via JSON body | **PASS** (fixed) |
| EDIT_TRAINING via JSON body | **PASS** (fixed) |

### 12. Land Parcels / Monitoring / Records (`ctrl-land-*.php`) — PASS (fixed)
| Action | Result |
|---|---|
| LIST land-parcel / monitoring / records, GET_LAND_PARCEL | PASS |
| ADD_LAND_PARCEL | **PASS** (fixed — empty numeric/date fields now NULL-safe) |
| EDIT_LAND_PARCEL | **PASS** (same fix applied) |

### 13. Products & Categories — PASS
- ADD/UPDATE product, GET_PRODUCT_DETAILS, LIST; category ADD/UPDATE/DELETE all work. Duplicate category names allowed (no uniqueness check) — minor.

### 14. Inventory (`ctrl-inventory.php`) — PASS
- ADD_INVENTORY (returns inventory_id), ADJUST (persists), EDIT_STATUS (persists), RESERVE/RELEASE (work on active status=1 batches; correctly reject inactive batches with "Inventory batch not found"), TRANSFER_INVENTORY (persists), stock movements, low-stock all work.

### 15. Inventory Report (`ctrl-inventory-report.php`) — PASS (fixed)
- `inventory_summary` with `facility_id` filter now works (was fatal `Unknown column 'pi.facility_id'`). All report types verified.

### 16. Simulation (`ctrl-simulation.php`) — PASS
- SIM_GET_STATE, SIM_PREVIEW, SIM_EXECUTE, SIM_CLEAR all work.

### 17. Wallet (`ctrl-wallet.php`) — PASS (fixed)
| Action | Result |
|---|---|
| GET_WALLET, LIST wallets | PASS |
| CASH_IN | **PASS** (fixed — `Wallet::getRequestMetadata()`, verified + reverted) |

### 18. Checkout (`ctrl-checkout.php`) — PASS
- PLACE_CHECKOUT works (deducts stock, returns reference_id + beneficiary/cooperative data).

### 19. Roles (`ctrl-role.php`) — PASS (fixed)
| Action | Result |
|---|---|
| ADD_ROLE, GET_ROLE_DETAIL, UPDATE_ROLE_ACCESS (with or without description) | PASS |
| UPDATE_ROLE_ACCESS without description | **PASS** (warning fixed) |

### 20. Modules (`ctrl-module.php`) — PASS (fixed)
- ADD/UPDATE/DELETE work. UPDATE_MODULE without `icon` now works without warnings.

### 21. Logs (`ctrl-logs.php`) — PASS (only controller with IS_LOGIN check)

### 22. Dashboard v2 controller — PASS
- GET_BENEFICIARY_DETAILS works (but no page route for it).

---

## Bugs Found and Fixed (file:line)

All 15 functional bugs below were fixed on 2026-08-19 and re-verified by HTTP tests. ✅ = fix confirmed by re-test.

1. ✅ **ADD_USER failed** — `controller/ctrl-users.php:189,191`. When `facility_id` is empty the SQL was `VALUES ('8', , CONCAT('EMP-','8'), ...)` → fatal syntax error. When `birthday` is empty → fatal `Incorrect date value: '' for column 'birthday'`. **Fix:** emit SQL `NULL` for empty `facility_id` and `birthday`.
2. ✅ **ADD_BENEFICIARY failed** — `controller/ctrl-beneficiary.php:121-125`. Undefined-key warnings for `barangay_id`, `district_id`, `city_id`, `province_id`, `region_id` (used `?:` instead of `??`), registration aborted when `birthday` empty (DATE column got `''`), and validation required `doc_num` which is auto-generated. **Fix:** use `($data['key'] ?? '') ?: null`, NULL-ify empty `birthday`, drop the redundant `doc_num` requirement and its broken pre-check.
3. ✅ **ADD_LAND_PARCEL failed** — `controller/ctrl-land-parcel.php:47-52` (ADD) and 94-107 (EDIT). Fatal `Data truncated for column 'productivity_score'` when numeric/date fields omitted. **Fix:** emit SQL `NULL` for empty `total_area_hectares`, `latitude`, `longitude`, `productivity_score`, `last_survey_date`.
4. ✅ **ADD_BOOKING failed for admin** — `controller/ctrl-booking.php:141`. Fatal `Incorrect integer value: '' for column 'branch_id'` when `branch_id` not supplied (admins have no branch). **Fix:** default empty `branch_id` to `null`.
5. ✅ **CASH_IN / CASH_OUT broken** — `controller/ctrl-wallet.php:277`. Fatal `Cannot access "self" when no class scope is active` — `self::getRequestMetadata()` called outside any class; also `Wallet` model was not included. **Fix:** include `model/wallet.php` and call `Wallet::getRequestMetadata()`. Cash-in now succeeds (verified, then reverted).
6. ✅ **ADD_TRAINING JSON body rejected** — `controller/ctrl-training.php:16-32`. Validation read `$_POST` only, so JSON requests failed with "Accreditation Number and Title are required". **Fix:** read from `$data` (already merged JSON/FormData), default `program_type` to `0`.
7. ✅ **EDIT_TRAINING JSON body rejected** — `controller/ctrl-training.php:501,563`. Empty `program_type` → fatal `Incorrect integer value: ''`. **Fix:** default `program_type` to `0`, add ID-required check.
8. ✅ **EDIT_PROGRAM broken** — `controller/ctrl-program.php:130-144`. Omitted `agency_id` → fatal integer error; omitted `product_id` (null) → SQL syntax error `product_id=,`; empty dates → fatal date error; omitted `remaining_budget` → silently reset budget to 0. **Fix:** NULL-safe SQL for `agency_id`, `product_id`, `start_date`, `end_date`; preserve existing `remaining_budget` when not supplied.
9. ✅ **GET_INVOICE_DATA broken** — `controller/ctrl-users.php:517`. Fatal `Unknown column 'f.employee_id' in 'on clause'`. **Fix:** join `facility f ON f.id = e.facility_id` (the actual FK; `employee.facility_id` exists).
10. ✅ **Inventory report with facility filter broken** — `controller/ctrl-inventory-report.php:82-95`. Fatal `Unknown column 'pi.facility_id'` in the `inventory_summary` subqueries (they lacked the `pi` alias). **Fix:** alias `product_inventory pi` in those subqueries. Verified with `facility_id=1`.
11. ✅ **Machines could not be deleted** — `controller/ctrl-machinery.php`. No `DELETE_MACHINERY` trans existed. **Fix:** added handler that blocks deletion while active bookings exist, removes images from disk/DB, clears maintenance rows, then deletes the machinery. Verified.
12. ✅ **Machinery-maintenance inverted response codes** — `controller/ctrl-machinery-maintenance.php`. Success returned `code:1`, errors `code:0`. **Fix:** flipped every response to the standard convention (success `0`, error `1`). Verified for ADD/LIST/UPDATE.
13. ✅ **GET_ALL_USER_ROLE produced invalid JSON** — `controller/ctrl-beneficiary.php:614`. Handler didn't `exit`, execution fell through into the DELETE_FACILITY block appending a second JSON object. **Fix:** added `exit;` after the response (the misplaced DELETE_FACILITY code was dead and is now served by `ctrl-facility.php`).
14. ✅ **UPDATE_ROLE_ACCESS warning** — `controller/ctrl-role.php:167-168`. Undefined array key `description`/`title`. **Fix:** `trim($data['description'] ?? '')` etc.
15. ✅ **UPDATE_MODULE warning** — `controller/ctrl-module.php:233-236`. Undefined array key `icon` + deprecated `trim(null)`. **Fix:** `trim($data['icon'] ?? '')` etc.
16. ⚠️ **`dashboard-v2` page missing** — still **not fixed** (out of scope): no `dashboard-v2.php` page file exists; route returns HTTP 404. Only the controller `ctrl-dashboard-v2.php` exists and works.

---

## Security Notes

- **No authentication on most controllers** — any unauthenticated caller can invoke ADD/EDIT/DELETE endpoints on users, beneficiaries, programs, inventory, etc. Only `ctrl-booking.php` and `ctrl-logs.php` check `IS_LOGIN`.
- **Pervasive SQL injection** — user input interpolated directly into queries (`$id`, `$name`, etc.) with only occasional `mysqli_real_escape_string`.
- **No CSRF protection**; admin password was reset to `admin123` for testing — restore the original hash before going live.

---

## Test Data Cleanup
All test rows created during verification and fix re-testing were removed (test users, beneficiaries, agencies, facilities, machines, maintenance records, trainings, programs, allocations, bookings, land parcels, inventory transfers, product categories). Orphaned wallets/wallet_logs from deleted test beneficiaries were purged. Product inventory id=1 stock restored to 70; reserved_stock restored to 5; wallet id=1 balance restored to 15000.