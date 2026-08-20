# DAR Web v2 — Development Process & Reference Guide

Complete reference for how this project works: how to **add**, **edit**, **delete**,
**list** and perform custom actions for every module — both in the **web layer**
(the pages under `views/`) and the **API layer** (the controllers under `controller/`).

---

## Table of Contents

1. [Project Overview](#1-project-overview)
2. [How the App Works (Architecture)](#2-how-the-app-works-architecture)
3. [How to Add a Brand-New Module (Recipe)](#3-how-to-add-a-brand-new-module-recipe)
4. [General CRUD Pattern](#4-general-crud-pattern)
5. [Module-by-Module Reference](#5-module-by-module-reference)
   - 5.1 Auth & Profile
   - 5.2 Dashboard
   - 5.3 User Management
   - 5.4 Module Management
   - 5.5 Roles & Permissions
   - 5.6 Agency
   - 5.7 Branch
   - 5.8 Machinery Type
   - 5.9 Machinery
   - 5.10 Machinery Maintenance
   - 5.11 Facility Type
   - 5.12 Facility
   - 5.13 Facility Product Pricing
   - 5.14 Beneficiary
   - 5.15 Location Reference
   - 5.16 Booking
   - 5.17 Program
   - 5.18 Allocation
   - 5.19 Program Beneficiary
   - 5.20 Training
   - 5.21 Land Monitoring (Parcel / Record / Logs)
   - 5.22 Product & Category
   - 5.23 Inventory
   - 5.24 Checkout / Store Sale
   - 5.25 Inventory Simulation
   - 5.26 Wallet
   - 5.27 Logs (Audit)
6. [Database Tables](#6-database-tables)
7. [Conventions & Gotchas](#7-conventions--gotchas)

---

## 1. Project Overview

A plain-PHP (no framework) admin web application for the **Department of Agrarian
Reform (DAR)**. It manages beneficiaries (ARBs), cooperatives/branches, machinery
rentals (booking), facilities, training, programs & allocations, land parcels and
monitoring, products/inventory (with FEFO selling), wallets, and audit logs.

| Item | Detail |
|---|---|
| Language | PHP 8 (procedural + some classes), MySQL (mysqli + PDO) |
| Frontend | jQuery + AJAX + Bootstrap 4/5 admin template (Mophy/DexignZone) |
| Routing | Single entry point `index.php` + `.htaccess` rewrite |
| Config | `.env` file (APP_URL, DB credentials) |
| Layout | Blade-like `@section`/`@yield` emulation via `sections.php` |

### Folder structure

```
dar-webv2/
├── index.php          # Front controller (routing, session, module menu load)
├── sections.php       # startSection/endSection/yieldSection helpers
├── .env               # APP_URL + DB connection settings
├── .htaccess          # Rewrites all URLs to index.php
├── controller/        # API layer (JSON endpoints), one file per module
├── views/             # Web layer (pages rendered by index.php)
├── model/             # Helper classes (Booking, GblFn, InventoryEngine, Wallet)
├── config/dbcon.php   # PDO singleton (used by wallet/beneficiary)
├── assets/            # CSS/JS/images/uploads
└── dar_dbv4.sql       # Database dump
```

---

## 2. How the App Works (Architecture)

### 2.1 Request flow

1. Browser hits `http://localhost/dar-webv2/<route>`.
2. `.htaccess` rewrites everything that is not a real file/folder to `index.php`.
3. `index.php`:
   - Starts the session.
   - Loads `.env`.
   - If logged in and `user_modules` not yet cached, loads the module tree +
     role access into `$_SESSION`.
   - Splits the URL, resolves `$route`.
   - Guards protected routes (redirects to `login` if `IS_LOGIN` is false).
   - `switch ($route)` includes the matching view file from `views/`.
   - After the view, includes `views/layout.php` to render the page shell.

### 2.2 Web layer (views)

Every view is a PHP file that declares **sections** which `layout.php` then
renders. Each page uses this shape:

```php
<?= startSection('css') ?>      <!-- page-specific <style>/<link> -->
...
<?= endSection() ?>

<?= startSection('content') ?>  <!-- the page HTML -->
...
<?= endSection() ?>

<?= startSection('scripts') ?>  <!-- jQuery/JS, incl. AJAX calls -->
...
<?= endSection() ?>
```

`sections.php` provides: `startSection($name)`, `endSection()`, `yieldSection($name)`.

Pages load their data and submit forms through **AJAX calls to the controllers**
(see `views/users-list.php` for the canonical pattern):

```js
$.ajax({
    url: baseURL + "controller/ctrl-<module>.php",
    type: "POST",
    contentType: "application/json",
    dataType: "json",
    data: JSON.stringify({ trans: "LIST_XXX", ...params }),
    success: function(res) {
        if (res.code == 0) { /* render res.data */ }
        else { Swal.fire("Error", res.message, "error"); }
    }
});
```

`showLoader()` / `closeLoader()` (defined in `layout.php`) show/close a
SweetAlert2 loading dialog.

### 2.3 API layer (controllers)

Each controller is a **JSON endpoint** that switches on a `trans` (transaction)
value. It accepts the request as JSON body (`php://input`), `$_POST`, or `$_GET`:

```php
$data = json_decode(file_get_contents("php://input"), true);
$trans = $_POST['trans'] ?? $_GET['trans'] ?? ($data['trans'] ?? '');

if ($trans == "ADD_X") { ... echo json_encode(["code"=>0, "message"=>"...", "data"=>...]); }
else if ($trans == "LIST_X") { ... }
// etc.
else { echo json_encode(["code"=>1, "message"=>"Invalid transaction"]); }
```

**Response convention:** `code = 0` means success, `code = 1` means error.
Most responses also include `message` and optional `data`.

DB access:
- Most controllers use `mysqli $conn` from `controller/connect.php` (reads `.env`).
- `ctrl-wallet.php`, `ctrl-beneficiary.php` (and the models) use PDO
  `config/dbcon.php` singleton (`DBCon::getConnection()`).

### 2.4 Menu / permissions system

The sidebar (`views/navbar_1.php`) is **not hard-coded**. It renders from DB:

- Table `user_modules` — the module tree. Fields: `id`, `parent_id`, `title`,
  `icon`, `page` (the route/URL), `filename`, `sort_order`, `is_menu`, `status`.
- Table `users_role` — a role has `access` = comma-separated module IDs.
- On login (`ctrl-auth.php`) and on module changes (`ctrl-module.php`), the
  session is populated with `user_modules` and `role_access`.
- `navbar_1.php` shows only modules the current role can access
  (`role_access`). A role with an empty access array acts as a super admin
  (sees everything).

**To give a role access to a page you must** add the module row in
`user_modules`, then tick it in the role editor (`views/user-role.php`, uses
`UPDATE_ROLE_ACCESS`).

---

## 3. How to Add a Brand-New Module (Recipe)

Complete end-to-end checklist to add a module (e.g. "Inventory Locations").

### Step 1 — Database table

Create the table in MySQL (add to `dar_dbv4.sql` too). Follow existing style:
`id` PK auto-increment, `created_at`/`updated_at` timestamps, `status` flag.

```sql
CREATE TABLE `inventory_location` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `details` TEXT NULL,
  `status` TINYINT NOT NULL DEFAULT '1',
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`)
);
```

### Step 2 — Register the module (menu + access)

Insert a row into `user_modules` (do this via the Modules page at route
`module-list`, or directly in SQL):

```sql
INSERT INTO user_modules (parent_id, title, icon, page, filename, sort_order, is_menu, status, created_at, updated_at)
VALUES (0, 'Inventory Location', 'fas fa-map-marker-alt', 'inventory-location', 'inventory-location.php', 99, 1, 1, NOW(), NOW());
```

- `page` is the route used in the URL (e.g. `inventory-location`).
- `filename` is the view file name (e.g. `inventory-location.php`).
- `parent_id = 0` for a top-level menu; use a parent module id for a submenu.
- `is_menu = 1` shows it in the sidebar.

Then assign the module to roles in **User Roles** (route `user-roles`).

### Step 3 — Create the controller (API)

Create `controller/ctrl-inventory-location.php`:

```php
<?php
header("Content-Type: application/json");
include __DIR__ . '/connect.php';

$data = json_decode(file_get_contents("php://input"), true);
$trans = $_POST['trans'] ?? $_GET['trans'] ?? ($data['trans'] ?? '');

if ($trans == "ADD_LOCATION") {
    $name = mysqli_real_escape_string($conn, $data['name'] ?? '');
    if ($name == '') { echo json_encode(["code"=>1,"message"=>"Name required"]); exit; }
    mysqli_query($conn, "INSERT INTO inventory_location (name, details, status, created_at, updated_at)
                         VALUES ('$name', '".($data['details']??'')."', 1, NOW(), NOW())");
    echo json_encode(["code"=>0,"message"=>"Added","data"=>["id"=>mysqli_insert_id($conn)]]);
}
else if ($trans == "LIST_LOCATION") { /* SELECT ... */ }
else if ($trans == "GET_LOCATION") { /* SELECT ... WHERE id=... */ }
else if ($trans == "UPDATE_LOCATION") { /* UPDATE ... */ }
else if ($trans == "DELETE_LOCATION") { /* DELETE ... */ }
else { echo json_encode(["code"=>1,"message"=>"Invalid transaction"]); }
```

### Step 4 — Create the view (web page)

Create `views/inventory-location.php` with the section pattern (list table +
Add/Edit modal + Delete confirm). List/load via `LIST_LOCATION`, save via
`ADD_LOCATION` / `UPDATE_LOCATION`, delete via `DELETE_LOCATION`.

### Step 5 — Register the route in `index.php`

Add a case to the switch in `index.php`:

```php
// ==================== Inventory Location ====================
case 'inventory-location':
    require_once 'views/inventory-location.php';
    break;
```

### Step 6 — Wire the menu / test

- Log in as a role that has the module assigned (or super admin).
- Confirm the sidebar shows the new menu item and the page loads.
- Confirm Add / Edit / Delete work against the API.

---

## 4. General CRUD Pattern

Every module follows the same five core API transactions:

| Transaction | HTTP intent | Typical params | Behavior |
|---|---|---|---|
| `ADD_*` | Create | the entity fields | validates, inserts, returns new `id` |
| `LIST_*` | Read (collection) | optional filters | returns `data` array |
| `GET_*` / `*_DETAIL` | Read (single) | `id` | returns one row in `data` |
| `UPDATE_*` / `EDIT_*` | Update | `id` + fields | updates row |
| `DELETE_*` | Delete | `id` | deletes (some soft-delete via `status=0`) |

Corresponding web pages are usually `*-list`, `*-add`, `*-edit`, `*-view`
routes mapped in `index.php`.

---

## 5. Module-by-Module Reference

> API URL for every module = `{APP_URL}/controller/ctrl-<name>.php`
> (called with JSON `{ trans: "..." }`).
> Web route = `{APP_URL}/<route>` → view file listed.

### 5.1 Auth & Profile

**Web pages**
| Route | View file | Purpose |
|---|---|---|
| `login` | `views/page-login.php` | Login form |
| `page-register` | `views/page-register.php` | Registration |
| `page-forgot-password` | `views/page-forgot-password.php` | Forgot password |
| `page-lock-screen` | `views/page-lock-screen.php` | Lock screen |
| `profile` | `views/profile.php` | Current user profile |
| `logout` | (handled in index.php) | Clears session |

**API — `controller/ctrl-auth.php`**
| Transaction | Params | Purpose |
|---|---|---|
| `LOGIN` | `username`, `password` | Authenticates, sets session (`IS_LOGIN`, `user_id`, `role_id`, `type`, `profile`, `user_modules`, `role_access`). Upgrades legacy plaintext passwords. |
| `LOGOUT` | — | Destroys session |
| `CHANGE_PASSWORD` | `id`, `password` (min 6 chars) | Updates password; only self or admin |

**API — `controller/ctrl-users.php`** (used by profile too)
| Transaction | Params | Purpose |
|---|---|---|
| `GET_USER` | `id` | Returns user + role + profile (employee or beneficiary) |
| `UPDATE_USER` | `id`, `username`, `password`?, `status`, profile fields, optional base64 `profile` photo | Updates user/personal info |
| `GET_ALL_USER_ROLE` | — | Active roles except beneficiary role |
| `GET_ALL_FACILITY` | — | Facilities with their types |

**API — `controller/ctrl-location.php`** (address dropdowns, read-only)
Transactions: `region`, `province` (`code`), `city` (`code`), `ncr_city`,
`district`, `barangay` (`code`), `barangay_district` (`code`).

---

### 5.2 Dashboard

**Web pages**
| Route | View file |
|---|---|
| `home`, `dashboard` | `views/home.php` |

**API — `controller/ctrl-dashboard.php`**
| Transaction | Purpose |
|---|---|
| `GET_DASHBOARD_COUNTS` | All dashboard metrics (machine/booking/facility/program/training counts, inventory, booking breakdown & trend, machine availability, low stock, pending bookings, recent logs). Role-scoped (roles 1/2/3). |

**API — `controller/ctrl-dashboard-v2.php`** (beneficiary dashboard)
| Transaction | Purpose |
|---|---|
| `GET_DASHBOARD_V2_COUNTS` | Beneficiary status counts, branch count, program count |
| `LIST_BENEFICIARY_V2` | Verified beneficiary list |
| `GET_BENEFICIARY_DETAILS` | Single beneficiary profile (`id`) |
| `GET_BENEFICIARY_BOOKINGS` | Beneficiary bookings (`id`) |
| `GET_BENEFICIARY_TRAININGS` | Beneficiary trainings (`id`) |

> Note: there is no `dashboard-v2` web page yet — only the controller exists.

---

### 5.3 User Management

**Web pages**
| Route | View file |
|---|---|
| `user-list` | `views/users-list.php` |
| `add-user` | `views/users-add.php` |
| `edit-user` | `views/users-edit.php` |
| `view-user` | `views/users-view.php` |

**API — `controller/ctrl-users.php`**
| Transaction | Params | Purpose |
|---|---|---|
| `ADD_USER` | `username`, `password` (default `123456`), `role_id`, personal info, address ids, optional base64 `profile` | Creates `users` row + `employee` row (type 0). Validates duplicate username/email/mobile. |
| `LIST_USER` | — | Lists admin/employee users (type 0) with profile |
| `LIST_USER_ROLE` | `role_id`? | Lists users filtered by role |
| `GET_USER` | `id` | Single user detail |
| `UPDATE_USER` | `id`, `username`, `password`?, `status`, profile fields, base64 `profile` | Updates user + employee/beneficiary |
| `DELETE_USER` | `id` | Deletes user + employee/beneficiary |
| `GET_INVOICE_DATA` | `beneficiary_id`, `cooperative_id` | Invoice header data |
| `GET_ALL_USER_ROLE` | — | Roles dropdown |
| `GET_ALL_FACILITY` | — | Facilities dropdown |

---

### 5.4 Module Management

**Web pages**
| Route | View file |
|---|---|
| `module-list` | `views/module-list.php` (nestable drag-drop menu editor) |

**API — `controller/ctrl-module.php`**
| Transaction | Params | Purpose |
|---|---|---|
| `ADD_MODULE` | `parent_id`, `title`, `icon`, `page`, `filename`, `sort_order`, `is_menu` | Create module (duplicate title guard) |
| `LIST_MODULE` | — | All modules + parent names |
| `GET_MODULE` | `id` | Single module |
| `UPDATE_MODULE` | `id`, + all add fields, `status` | Update module |
| `REORDER_MODULE` | `order` array `[{id, parent_id}]` | Drag-drop reorder (writes `sort_order` per parent) |
| `DELETE_MODULE` | `id` | Soft delete (`status=0`) |

All module transactions refresh the session module snapshot
(`refreshModuleSession`) so sidebar changes appear immediately.

---

### 5.5 Roles & Permissions

**Web pages**
| Route | View file |
|---|---|
| `user-roles` | `views/user-role.php` |

**API — `controller/ctrl-role.php`**
| Transaction | Params | Purpose |
|---|---|---|
| `LIST_USER_ROLES` | — | All roles + access count + status label |
| `GET_ROLE_DETAIL` | `id` | Role + module tree with `allowed` flags |
| `LIST_MODULES` | — | Module tree for add-role editor |
| `UPDATE_ROLE_ACCESS` | `id`, `title`, `description`, `status`, `access` array | Save role + checked module ids |
| `ADD_ROLE` | `title`, `description`, `status`, `access` array | Create role (duplicate title guard) |

---

### 5.6 Agency

**Web pages:** `agency` → `views/agency.php`

**API — `controller/ctrl-agency.php`** (table: `agency`)
| Transaction | Params | Purpose |
|---|---|---|
| `ADD_AGENCY` | `code`?, `name`, `details` | Create; auto-generates code from initials if blank |
| `EDIT_AGENCY` | `id`, `code`, `name`, `details` | Update |
| `DELETE_AGENCY` | `id` | Delete |
| `LIST_AGENCY` | — | List all |

---

### 5.7 Branch

**Web pages:** `branch-list` → `views/branch-list.php`

**API — `controller/ctrl-branch.php`** (table: `branch`)
| Transaction | Params | Purpose |
|---|---|---|
| `ADD_BRANCH` | `name`, `details` | Create; auto-generates sequential `B###` code |
| `EDIT_BRANCH` | `id`, `code`, `name`, `details` | Update |
| `DELETE_BRANCH` | `id` | Delete |
| `LIST_BRANCH` | — | List all |

---

### 5.8 Machinery Type

**Web pages:** `machine-types` → `views/machine-types.php`

**API — `controller/ctrl-machine-type.php`** (table: `machinery_type`)
| Transaction | Params | Purpose |
|---|---|---|
| `ADD_MACHINERY_TYPE` | `name`, `details` | Create |
| `LIST_MACHINERY_TYPE` | `id`?, `page`, `limit` | Paginated list (or single) |
| `EDIT_MACHINERY_TYPE` | `id`, `name`?, `details`? | Partial update |
| `DELETE_MACHINERY_TYPE` | `id` | Delete |

---

### 5.9 Machinery

**Web pages:** `machine-list` → `views/machine-list.php`

**API — `controller/ctrl-machinery.php`** (tables: `machinery`, `machinery_images`, ...)
| Transaction | Params | Purpose |
|---|---|---|
| `LIST_MACHINERY_TYPE` | `id`?, `page`, `limit` | Machinery-type list proxy |
| `LIST_MACHINERY` | `employee_id`? | List with branch/type/primary image; scoped to employee's branch |
| `ADD_MACHINERY` | `branch_id`, `type_id`, `name`, `model`, `daily_rate`, `description`, `status`, `images[]` (base64) | Create + upload images |
| `UPDATE_MACHINERY` | `id`, + fields, `images[]` | Update; replaces images if new ones sent |
| `GET_MACHINERY_IMAGES` | `id` | List images |
| `DELETE_MACHINERY_IMAGE` | `id`, `name` | Remove one image + file |
| `DELETE_MACHINERY` | `id` | Delete (blocked if active bookings); removes images + maintenance |

---

### 5.10 Machinery Maintenance

**Web pages:** `machine-maintenance` → `views/machine-maintenance.php`

**API — `controller/ctrl-machinery-maintenance.php`**
(tables: `machinery_maintenance`, `machinery`)
| Transaction | Params | Purpose |
|---|---|---|
| `ADD_MAINTENANCE` | `machinery_id`, `emp_id`, `facility_id`, `type`, `priority`, `start_date`, `end_date`, `labor_cost`, `parts_cost`, `odometer_reading`, `description` | Schedule maintenance (status 1); flips machinery to unavailable (0) |
| `UPDATE_MAINTENANCE_STATUS` | `id`, `status` | Update status; status 3 (completed) sets machinery back to available |
| `LIST_MAINTENANCE` | `facility_id`?, `machinery_id`? | List with labels + auto status |

---

### 5.11 Facility Type

**Web pages:** `facility-type` → `views/facility-type.php`

**API — `controller/ctrl-facility-type.php`** (table: `facility_type`)
| Transaction | Params | Purpose |
|---|---|---|
| `LIST_FACILITY_TYPE` | `id`?, `page`, `limit` | Paginated list |
| `ADD_FACILITY_TYPE` | `name`, `details` | Create (duplicate-name guard) |
| `UPDATE_FACILITY_TYPE` | `id`, `name`?, `details`? | Partial update |
| `DELETE_FACILITY_TYPE` | `id` | Delete |

---

### 5.12 Facility

**Web pages**
| Route | View file |
|---|---|
| `list-facility` | `views/facility-list.php` |
| `add-facility` | `views/facility-add.php` |
| `edit-facility` | `views/facility-edit.php` |

**API — `controller/ctrl-facility.php`** (tables: `facility`, `facility_type`, `employee`, `branch`)
| Transaction | Params | Purpose |
|---|---|---|
| `LIST_FACILITY` | `page`, `limit` | Paginated list with types; role-scoped (role 2 sees own facility) |
| `GET_FACILITY` | `id` | Single facility + types + branch + assigned employee |
| `ADD_FACILITY` | `branch_id`, `type_ids` (array→CSV), `employee_id`?, `name`, `phone`, `email`, address fields, `location_lat/lng`, `operating_hours`, `status` | Create; optionally assigns an employee |
| `UPDATE_FACILITY` | `id` + all add fields | Update + reassign employee |
| `DELETE_FACILITY` | `id` | Delete |
| `GET_FACILITY_BY_EMPLOYEE` | `employee_id` | Facility assigned to an employee |
| `LIST_FACILITY_BY_TYPE` | `facility_type` (CSV ids) | Facilities matching types |

---

### 5.13 Facility Product Pricing

**Web pages**
| Route | View file |
|---|---|
| `facility-prices` | `views/facility-prices.php` |
| `price-history` | `views/price-history.php` |

**API — `controller/ctrl-product-price.php`**
(tables: `product_facility_price`, `product_price_history`, `product_inventory`)
| Transaction | Params | Purpose |
|---|---|---|
| `SET_FACILITY_PRICE` | `product_id`, `facility_id`, `selling_price`, `minimum_price`, `maximum_price`, `created_by`, `remarks`, `effective_date` | Upsert price band; appends price history; syncs batch `selling_price` |
| `LIST_FACILITY_PRICES` | `product_id`?, `facility_id`? | Current price bands per product/facility |
| `GET_PRICE_HISTORY` | `product_id`, `facility_id`, `limit` (200) | Append-only selling price history |
| `GET_FACILITY_PRICE` | `product_id`, `facility_id` | Active selling price |

---

### 5.14 Beneficiary

**Web pages**
| Route | View file |
|---|---|
| `beneficiary-list` | `views/beneficiary-list.php` |
| `beneficiary-verify-list` | `views/beneficiary-verify-list.php` |
| `beneficiary-verify` | `views/beneficiary-verify.php` |
| `beneficiary-view` | `views/beneficiary-view.php` |
| `beneficiary-add` | `views/beneficiary-add.php` |
| `beneficiary-edit` | `views/beneficiary-edit.php` |

**API — `controller/ctrl-beneficiary.php`** (tables: `users`, `beneficiary`, `wallet`, `wallet_logs`, `employee`, `facility`, `branch`)
| Transaction | Params | Purpose |
|---|---|---|
| `ADD_BENEFICIARY` | `username`, `password` (default `123456`), email/mobile, `doc_num`?, `profile`, `branch_id`, personal + address fields | Creates user (role 3), beneficiary, and wallet (auto 9-digit doc_num). Uses PDO transaction. |
| `LIST_BENEFICIARY` | `status`? (`UNVERIFIED`/`VERIFIED`) | List with branch/facility; scoped by session branch for non-admin |
| `GET_BENEFICIARY` | `id` | Single beneficiary detail |
| `UPDATE_USER` | `id`, `username`, `password`?, `status`, personal/address fields | Update user + employee OR beneficiary |
| `UPDATE_BENEFICIARY` | `id` + all beneficiary fields, `branch_id`, base64 `profile` | Transactional update of user + beneficiary |
| `GET_INVOICE_DATA` | `beneficiary_id`, `cooperative_id` | Invoice data |
| `GET_ALL_USER_ROLE` | — | Roles dropdown |
| `GET_COOPERATIVE_FACILITY` | — | Facilities with types |
| `LIST_BRANCH` | — | Branches dropdown |

---

### 5.15 Location Reference

Used as address dropdowns by user/beneficiary/facility/training forms.

**API — `controller/ctrl-location.php`** (tables: `region`, `province`, `municipality`, `district`, `barangay`)

| Transaction | Params | Returns |
|---|---|---|
| `region` | — | All regions |
| `province` | `code` (region) | Provinces of a region |
| `city` | `code` (province) | Municipalities of a province |
| `ncr_city` | — | NCR municipalities |
| `district` | — | All districts |
| `barangay` | `code` (municipality) | Barangays of a city/municipality |
| `barangay_district` | `code` (district) | Barangays of a sub-municipality |

---

### 5.16 Booking

**Web pages**
| Route | View file |
|---|---|
| `booking-browse` | `views/booking-browse.php` |
| `booking-beneficiary` | `views/booking-beneficiary.php` |
| `booking-available` | `views/booking-available.php` |
| `booking-training` | `views/booking-training.php` |
| `booking-approval` | `views/booking-approval.php` |
| `booking-completed` | `views/booking-completed.php` |
| `booking-declined` | `views/booking-declined.php` |
| `booking-list` | `views/booking-list.php` |

**API — `controller/ctrl-booking.php`**
(tables: `booking`, `booking_schedules`, `booking_logs`, `machinery`, `machinery_images`, `machinery_reviews`, `beneficiary`, `branch`)
| Transaction | Params | Purpose |
|---|---|---|
| `LIST_AVAILABLE_BOOKINGS` | — | Machinery not under active booking |
| `LIST_AVAILABLE_MACHINERY` | `branch_id`? | Available machinery (role-scoped) |
| `ADD_BOOKING` | `beneficiary_id`, `machinery_id`, `branch_id`, `start_date`, `end_date`, `booked_by`, `total_days`, `unit_price`, `total_cost` | Create booking (checks schedule conflicts); generates booking number; inserts log + schedule |
| `LIST_BOOKING_APPROVAL` | — | Pending bookings (status 0) |
| `GET_BOOKING_APPROVAL_DETAIL` | `booking_id` | Full detail + schedule |
| `APPROVE_BOOKING` | `booking_id`, `users_id` | Approve (status 1) |
| `DECLINE_BOOKING` | `booking_id`, `users_id`, `remarks` | Decline (status 4) |
| `CHECKOUT_BOOKING` | `booking_id`, `users_id` | Check out (status 2) |
| `LIST_RETURN_BOOKING` | — | Bookings in statuses 1/2/3 |
| `RETURN_BOOKING` | `booking_id`, `users_id` | Return (status 3) |
| `LIST_BOOKING_DECLINED` | — | Declined bookings |
| `LIST_BENEFICIARY_BOOKING` | (session user) | Current user's bookings |
| `LIST_BOOKING` | — | All bookings with labels |
| `GET_MACHINERY_IMAGES` | `id` | Machinery images |
| `LIST_BOOKED_DATES` | `machinery_id` | Booked date ranges |
| `LIST_MACHINERY_REVIEWS` | `machinery_id` | Approved reviews + average |
| `SUBMIT_MACHINERY_RATING` | `booking_id`, `machinery_id`, `rating`, `comment` | Rate a completed booking; recomputes machinery average |

Booking statuses: 0 pending, 1 approved, 2 checked out, 3 returned, 4 declined.

---

### 5.17 Program

**Web pages**
| Route | View file |
|---|---|
| `list-programs` | `views/program-list.php` |
| `add-program` | `views/program-add.php` |

**API — `controller/ctrl-program.php`** (tables: `program`, `agency`, `product`)
| Transaction | Params | Purpose |
|---|---|---|
| `ADD_PROGRAM` | `name`, `agency_id`, `total_budget`, `remaining_budget`, `start_date`, `end_date`, `asset_type`, `product_id` | Create; auto code `PRG-YYYYMMDD-####`; status 1 |
| `EDIT_PROGRAM` | `id`, + fields, `status` | Update |
| `UPDATE_STATUS_PROGRAM` | `id`, `status` | Status-only update |
| `DELETE_PROGRAM` | `id` | Hard delete |
| `LIST_PROGRAM` | — | All programs + agency/product names + status text |

---

### 5.18 Allocation

**Web pages**
| Route | View file |
|---|---|
| `list-allocations` | `views/program-allocation.php` |
| `add-allocation` | `views/program-allocation-add.php` |
| `edit-allocation` | `views/program-allocation-edit.php` |

**API — `controller/ctrl-allocation.php`**
(tables: `program_allocation`, `program`, `branch`, `product`)
| Transaction | Params | Purpose |
|---|---|---|
| `ADD_ALLOCATION` | `subsidy_type` (0 money / 1 product), `program_id`, `branch_id`, `product_id`, `allocation_budget` or `allocated_quantity`, `unit_subsidy_value`, `max_per_beneficiary` | Create allocation |
| `LIST_ALLOCATION` | `status`? | List + type/status labels |
| `GET_ALLOCATION` | `id` | Single allocation |
| `EDIT_ALLOCATION` | `id`, `allocated_budget`, `unit_subsidy_value`, `max_per_beneficiary` | Update; recomputes reserved budget |
| `EDIT_STATUS` | `id`, `status` | Status update (0/1/2) |

---

### 5.19 Program Beneficiary

**Web pages:** `list-program-beneficiary` → `views/program-beneficiary.php`

**API — `controller/ctrl-program-beneficiary.php`**
(tables: `program_beneficiary`, `program_allocation`, `program`, `beneficiary`, `product`, `branch`)
| Transaction | Params | Purpose |
|---|---|---|
| `ADD_PROGRAM_BENEFICIARY` | `allocation_id`, `beneficiary_ids` array | Bulk enroll with capacity/budget checks; updates allocation + program budget |
| `LIST_PROGRAM_BENEFICIARY` | `allocation_id` | Enrolled list |
| `LIST_BENEFICIARY` | `allocation_id` | Eligible (not yet enrolled) beneficiaries |
| `GET_PROGRAM_ALLOCATION` | `allocation_id` | Allocation detail |
| `GET_PROGRAM_BENEFICIARY` | `id` | Single enrollment |
| `UPDATE_PROGRAM_BENEFICIARY_STATUS` | `id`, `action` (RELEASE/RECEIVE/CANCEL) | Lifecycle; RECEIVE stamps `date_received` + `recieved_budget` |
| `DELETE_PROGRAM_BENEFICIARY` | `id` | Remove enrollment |

Enrollment statuses: 0 ENROLLED, 1 RELEASED, 2 RECEIVED, 3 CANCELLED.

---

### 5.20 Training

**Web pages**
| Route | View file |
|---|---|
| `list-training` | `views/training-list.php` |
| `add-training` | `views/training-add.php` |
| `training-admission` | `views/training-admission.php` |
| `training-available` | `views/training-available.php` |

**API — `controller/ctrl-training.php`**
(tables: `training`, `training_program`, `training_admission`, `training_approval`, `beneficiary`, location tables)
| Transaction | Params | Purpose |
|---|---|---|
| `ADD_TRAINING` | `accreditation_no/date`, `title`, `summary`, `objectives`, `start_at`, `end_at`, `program_type`, address ids; files `promotional_image`, `discussion_file` | Create (approval_status 0), auto ref `TRN-<time>` |
| `EDIT_TRAINING` | `id` + add fields | Update (replaces files if provided) |
| `DELETE_TRAINING` | `id` | Hard delete |
| `GET_TRAINING` | `id` | Single training + location names |
| `LIST_TRAINING` | — | All trainings + counts |
| `APPROVE_TRAINING` | `id`, `reason` | Approve (status 1) + log |
| `REJECT_TRAINING` | `id`, `reason` | Reject (status 2) + log |
| `START_TRAINING` | `id` | status 1 (active) |
| `CLOSE_TRAINING` | `id` | status 2 (closed) |
| `LIST_APPROVAL_LOG` | — | Approval/rejection history |
| `ADD_PROGRAM` | `training_id`, `topic`, `speaker`, `time_start`, `time_end` | Add session (overlap-checked) |
| `EDIT_PROGRAM` | `id`, `training_id`, `topic`, `speaker`, `time_start`, `time_end` | Edit session |
| `DELETE_PROGRAM` | `id` | Delete session |
| `GET_TRAINING_PROGRAMS` | `training_id` | Sessions list |
| `ADD_ADMISSION` | `training_id`, `beneficiary_id`, `doc_num` | Single enrollment |
| `ADD_ADMISSIONS` | `training_id`, `beneficiary_ids` array | Bulk enrollment |
| `ENROLL_TRAINING` | `training_id`, `beneficiary_id` | Self-service enrollment |
| `LIST_ADMISSION` | — | All admissions |
| `GET_ADMISSION` | `id` | Single admission |
| `UPDATE_ADMISSION_STATUS` | `id`, `status` | Attendance (status 1 stamps `attendance_at`) |
| `LIST_TRAINING_ADMISSIONS` | — | Admissions for started/closed trainings + counts |
| `GET_TRAINING_ADMISSIONS` | `training_id` | Admissions of one training |
| `LIST_AVAILABLE_TRAINING` | `beneficiary_id` | Approved, open trainings for enrollment |

---

### 5.21 Land Monitoring (Parcel / Record / Logs)

**Web pages**
| Route | View file |
|---|---|
| `list-land-monitoring` | `views/land-monitoring-logs.php` |
| `list-land-parcel` | `views/land-monitoring-parcel.php` |
| `add-land-parcel` | `views/land-monitoring-parcel-add.php` |
| `edit-land-parcel` | `views/land-monitoring-parcel-edit.php` |
| `list-land-records` | `views/land-monitoring-record.php` |
| `add-land-records` | `views/land-monitoring-record-add.php` |
| `edit-land-records` | `views/land-monitoring-record-edit.php` |

**API — `controller/ctrl-land-parcel.php`** (tables: `cocrom_land_parcels`, `beneficiary`)
| Transaction | Params | Purpose |
|---|---|---|
| `ADD_LAND_PARCEL` | `beneficiary_id`, `title_number`, `total_area_hectares`, `latitude`, `longitude`, `land_use_type`, `productivity_score`, `last_survey_date` | Create parcel |
| `EDIT_LAND_PARCEL` | `id` + fields | Update parcel |
| `LIST_LAND_PARCEL` | — | All parcels + beneficiary |
| `GET_PARCEL_DETAIL` | `id` | Parcel + certificates + monitoring logs with images |

**API — `controller/ctrl-land-records.php`** (tables: `cocrom_records`, `cocrom_land_parcels`, `beneficiary`)
| Transaction | Params | Purpose |
|---|---|---|
| `ADD_RECORD` | `land_parcel_id`, `beneficiary_id`, `credit_limit`, `certificate_status`, `issued_date`, `expiry_date`, `encrypted_signature` | Create certificate/record |
| `EDIT_RECORD` | `id` + fields | Update record |
| `LIST_RECORDS` | — | All records |
| `GET_RECORD_DETAIL` | `id` | Single record |
| `DELETE_RECORD` | `id` | Delete record |
| `GET_RECORDS_BY_PARCEL` | `land_parcel_id` | Records of a parcel |

**API — `controller/ctrl-land-monitoring.php`**
(tables: `cocrom_land_monitoring`, `cocrom_land_images`, `cocrom_land_parcels`)
| Transaction | Params | Purpose |
|---|---|---|
| `ADD_MONITORING` | `land_parcels_id`, `employee_id`, `log_type`, `title`, `notes`, files `images[]` | Create log + upload images (first = primary) |
| `EDIT_MONITORING` | `id` + fields, `delete_images` CSV, files `new_images[]` | Update log, remove/upload images |
| `LIST_MONITORING` | — | All logs + employee/parcel |
| `GET_MONITORING` | `id` | Single log + images |
| `DELETE_MONITORING` | `id` | Delete log + images |

---

### 5.22 Product & Category

**Web pages**
| Route | View file |
|---|---|
| `list-products` | `views/product-list.php` |
| `add-product` | `views/product-add.php` |
| `edit-product` | `views/product-edit.php` |
| `product-details` | `views/product-details.php` |
| `product-category` | `views/product-category.php` |
| `product-available` | `views/product-available.php` (storefront / checkout) |

**API — `controller/ctrl-products.php`** (tables: `product`, `product_category`, `product_images`, `product_inventory`, `product_facility_price`)
| Transaction | Params | Purpose |
|---|---|---|
| `ADD_PRODUCT` | `category_id`, `name`, `details`, `unit`, `is_hazardous`, `status`, `barcode`, `images[]` | Create; auto SKU; validates duplicate name; uploads images |
| `UPDATE_PRODUCT` | `id`, + fields, `images[]` | Partial update; appends images (first becomes primary if none) |
| `LIST_PRODUCT` | `search`? | List with category, primary image, facility count, total stock |
| `SET_PRIMARY_IMAGE` | `image_id`, `product_id` | Set product primary image |
| `GET_PRODUCT_DETAILS` | `product_id` | Product + images + inventory batches + price bands |

**API — `controller/ctrl-product-category.php`** (table: `product_category`)
| Transaction | Params | Purpose |
|---|---|---|
| `ADD_PRODUCT_CATEGORY` | `name`, `details`, `status` | Create (POST only) |
| `UPDATE_PRODUCT_CATEGORY` | `id`, `name`, `details`, `status` | Update |
| `LIST_PRODUCT_CATEGORY` | — | List all |
| `DELETE_PRODUCT_CATEGORY` | `id` | Soft delete; blocked while products use it |

---

### 5.23 Inventory

**Web pages**
| Route | View file |
|---|---|
| `list-inventory` | `views/product-inventory.php` |
| `add-inventory` | `views/product-inventory-add.php` |
| `stock-movements` | `views/product-stock-movements.php` |
| `low-stock` | `views/product-low-stock.php` |

**API — `controller/ctrl-inventory.php`**
(tables: `product_inventory`, `product_inventory_logs`, `product`, `product_category`, `product_images`, `product_facility_price`, `facility`)
| Transaction | Params | Purpose |
|---|---|---|
| `LIST_INVENTORY` | `employee_id`? | Batch list scoped to employee facility |
| `EDIT_STATUS` | `id`, `status` (0/1) | Activate/deactivate batch |
| `ADD_INVENTORY` / `RECEIVE_INVENTORY` | `facility_id`, `product_id`, `quantity`/`current_stock`, `reserved_stock`, `cost_price`, `selling_price`, `batch_number`, `reorder_level`, `expiry_date`, `storage_location`, `status`, `users_id`/`created_by`, `remarks` | New batch or restock existing; logs action 1 |
| `GET_PRODUCT_IMAGES` | `product_id` | Product images |
| `UPDATE_INVENTORY` | `id`, field set, `users_id` | Edit batch; logs stock/reserve/price changes |
| `GET_INVENTORY_LOGS` | `inventory_id`?, `product_id`?, `facility_id`?, `transaction_type`?, `limit` | Audit trail (action labels) |
| `GET_INVENTORY_DASHBOARD` | — | Totals: products, facilities, units, low stock, expiring |
| `RESERVE_INVENTORY` | `inventory_id`, `quantity`, `users_id`, `remarks` | Reserve stock (action 8) |
| `RELEASE_RESERVATION` | `inventory_id`, `quantity`, `users_id`, `remarks` | Release reservation (action 9) |
| `ADJUST_INVENTORY` | `inventory_id`, `new_stock`, `users_id`, `remarks` | Set absolute stock (action 4) |
| `TRANSFER_INVENTORY` | `source_inventory_id`, `destination_facility_id`, `quantity`, `users_id`, `remarks` | Move stock; creates `-T` batch at destination (action 10 both sides) |
| `GET_SELL_INFO` | `facility_id`, `product_id` | Price + available stock + FEFO batches |
| `SELL_INVENTORY` | `facility_id`, `product_id`, `quantity`, `users_id`/`created_by`, `remarks` | FEFO sale; logs action 2 per batch; returns low-stock list |
| `RETURN_INVENTORY` | `inventory_id`, `quantity`, `users_id`/`created_by`, `remarks` | Add returned stock (action 3) |
| `DAMAGE_INVENTORY` | `inventory_id`, `quantity`, `users_id`/`created_by`, `remarks` | Remove damaged stock (action 6) |
| `MARK_EXPIRED` | `inventory_id`, `quantity`?, `users_id`/`created_by`, `remarks` | Remove expired stock (action 5) |
| `LIST_LOW_STOCK` | `facility_id`? | Batches at/below reorder level |

**API — `controller/ctrl-inventory-report.php`**
| Transaction | Params | Purpose |
|---|---|---|
| `GET_REPORT` | `report` + (`facility_id`, `product_id`, `from`, `to`) | Dispatches report type. Values: `product_summary`, `inventory_summary`, `inventory_by_facility`, `inventory_by_product`, `batch_inventory`, `price_comparison`, `price_history`, `inventory_ledger`, `inventory_movement`, `low_stock`, `expiring_products` |

**Shared engine — `model/inventory.php` (`InventoryEngine`)**: `availableStock`,
`getFacilitySellingPrice`, `getFEFOBatches`, `allocateFEFO`,
`getAvailableByProduct`, `getCurrentByProduct`, `assertReservedSafe`.

---

### 5.24 Checkout / Store Sale

**Web pages:** `product-available` → `views/product-available.php`

**API — `controller/ctrl-checkout.php`**
(tables: `store_transactions`, `product_inventory`, `product_inventory_logs`, `product`, `employee`, `beneficiary`)
| Transaction | Params | Purpose |
|---|---|---|
| `PLACE_CHECKOUT` | `beneficiary_id`, `cooperative_id`, `users_id`, `items[]` (`inventory_id`, `product_id`, `qty`), `payment_method` | Full sale: deduct stock (FEFO or per batch), log movements, insert `store_transactions`, commit |
| `GET_CHECKOUT_LOGS` | `reference_id`? | Sale logs (action type 2) |

---

### 5.25 Inventory Simulation

**Web pages:** `simulation` → `views/product-simulation.php`

**API — `controller/ctrl-simulation.php`** (session-only; never writes to DB)
| Transaction | Params | Purpose |
|---|---|---|
| `SIM_GET_STATE` | — | Current session simulation history + stats |
| `SIM_CLEAR` | — | Reset simulation |
| `SIM_PREVIEW` | `type` (BUY/SELL), `facility_id`, `product_id`, `quantity`, `unit_price` (BUY) | Validate + project stock impact |
| `SIM_EXECUTE` | same as PREVIEW | Persist record in session (`$_SESSION['inv_sim']`) |

---

### 5.26 Wallet

**Web pages**
| Route | View file |
|---|---|
| `wallet-list` | `views/wallet-list.php` |
| `beneficiary-view` | `views/beneficiary-view.php` (wallet panel) |
| `profile` | `views/profile.php` (wallet panel) |

**API — `controller/ctrl-wallet.php`** (tables: `wallet`, `wallet_balances`, `wallet_logs`, `beneficiary`, `branch`, `program`)
| Transaction | Params | Purpose |
|---|---|---|
| `LIST_WALLET` | (session role/user) | Wallets role-scoped |
| `GET_WALLET` | `id` OR `beneficiary_id` OR `users_id` | Wallet + balances |
| `GET_WALLET_LOGS` | `id` | Transaction log (0 created, 1 debit, 2 credit, 3 frozen) |
| `LIST_PROGRAMS` | — | Active programs |
| `CASH_IN` | `wallet_id`, `amount`, `program_id`, `balance_type` | Credit wallet; blocks frozen; logs action 2 |
| `GET_WALLET_SUMMARY` | (session role/user) | Aggregate wallet stats |

**Model — `model/wallet.php` (`Wallet`)**: `getRequestMetadata()`,
`generateAccountNumber()` (Luhn 16-digit), `createWallet($beneficiary_id)`,
`updateBalance($walletId, $amount, $action)`.

---

### 5.27 Logs (Audit)

**Web pages:** `logs` → `views/logs.php`

**API — `controller/ctrl-logs.php`**
| Transaction | Params | Purpose |
|---|---|---|
| `LIST_LOGS` | `module`? (`Booking`/`Inventory`/`Pricing`/`Wallet`/`Review`/`Store`) | Merged, newest-first audit feed from all module log tables. Requires login. |

---

## 6. Database Tables

| Table | Used by |
|---|---|
| `users`, `users_role`, `users_token` | Auth, roles, sessions |
| `user_modules` | Sidebar / menu tree |
| `employee`, `beneficiary` | People profiles |
| `agency`, `branch`, `facility`, `facility_type`, `facility_terminal`, `terminal` | Organization master data |
| `region`, `province`, `municipality`, `district`, `barangay` | PSGC location hierarchy |
| `machinery`, `machinery_type`, `machinery_images`, `machinery_maintenance`, `machinery_reviews` | Machinery module |
| `booking`, `booking_logs`, `booking_schedules` | Booking module |
| `program`, `program_allocation`, `program_beneficiary` | Programs module |
| `training`, `training_program`, `training_admission`, `training_approval` | Training module |
| `cocrom_land_parcels`, `cocrom_land_monitoring`, `cocrom_land_images`, `cocrom_records`, `cocrom_records_images` | Land monitoring |
| `product`, `product_category`, `product_images`, `product_facility_price`, `product_price_history` | Products & pricing |
| `product_inventory`, `product_inventory_logs` | Inventory |
| `store_transactions`, `system_treasury`, `service_reviews` | Store/checkout, treasury, reviews |
| `wallet`, `wallet_balances`, `wallet_logs` | Wallet module |

---

## 7. Conventions & Gotchas

### Response format
- Always JSON: `{ "code": 0|1, "message": "...", "data": ... }`.
- `code = 0` success, `code = 1` failure. Front-end checks `res.code == 0`.

### Request format
- Default is **JSON body** (`Content-Type: application/json`). `trans` is read
  from `$_POST`, then `$_GET`, then JSON body — so either works.
- File uploads use `multipart/form-data` (`$_FILES`) — e.g. land monitoring
  images, training files. Most profile/product/machinery images accept base64.

### Image / file uploads
- Profile photos → `assets/images/profile/`
- Monitoring images → `assets/images/monitoring/`
- Products → `assets/images/products/` (via `GblFn::processUpload`)
- Machinery → `assets/images/machinery/`
- Training → `assets/images/training/`

### Two DB drivers in use
- Legacy `mysqli $conn` (`controller/connect.php`) — most controllers.
- PDO `DBCon` (`config/dbcon.php`) — wallet, beneficiary (uses transactions).

### Soft vs hard delete
- Soft delete (set `status = 0`): modules, product categories, users/inventory status.
- Hard delete: agencies, branches, machinery types, programs, training, parcels/records, etc.

### Status conventions (varies by module)
- Users/inventory: `0` inactive/pending, `1` active.
- Booking: `0` pending, `1` approved, `2` checked out, `3` returned, `4` declined.
- Training: approval `0` pending, `1` approved, `2` rejected; status `1` active, `2` closed.
- Program-beneficiary: `0` ENROLLED, `1` RELEASED, `2` RECEIVED, `3` CANCELLED.
- Inventory log action types: 1 Receive, 2 Sale, 3 Return, 4 Adjustment, 5 Expired, 6 Damaged, 7 Price Change, 8 Reserve, 9 Release, 10 Transfer.

### Adding a page always requires touching `index.php`
Every new route must have a `case` in the `switch` in `index.php`, or it will
return the 404 page.

### Menu visibility = `user_modules` + `users_role.access`
A view file alone is not enough to show a page in the sidebar. Add the module
row and assign it to the role (see [Section 3](#3-how-to-add-a-brand-new-module-recipe)).

### Security note
Controllers are a legacy codebase using string-interpolated SQL
(`mysqli_real_escape_string`). Prefer the PDO `DBCon` + prepared statements
for any new code, following the wallet model's pattern.