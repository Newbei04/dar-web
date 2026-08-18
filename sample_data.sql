-- ============================================================================
--  SAMPLE DATA for `dar_dbv4`
--  Import this file AFTER importing dar_dbv4.sql
--
--  NOTE:  reference tables (barangay, district, municipality, province,
--         region) already contain data inside dar_dbv4.sql and are NOT
--         repeated here.
--
--  Reference codes used (PSGC codes from dar_dbv4.sql):
--   * region code 010000000    = Region I (Ilocos)
--   * province code 012800000  = Ilocos Norte
--   * municipality code 012812000 = City of Laoag
--   * municipality code 012805000 = City of Batac
--   * barangay code 012812001  = Bgy. No. 42, Apaya
--   * barangay code 012812002  = Bgy. No. 36, Araniw
--   * barangay code 012812005  = Bgy. No. 41, Balacad
--   * barangay code 012812006  = Bgy. No. 40, Balatong
--   * barangay code 012805001  = Aglipay (Pob.), City of Batac
--
--  Default password for ALL users = 12345678  (bcrypt hashed)
--   - admin      (role 1 - Admin)
--   - coop       (role 2 - Employee / Cooperative)
--   - ben1..ben3 (role 3 - Beneficiary)
-- ============================================================================

SET FOREIGN_KEY_CHECKS=0;
START TRANSACTION;

-- ---------------------------------------------------------------
-- users_role
-- ---------------------------------------------------------------
INSERT INTO `users_role` (`id`, `title`, `description`, `access`, `status`, `created_dt`, `updated_dt`) VALUES
(1, 'Administrator',      'Full system access',        '1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74', 1, NOW(), NOW()),
(2, 'Employee/Cooperative','Branch & facility staff',   '1,2,3,4,5,6,7,8,9,10,11,13,15,16,17,18,19,20,21,22,23,24,25,27,29,30,31,32,33,34,35,36,37,38,43,44,45,46,47,49,50,51,52,53,54,55,56,62,66,67,68,69,70,72,73,74', 1, NOW(), NOW()),
(3, 'Beneficiary (ARB)',  'Farmer beneficiary portal', '1,23,30,31,32,44,45,62,72,73',                              1, NOW(), NOW());

-- ---------------------------------------------------------------
-- users   (password = 12345678, bcrypt hash)
-- ---------------------------------------------------------------
INSERT INTO `users` (`id`, `username`, `password`, `role_id`, `type`, `attempt`, `status`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$gRa5TRDFX1cp0UumTBlMHuJIz.IXuYIF6P4flficUMvApvmDJTG6O', 1, 1, NULL, 1, NOW(), NOW()),
(2, 'coop',  '$2y$10$gRa5TRDFX1cp0UumTBlMHuJIz.IXuYIF6P4flficUMvApvmDJTG6O', 2, 2, NULL, 1, NOW(), NOW()),
(3, 'staff', '$2y$10$gRa5TRDFX1cp0UumTBlMHuJIz.IXuYIF6P4flficUMvApvmDJTG6O', 2, 3, NULL, 1, NOW(), NOW()),
(4, 'ben1',  '$2y$10$gRa5TRDFX1cp0UumTBlMHuJIz.IXuYIF6P4flficUMvApvmDJTG6O', 3, 4, NULL, 1, NOW(), NOW()),
(5, 'ben2',  '$2y$10$gRa5TRDFX1cp0UumTBlMHuJIz.IXuYIF6P4flficUMvApvmDJTG6O', 3, 5, NULL, 1, NOW(), NOW()),
(6, 'ben3',  '$2y$10$gRa5TRDFX1cp0UumTBlMHuJIz.IXuYIF6P4flficUMvApvmDJTG6O', 3, 6, NULL, 1, NOW(), NOW());

-- ---------------------------------------------------------------
-- branch
-- ---------------------------------------------------------------
INSERT INTO `branch` (`id`, `code`, `name`, `details`, `created_at`, `updated_at`) VALUES
(1, 'BR-01', 'Ilocos Norte Provincial Office', 'Main provincial office', NOW(), NOW()),
(2, 'BR-02', 'Laoag City Field Office',          'City field branch',        NOW(), NOW()),
(3, 'BR-03', 'Batac Satellite Office',           'Satellite branch',         NOW(), NOW());

-- ---------------------------------------------------------------
-- agency
-- ---------------------------------------------------------------
INSERT INTO `agency` (`id`, `code`, `name`, `details`, `created_at`, `updated_at`) VALUES
(1, 'DAR', 'Department of Agrarian Reform', 'National implementing agency on agrarian reform', NOW(), NOW()),
(2, 'LGU', 'Local Government Unit',         'Local municipal government',                     NOW(), NOW());

-- ---------------------------------------------------------------
-- facility_type
-- ---------------------------------------------------------------
INSERT INTO `facility_type` (`id`, `name`, `details`, `created_at`, `updated_at`) VALUES
(1, 'Agri-Store',       'Farm inputs retail store',          NOW(), NOW()),
(2, 'Equipment Hub',    'Machinery reservation hub',         NOW(), NOW()),
(3, 'Extension Office', 'Training / extension facility',     NOW(), NOW()),
(4, 'Warehouse',        'Storage / inventory warehouse',     NOW(), NOW());

-- ---------------------------------------------------------------
-- facility
-- ---------------------------------------------------------------
INSERT INTO `facility` (`id`, `branch_id`, `type_ids`, `name`, `phone`, `email`, `street`, `barangay_id`, `district_id`, `city_id`, `province_id`, `region_id`, `postal_code`, `address`, `location_lat`, `location_lng`, `operating_hours`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, '1,4', 'DAR Ilocos Norte Agri-Store', '077-600-1234', 'store.ilocosnorte@dar.gov.ph', 'Mariano Marcos Ave', '012812001', NULL, '012812000', '012800000', '010000000', '2900', 'Bgy. No. 42, Apaya, Laoag City, Ilocos Norte', 18.1966, 120.5921, '8:00 AM - 5:00 PM', 1, NOW(), NOW()),
(2, 2, '2',    'Laoag Equipment Hub',        '077-600-5678', 'equipment.laoag@dar.gov.ph',  'Abadilla St',         '012812005', NULL, '012812000', '012800000', '010000000', '2900', 'Bgy. No. 41, Balacad, Laoag City, Ilocos Norte', 18.2010, 120.5830, '8:00 AM - 5:00 PM', 1, NOW(), NOW()),
(3, 3, '3',    'Batac Extension Office',     '077-600-9012', 'extension.batac@dar.gov.ph', 'Washington St',       '012805001', NULL, '012805000', '012800000', '010000000', '2906', 'Aglipay (Pob.), City of Batac, Ilocos Norte', 18.0553, 120.5691, '8:00 AM - 5:00 PM', 1, NOW(), NOW());

-- ---------------------------------------------------------------
-- employee
-- ---------------------------------------------------------------
INSERT INTO `employee` (`id`, `users_id`, `branch_id`, `facility_id`, `reference_no`, `fname`, `mname`, `lname`, `gender`, `marital`, `birthday`, `street`, `barangay_id`, `district_id`, `city_id`, `province_id`, `region_id`, `zip_code`, `country`, `address`, `email`, `mobile`, `position_id`, `status`, `profile`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 1, 'EMP-2', 'Maria',   'L.', 'Santos',      'Female', 'Married', '1988-04-12', 'J.P. Rizal St', '012812002', NULL, '012812000', '012800000', '010000000', '2900', 'PH', 'Bgy. No. 36, Araniw, Laoag City, Ilocos Norte', 'maria.santos@dar.gov.ph',  '09171234567', NULL, 1, NULL, NOW(), NOW()),
(2, 3, 2, 2, 'EMP-3', 'Jose',   'R.', 'Dela Cruz',   'Male',   'Single',  '1992-09-23', 'Gen. Luna St',  '012812005', NULL, '012812000', '012800000', '010000000', '2900', 'PH', 'Bgy. No. 41, Balacad, Laoag City, Ilocos Norte', 'jose.delacruz@dar.gov.ph', '09179876543', NULL, 1, NULL, NOW(), NOW()),
(3, 1, 1, 1, 'EMP-1', 'Admin',  'A.', 'Administrator','Male',  'Married', '1985-01-01', 'S. Gomez St',   '012812001', NULL, '012812000', '012800000', '010000000', '2900', 'PH', 'Bgy. No. 42, Apaya, Laoag City, Ilocos Norte', 'admin@dar.gov.ph',      '09170000001', NULL, 1, NULL, NOW(), NOW());

-- ---------------------------------------------------------------
-- beneficiary
-- ---------------------------------------------------------------
INSERT INTO `beneficiary` (`id`, `users_id`, `branch_id`, `facility_id`, `doc_num`, `card_num`, `national_id`, `lname`, `fname`, `mname`, `sname`, `nationality`, `gender`, `marital`, `birthday`, `email`, `mobile`, `street`, `barangay_id`, `district_id`, `city_id`, `province_id`, `region_id`, `zip_code`, `country`, `address`, `land_tenure_status`, `profile`, `report_card_score`, `verified_dt`, `status`, `created_at`, `updated_at`) VALUES
(1, 4, 1, 1, 'DOC000001', 'CARD0000000001', 'NID0000000001', 'Ramos',     'Pedro',     'C.', 'P.',  'Filipino', 'Male',   'Married', '1975-03-10', 'pedro.ramos@sample.ph', '09171111111', 'Manuel Nalvo St', '012812001', NULL, '012812000', '012800000', '010000000', '2900', 'PH', 'Bgy. No. 42, Apaya, Laoag City, Ilocos Norte', 'Cultivator', NULL, 85, NOW(), 1, NOW(), NOW()),
(2, 5, 1, 1, 'DOC000002', 'CARD0000000002', 'NID0000000002', 'Bautista',  'Luzviminda','D.', 'V.',  'Filipino', 'Female', 'Widowed', '1980-07-25', 'luz.bautista@sample.ph', '09172222222', 'Quezon Ave',     '012812006', NULL, '012812000', '012800000', '010000000', '2900', 'PH', 'Bgy. No. 40, Balatong, Laoag City, Ilocos Norte', 'Amortizing', NULL, 90, NOW(), 1, NOW(), NOW()),
(3, 6, 1, 1, 'DOC000003', 'CARD0000000003', 'NID0000000003', 'Lopez',     'Andres',   'B.', 'B.',  'Filipino', 'Male',   'Single',  '1990-11-02', 'andres.lopez@sample.ph', '09173333333', 'M.H. del Pilar St', '012812002', NULL, '012812000', '012800000', '010000000', '2900', 'PH', 'Bgy. No. 36, Araniw, Laoag City, Ilocos Norte', 'Emancipated', NULL, 78, NOW(), 1, NOW(), NOW());

-- ---------------------------------------------------------------
-- machinery_type
-- ---------------------------------------------------------------
INSERT INTO `machinery_type` (`id`, `name`, `details`, `created_at`, `updated_at`) VALUES
(1, 'Hand Tractor',      'Two-wheel walking tractor',      NOW(), NOW()),
(2, 'Four-Wheel Tractor','Ride-on four-wheel tractor',     NOW(), NOW()),
(3, 'Rice Combine Harvester', 'Harvesting machine',        NOW(), NOW()),
(4, 'Thresher',         'Mechanical threshing machine',    NOW(), NOW()),
(5, 'Transplanter',     'Rice transplanting machine',      NOW(), NOW());

-- ---------------------------------------------------------------
-- machinery
-- ---------------------------------------------------------------
INSERT INTO `machinery` (`id`, `branch_id`, `type_id`, `name`, `model`, `description`, `daily_rate`, `ratings`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Kubota Hand Tractor',      'KUB-MD-100', 'Two-wheel hand tractor for small farms',          1500, 4.5, 1, NOW(), NOW()),
(2, 1, 2, 'Mahindra 4WD Tractor',     'MAH-4WD-S650','Four-wheel tractor for land prep',                5000, 4.8, 1, NOW(), NOW()),
(3, 2, 3, 'Yanmar Combine Harvester', 'YAN-CB-880',  'Combine harvester for rice harvesting',           8000, 4.7, 1, NOW(), NOW()),
(4, 2, 4, 'Kubota Thresher',          'KUB-THR-200', 'Mechanical thresher',                              2500, 4.2, 1, NOW(), NOW()),
(5, 3, 5, 'Kubota Transplanter',      'KUB-TRP-300', 'Rice transplanter',                                3000, 4.4, 2, NOW(), NOW());

-- ---------------------------------------------------------------
-- machinery_images
-- ---------------------------------------------------------------
INSERT INTO `machinery_images` (`id`, `machinery_id`, `name`, `is_primary`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'hand_tractor_1.jpg', 1, 1, NOW(), NOW()),
(2, 2, 'tractor_4wd_1.jpg',  1, 1, NOW(), NOW()),
(3, 3, 'harvester_1.jpg',    1, 1, NOW(), NOW()),
(4, 4, 'thresher_1.jpg',     1, 1, NOW(), NOW()),
(5, 5, 'transplanter_1.jpg', 1, 1, NOW(), NOW());

-- ---------------------------------------------------------------
-- machinery_maintenance
-- ---------------------------------------------------------------
INSERT INTO `machinery_maintenance` (`id`, `machinery_id`, `emp_id`, `facility_id`, `type`, `priority`, `start_date`, `end_date`, `labor_cost`, `parts_cost`, `odometer_reading`, `description`, `resolution_notes`, `status`, `created_at`, `updated_at`) VALUES
(1, 2, 2, 2, 1, 2, '2026-01-05 09:00:00', '2026-01-05 17:00:00', 1500, 4500, 12500, 'Routine preventive maintenance', NULL, 3, NOW(), NOW()),
(2, 3, 2, 2, 4, 4, '2026-03-12 09:00:00', '2026-03-13 17:00:00', 2500, 12000, 89000, 'Engine replacement needed', 'Engine replaced', 3, NOW(), NOW()),
(3, 1, 1, 1, 1, 1, '2026-06-01 09:00:00', '2026-06-01 16:00:00', 800, 1200, 54000, 'Inspection checkup', NULL, 1, NOW(), NOW());

-- ---------------------------------------------------------------
-- machinery_reviews
-- ---------------------------------------------------------------
INSERT INTO `machinery_reviews` (`id`, `machinery_id`, `beneficiary_id`, `rating`, `comment`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 5, 'Very reliable machine for my farm.',  1, NOW(), NOW()),
(2, 2, 2, 4, 'Works well, slightly noisy.',         1, NOW(), NOW());

-- ---------------------------------------------------------------
-- product_category
-- ---------------------------------------------------------------
INSERT INTO `product_category` (`id`, `name`, `details`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Seed',       'Rice and vegetable seeds',        1, NOW(), NOW()),
(2, 'Fertilizer', 'Inorganic and organic fertilizers',1, NOW(), NOW()),
(3, 'Pesticide',  'Pest control chemicals',          1, NOW(), NOW()),
(4, 'Herbicide',  'Weed control chemicals',          1, NOW(), NOW()),
(5, 'Tool',       'Farm tools and equipment parts',  1, NOW(), NOW());

-- ---------------------------------------------------------------
-- product
-- ---------------------------------------------------------------
INSERT INTO `product` (`id`, `facility_id`, `category_id`, `sku`, `name`, `details`, `unit`, `is_hazardous`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'SKU-SEED-001', 'NSIC Rc222 Rice Seeds',   'Certified inbred rice seeds',       '50kg Bag',  0, 1, NOW(), NOW()),
(2, 1, 2, 'SKU-FERT-001', 'Urea 46-0-0',             'Nitrogen fertilizer',               '50kg Bag',  0, 1, NOW(), NOW()),
(3, 1, 2, 'SKU-FERT-002', 'Complete Fertilizer 14-14-14', 'Balanced NPK fertilizer',     '50kg Bag',  0, 1, NOW(), NOW()),
(4, 1, 3, 'SKU-PEST-001', 'Lambda Insecticide',      'Liquid insecticide',                '1L Bottle', 1, 1, NOW(), NOW()),
(5, 1, 4, 'SKU-HEB-001',  'Glyphosate Herbicide',    'Liquid herbicide',                  '1L Bottle', 1, 1, NOW(), NOW()),
(6, 1, 5, 'SKU-TOOL-001', 'Farm PVC Boots',          'Protective rubber boots',           'Pair',      0, 1, NOW(), NOW());

-- ---------------------------------------------------------------
-- product_images
-- ---------------------------------------------------------------
INSERT INTO `product_images` (`id`, `product_id`, `name`, `is_primary`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'rice_seeds_1.jpg', 1, 1, NOW(), NOW()),
(2, 2, 'urea_1.jpg',       1, 1, NOW(), NOW()),
(3, 3, 'complete_1.jpg',   1, 1, NOW(), NOW()),
(4, 4, 'insecticide_1.jpg',1, 1, NOW(), NOW()),
(5, 5, 'herbicide_1.jpg',  1, 1, NOW(), NOW()),
(6, 6, 'boots_1.jpg',      1, 1, NOW(), NOW());

-- ---------------------------------------------------------------
-- product_facility_price
-- ---------------------------------------------------------------
INSERT INTO `product_facility_price` (`id`, `product_id`, `facility_id`, `selling_price`, `minimum_price`, `maximum_price`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1350.00, 1300.00, 1400.00, 1, NOW(), NOW()),
(2, 2, 1,  980.00,  950.00, 1000.00, 1, NOW(), NOW()),
(3, 3, 1, 1250.00, 1200.00, 1280.00, 1, NOW(), NOW()),
(4, 4, 1,  350.00,  340.00,  360.00, 1, NOW(), NOW());

-- ---------------------------------------------------------------
-- product_inventory
-- ---------------------------------------------------------------
INSERT INTO `product_inventory` (`id`, `facility_id`, `product_id`, `received_stock`, `current_stock`, `reserved_stock`, `reorder_level`, `cost_price`, `selling_price`, `batch_number`, `expiry_date`, `storage_location`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 100,  70,  5, 20, 1100.00, 1350.00, 'BATCH-SEED-001', '2026-12-31', 1, 1, NOW(), NOW()),
(2, 1, 2, 200, 150, 10, 40,  820.00,  980.00, 'BATCH-UREA-001', '2027-06-30', 2, 1, NOW(), NOW()),
(3, 1, 3, 160, 120,  8, 30, 1050.00, 1250.00, 'BATCH-CMP-001',  '2027-05-31', 2, 1, NOW(), NOW()),
(4, 1, 4,  80,   15,  0, 25,  280.00,  350.00, 'BATCH-PST-001', '2026-09-30', 3, 1, NOW(), NOW());

-- ---------------------------------------------------------------
-- product_inventory_logs
-- ---------------------------------------------------------------
INSERT INTO `product_inventory_logs` (`id`, `inventory_id`, `action_type`, `quantity_changed`, `new_balance`, `reference_id`, `users_id`, `remarks`, `created_at`) VALUES
(1, 1, 1, 100, 100, NULL, 1, 'Initial restock of rice seeds',          NOW()),
(2, 2, 1, 200, 200, NULL, 1, 'Initial restock of urea',               NOW()),
(3, 1, 2,  30,  70, NULL, 4, 'Sold 30 bags to beneficiary',          NOW()),
(4, 4, 4,  -10,  15, NULL, 1, 'Low stock adjustment',                 NOW()),
(5, 3, 5,  -40,  80, NULL, 1, 'Batch partially expired, written off', NOW()),
(6, 2, 6,  -15, 135, NULL, 3, 'Damaged bags during handling',         NOW());

-- ---------------------------------------------------------------
-- product_price_history
-- ---------------------------------------------------------------
INSERT INTO `product_price_history` (`id`, `product_id`, `facility_id`, `price_type`, `old_price`, `new_price`, `remarks`, `created_by`, `effective_date`, `created_at`) VALUES
(1, 1, 1, 'SELLING', NULL, 1350.00, 'Initial selling price',       1, '2026-01-01', NOW()),
(2, 2, 1, 'SELLING',  950.00,  980.00, 'Price increased due to demand', 1, '2026-02-01', NOW()),
(3, 3, 1, 'SELLING', 1280.00, 1250.00, 'Promo price adjustment',       2, '2026-03-15', NOW());

-- ---------------------------------------------------------------
-- program
-- ---------------------------------------------------------------
INSERT INTO `program` (`id`, `code`, `name`, `agency_id`, `product_id`, `total_budget`, `remaining_budget`, `start_date`, `end_date`, `asset_type`, `status`, `created_at`, `updated_at`) VALUES
(1, 'PRG-2026-01', 'Rice Farmers Financial Assistance', 1, NULL, 3000000.00, 2400000.00, '2026-01-01', '2026-12-31', 0, 1, NOW(), NOW()),
(2, 'PRG-2026-02', 'Fuel Subsidy for Farmers',          2, NULL,  500000.00,  380000.00, '2026-02-01', '2026-08-31', 0, 1, NOW(), NOW()),
(3, 'PRG-2026-03', 'Seed Distribution Program',         1, 1,     2000.00,    1500.00,   '2026-03-01', '2026-09-30', 1, 1, NOW(), NOW());

-- ---------------------------------------------------------------
-- program_allocation
-- ---------------------------------------------------------------
INSERT INTO `program_allocation` (`id`, `program_id`, `branch_id`, `subsidy_type`, `allocated_budget`, `distributed_budget`, `reserved_budget`, `unit_subsidy_value`, `max_per_beneficiary`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 0, 2000000.00,  600000.00, 1400000.00, 15000.00, 50000.00, 1, NOW(), NOW()),
(2, 2, 1, 0,  400000.00,   80000.00,  320000.00,  5000.00, 10000.00, 1, NOW(), NOW()),
(3, 3, 1, 1,  1500.00,      500.00,    1000.00,    500.00, 2000.00,  1, NOW(), NOW());

-- ---------------------------------------------------------------
-- program_beneficiary
-- ---------------------------------------------------------------
INSERT INTO `program_beneficiary` (`id`, `allocation_id`, `beneficiary_id`, `recieved_budget`, `status`, `date_enrolled`, `date_received`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 15000.00, 1, '2026-01-10 09:00:00', '2026-01-15 10:00:00', NOW(), NOW()),
(2, 1, 2, 15000.00, 1, '2026-01-11 09:00:00', '2026-01-16 11:00:00', NOW(), NOW()),
(3, 3, 1,  500.00,  1, '2026-03-05 09:00:00', '2026-03-08 09:30:00', NOW(), NOW());

-- ---------------------------------------------------------------
-- system_treasury
-- ---------------------------------------------------------------
INSERT INTO `system_treasury` (`id`, `program_id`, `total_budget_allocated`, `current_unallocated_balance`, `total_disbursed_to_date`, `updated_at`) VALUES
(1, 1, 3000000.00, 1000000.00, 2000000.00, NOW());

-- ---------------------------------------------------------------
-- booking
-- ---------------------------------------------------------------
INSERT INTO `booking` (`id`, `booking_num`, `beneficiary_id`, `branch_id`, `machinery_id`, `latitude`, `longitude`, `total_days`, `unit_price`, `total_cost`, `status`, `created_at`, `updated_at`) VALUES
(1, 'BK-2026-0001', 1, 1, 1, 18.1966, 120.5921, 2, 1500.00, 3000.00, 1, NOW(), NOW()),
(2, 'BK-2026-0002', 2, 1, 2, 18.2010, 120.5830, 1, 5000.00, 5000.00, 3, NOW(), NOW()),
(3, 'BK-2026-0003', 3, 1, 3, 18.1900, 120.5900, 3, 8000.00, 24000.00, 1, NOW(), NOW()),
(4, 'BK-2026-0004', 3, 1, 5, 18.1900, 120.5900, 2, 3000.00, 6000.00, 4, NOW(), NOW());

-- ---------------------------------------------------------------
-- booking_schedules
-- ---------------------------------------------------------------
INSERT INTO `booking_schedules` (`id`, `booking_id`, `start_at`, `end_at`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, '2026-06-01 08:00:00', '2026-06-02 17:00:00', 2, NOW(), NOW()),
(2, 2, '2026-05-20 08:00:00', '2026-05-21 17:00:00', 2, NOW(), NOW()),
(3, 3, '2026-06-15 08:00:00', '2026-06-17 17:00:00', 1, NOW(), NOW());

-- ---------------------------------------------------------------
-- booking_logs
-- ---------------------------------------------------------------
INSERT INTO `booking_logs` (`id`, `booking_id`, `wallet_balance_id`, `booked_by`, `approved_at`, `approved_staff_id`, `checkout_at`, `checkout_signature`, `checkout_staff_id`, `returned_at`, `returned_signature`, `returned_staff_id`, `declined_at`, `declined_staff_id`, `declined_remarks`, `remarks`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 4, '2026-05-28 09:00:00', 2, '2026-06-01 08:05:00', 'sample-sig', 2, '2026-06-02 17:10:00', 'sample-sig', 2, NULL, NULL, NULL, 'Approved by staff', NOW(), NOW()),
(2, 2, NULL, 5, '2026-05-18 09:00:00', 2, '2026-05-20 08:05:00', 'sample-sig', 2, '2026-05-21 17:10:00', 'sample-sig', 2, NULL, NULL, NULL, 'Completed booking',  NOW(), NOW()),
(3, 3, NULL, 6, '2026-06-12 09:00:00', 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Approved booking',  NOW(), NOW()),
(4, 4, NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-14 14:30:00', 3, 'Machine unavailable on requested dates', 'Declined booking', NOW(), NOW());

-- ---------------------------------------------------------------
-- service_reviews
-- ---------------------------------------------------------------
INSERT INTO `service_reviews` (`id`, `booking_id`, `rating`, `quality_score`, `timeliness_score`, `comments`, `beneficiary_signature_payload`, `emp_id`, `is_verified`, `verification_timestamp`, `verification_notes`, `is_disputed`, `dispute_reason`, `created_at`, `updated_at`) VALUES
(1, 1, 5, 5, 4, 'Great machine and staff.', 'sig-payload', 2, 1, NOW(), 'Verified', 0, NULL, NOW(), NOW()),
(2, 2, 4, 4, 5, 'Satisfied with the service.', 'sig-payload', 2, 1, NOW(), 'Verified', 0, NULL, NOW(), NOW());

-- ---------------------------------------------------------------
-- wallet
-- ---------------------------------------------------------------
INSERT INTO `wallet` (`id`, `account_num`, `beneficiary_id`, `total_balance`, `credit_limit`, `is_frozen`, `created_at`) VALUES
(1, 'WAL0000000000001', 1, 15000.00, 100000.00, 0, NOW()),
(2, 'WAL0000000000002', 2, 12000.00,  80000.00, 0, NOW()),
(3, 'WAL0000000000003', 3,  5000.00,  60000.00, 0, NOW());

-- ---------------------------------------------------------------
-- wallet_balances
-- ---------------------------------------------------------------
INSERT INTO `wallet_balances` (`id`, `wallet_id`, `program_id`, `balance_type`, `amount`, `created_at`) VALUES
(1, 1, 1, 4, 15000.00, NOW()),
(2, 2, 1, 4, 12000.00, NOW()),
(3, 3, 2, 2,  5000.00, NOW());

-- ---------------------------------------------------------------
-- wallet_logs
-- ---------------------------------------------------------------
INSERT INTO `wallet_logs` (`id`, `wallet_id`, `action`, `amount`, `balance_before`, `balance_after`, `metadata`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 15000.00, 0.00, 15000.00, '{"program":1,"note":"RFFA credit"}', NOW(), NOW()),
(2, 2, 2, 12000.00, 0.00, 12000.00, '{"program":1,"note":"RFFA credit"}', NOW(), NOW()),
(3, 3, 2,  5000.00, 0.00,  5000.00, '{"program":2,"note":"Fuel subsidy"}', NOW(), NOW()),
(4, 1, 1, 1000.00, 15000.00, 14000.00, '{"program":1,"note":"Store purchase TRX-2026-0001"}', NOW(), NOW());

-- ---------------------------------------------------------------
-- store_transactions
-- ---------------------------------------------------------------
INSERT INTO `store_transactions` (`id`, `reference_no`, `facility_id`, `beneficiary_id`, `emp_id`, `program_id`, `wallet_balance_id`, `gross_amount`, `discount_amount`, `net_amount`, `payment_method`, `settlement_status`, `card_tap_payload`, `transaction_dt`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'TRX-2026-0001', 1, 1, 2, 1, 1, 1000.00, 0.00, 1000.00, 1, 1, 'payload-payload', '2026-05-30 10:00:00', 'Purchased rice seeds', NOW(), NOW()),
(2, 'TRX-2026-0002', 1, 2, 2, 1, 2,  490.00, 0.00,  490.00, 3, 0, 'payload-payload', '2026-06-05 15:30:00', 'Purchased urea (wallet)', NOW(), NOW());

-- ---------------------------------------------------------------
-- terminal / facility_terminal
-- ---------------------------------------------------------------
INSERT INTO `terminal` (`id`, `name`, `uid`, `details`, `type`, `created_dt`, `updated_dt`) VALUES
(1, 'POS Terminal - Laoag', 'POS-001', 'Point of sale terminal', 1, NOW(), NOW()),
(2, 'NFC Reader - Batac',  'NFC-001', 'NFC mobile reader',      3, NOW(), NOW());

INSERT INTO `facility_terminal` (`id`, `facility_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, NOW(), NOW()),
(2, 2, 1, NOW(), NOW());

-- ---------------------------------------------------------------
-- cocrom_land_parcels
-- ---------------------------------------------------------------
INSERT INTO `cocrom_land_parcels` (`id`, `beneficiary_id`, `title_number`, `total_area_hectares`, `latitude`, `longitude`, `land_use_type`, `productivity_score`, `last_survey_date`, `created_at`, `updated_at`) VALUES
(1, 1, 'TCT-2026-0001', 2.50, 18.1966, 120.5921, 'Rice',    88.5, '2026-04-01', NOW(), NOW()),
(2, 2, 'TCT-2026-0002', 1.75, 18.2010, 120.5830, 'Rice',    92.0, '2026-04-02', NOW(), NOW()),
(3, 3, 'TCT-2026-0003', 3.00, 18.1900, 120.5900, 'Corn',    80.0, '2026-04-03', NOW(), NOW());

-- ---------------------------------------------------------------
-- cocrom_records
-- ---------------------------------------------------------------
INSERT INTO `cocrom_records` (`id`, `beneficiary_id`, `land_parcel_id`, `credit_limit`, `certificate_status`, `issued_date`, `expiry_date`, `encrypted_signature`) VALUES
(1, 1, 1, 100000.00, 'Active', '2026-01-01', '2031-01-01', 'enc-sig-1'),
(2, 2, 2,  80000.00, 'Active', '2026-01-01', '2031-01-01', 'enc-sig-2');

-- ---------------------------------------------------------------
-- cocrom_land_monitoring
-- ---------------------------------------------------------------
INSERT INTO `cocrom_land_monitoring` (`id`, `employee_id`, `land_parcels_id`, `log_type`, `title`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 0, 'Site Visit',      'Checked crop health and water levels.', NOW(), NOW()),
(2, 2, 2, 2, 'Soil Test',       'Soil pH recorded at 6.2.',           NOW(), NOW());

-- ---------------------------------------------------------------
-- cocrom_land_images / cocrom_records_images
-- ---------------------------------------------------------------
INSERT INTO `cocrom_land_images` (`id`, `land_monitoring_id`, `name`, `is_primary`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'site_visit_1.jpg', 1, 1, NOW(), NOW()),
(2, 2, 'soil_test_1.jpg',  1, 1, NOW(), NOW());

INSERT INTO `cocrom_records_images` (`id`, `records_id`, `name`, `is_primary`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'record_img_1.jpg', 1, 1, NOW(), NOW());

-- ---------------------------------------------------------------
-- training
-- ---------------------------------------------------------------
INSERT INTO `training` (`id`, `reference_no`, `accreditation_no`, `accreditation_date`, `title`, `summary`, `objectives`, `promotional_image`, `start_at`, `end_at`, `street`, `barangay_id`, `district_id`, `city_id`, `province_id`, `region_id`, `zip_code`, `latitude`, `longitude`, `hours`, `discussion`, `program_type`, `is_all`, `is_paid`, `amount`, `status`, `approval_status`, `created_at`, `updated_at`) VALUES
(1, 'TRN-2026-001', 'ACC-001', '2025-12-01', 'Organic Fertilizer Basic Training', 'Learn how to make organic fertilizers.', 'Teach farmers basic organic fertilization.', NULL, '2026-06-10 09:00:00', '2026-06-10 17:00:00', 'P. Burgos St', '012812001', NULL, '012812000', '012800000', '010000000', '2900', 18.1966, 120.5921, 8, NULL, 1, 1, 0, 0.00, 1, 1, NOW(), NOW()),
(2, 'TRN-2026-002', 'ACC-002', '2025-12-10', 'Harvesting Equipment Seminar', 'Proper handling of harvesting machinery.', 'Educate on safe machinery operation.', NULL, '2026-07-15 09:00:00', '2026-07-15 17:00:00', 'Gen. Luna St', '012812005', NULL, '012812000', '012800000', '010000000', '2900', 18.2010, 120.5830, 8, NULL, 1, 1, 1, 500.00, 1, 1, NOW(), NOW());

-- ---------------------------------------------------------------
-- training_program
-- ---------------------------------------------------------------
INSERT INTO `training_program` (`id`, `training_id`, `topic`, `speaker`, `time_start`, `time_end`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Soil and Composting', 'Engr. Ana Cruz', '09:00:00', '12:00:00', 1, NOW(), NOW()),
(2, 1, 'Organic Fertilizer Demo', 'Dr. Ben Torres', '13:00:00', '16:00:00', 1, NOW(), NOW()),
(3, 2, 'Machinery Safety', 'Engr. Carl Reyes', '09:00:00', '12:00:00', 1, NOW(), NOW());

-- ---------------------------------------------------------------
-- training_admission
-- ---------------------------------------------------------------
INSERT INTO `training_admission` (`id`, `training_id`, `beneficiary_id`, `doc_num`, `registration_at`, `attendance_at`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'DOC000001', '2026-06-01 09:00:00', '2026-06-10 08:55:00', 1, NOW(), NOW()),
(2, 1, 2, 'DOC000002', '2026-06-02 09:00:00', NULL, 0, NOW(), NOW()),
(3, 2, 3, 'DOC000003', '2026-07-01 09:00:00', NULL, 0, NOW(), NOW());

-- ---------------------------------------------------------------
-- training_approval
-- ---------------------------------------------------------------
INSERT INTO `training_approval` (`id`, `training_id`, `action`, `reason`, `approved_by`, `created_at`) VALUES
(1, 1, 'approved', 'Training validated by admin.', 1, NOW()),
(2, 2, 'approved', 'Seminar approved.',             1, NOW());

-- ---------------------------------------------------------------
-- user_modules  (parent 0 = top-level; children reference parent id)
-- Pages mirror the routes defined in index.php
-- ---------------------------------------------------------------
INSERT INTO `user_modules` (`id`, `parent_id`, `title`, `icon`, `page`, `filename`, `sort_order`, `is_menu`, `status`, `created_at`, `updated_at`) VALUES
(1,  0,  'Dashboard',           'fas fa-home',              'dashboard',              'home.php',                        1,  1, 1, NOW(), NOW()),
(2,  0,  'Machine',             'fas fa-tractor',           NULL,                     NULL,                              2,  1, 1, NOW(), NOW()),
(3,  2,  'List Machine',        'fas fa-list',              'machine-list',           'machine-list.php',                1,  1, 1, NOW(), NOW()),
(4,  2,  'Machine Types',       'fas fa-tags',              'machine-types',          'machine-types.php',               2,  1, 1, NOW(), NOW()),
(5,  2,  'Machine Maintenance', 'fas fa-wrench',            'machine-maintenance',    'machine-maintenance.php',         3,  1, 1, NOW(), NOW()),
(6,  0,  'Branch',              'fas fa-location-dot',      'branch-list',            'branch-list.php',                 3,  1, 1, NOW(), NOW()),
(7,  0,  'Agency',              'fas fa-briefcase',         'agency',                 'agency.php',                      4,  1, 1, NOW(), NOW()),
(8,  0,  'Beneficiary',         'fas fa-hand-holding-heart',NULL,                     NULL,                              5,  1, 1, NOW(), NOW()),
(9,  8,  'List Beneficiary',    'fas fa-list',              'beneficiary-list',       'beneficiary-list.php',            1,  1, 1, NOW(), NOW()),
(10, 8,  'For Verification',    'fas fa-user-check',        'beneficiary-verify-list','beneficiary-verify-list.php',     2,  1, 1, NOW(), NOW()),
(11, 8,  'Add Beneficiary',     'fas fa-user-plus',         'beneficiary-add',        'beneficiary-add.php',             3,  1, 1, NOW(), NOW()),
(12, 8,  'Edit Beneficiary',    'fas fa-edit',              'beneficiary-edit',       'beneficiary-edit.php',            4,  0, 1, NOW(), NOW()),
(13, 8,  'View Beneficiary',    'fas fa-eye',               'beneficiary-view',       'beneficiary-view.php',            5,  0, 1, NOW(), NOW()),
(14, 8,  'Verify Beneficiary',  'fas fa-fingerprint',       'beneficiary-verify',     'beneficiary-verify.php',          6,  0, 1, NOW(), NOW()),
(15, 0,  'Facility',            'fas fa-building-columns',  NULL,                     NULL,                              6,  1, 1, NOW(), NOW()),
(16, 15, 'List Facility',       'fas fa-list',              'list-facility',          'facility-list.php',               1,  1, 1, NOW(), NOW()),
(17, 15, 'Add Facility',        'fas fa-plus',              'add-facility',           'facility-add.php',                2,  1, 1, NOW(), NOW()),
(18, 15, 'Facility Types',      'fas fa-tags',              'facility-type',          'facility-type.php',               3,  1, 1, NOW(), NOW()),
(19, 15, 'Edit Facility',       'fas fa-edit',              'edit-facility',          'facility-edit.php',               4,  0, 1, NOW(), NOW()),
(20, 0,  'Products',            'fas fa-boxes-stacked',     NULL,                     NULL,                              7,  1, 1, NOW(), NOW()),
(21, 20, 'List Products',       'fas fa-list',              'list-products',          'product-list.php',                1,  1, 1, NOW(), NOW()),
(22, 20, 'Product Categories',  'fas fa-tags',              'product-category',       'product-category.php',            2,  1, 1, NOW(), NOW()),
(23, 20, 'Available Products',  'fas fa-box-open',          'product-available',      'product-available.php',           3,  1, 1, NOW(), NOW()),
(24, 20, 'Facility Prices',     'fas fa-tag',               'facility-prices',        'facility-prices.php',             4,  1, 1, NOW(), NOW()),
(25, 20, 'Price History',       'fas fa-clock-rotate-left', 'price-history',          'price-history.php',               5,  1, 1, NOW(), NOW()),
(26, 20, 'Add Product',         'fas fa-plus',              'add-product',            'product-add.php',                 6,  0, 1, NOW(), NOW()),
(27, 20, 'Edit Product',        'fas fa-edit',              'edit-product',           'product-edit.php',                7,  0, 1, NOW(), NOW()),
(28, 20, 'Product Details',     'fas fa-eye',               'product-details',        'product-details.php',             8,  0, 1, NOW(), NOW()),
(66, 0,  'Inventory',           'fas fa-warehouse',         NULL,                     NULL,                              8,  1, 1, NOW(), NOW()),
(67, 66, 'Stock List',          'fas fa-list',              'list-inventory',         'product-inventory.php',           1,  1, 1, NOW(), NOW()),
(68, 66, 'Receive Stock',       'fas fa-plus',              'add-inventory',          'product-inventory-add.php',       2,  1, 1, NOW(), NOW()),
(69, 66, 'Stock Movements',     'fas fa-right-left',        'stock-movements',        'product-stock-movements.php',     3,  1, 1, NOW(), NOW()),
(70, 66, 'Low Stock',           'fas fa-exclamation-triangle','low-stock',            'product-low-stock.php',           4,  1, 1, NOW(), NOW()),
(71, 66, 'Buy / Sell Simulation','fas fa-flask',            'simulation',             'product-simulation.php',          5,  1, 1, NOW(), NOW()),
(29, 0,  'Booking',             'fas fa-calendar-check',    NULL,                     NULL,                              9,  1, 1, NOW(), NOW()),
(30, 29, 'Book a Machine',      'fas fa-tractor',           'booking-browse',         'booking-browse.php',              1,  1, 1, NOW(), NOW()),
(31, 29, 'My Bookings',         'fas fa-credit-card',       'booking-beneficiary',    'booking-beneficiary.php',         2,  1, 1, NOW(), NOW()),
(32, 29, 'Available Machinery', 'fas fa-box-open',          'booking-available',      'booking-available.php',           3,  1, 1, NOW(), NOW()),
(33, 29, 'Booking Approval',    'fas fa-check',             'booking-approval',       'booking-approval.php',            4,  1, 1, NOW(), NOW()),
(34, 29, 'Checkout & Return',   'fas fa-circle-check',      'booking-completed',      'booking-completed.php',           5,  1, 1, NOW(), NOW()),
(35, 29, 'Declined Bookings',   'fas fa-xmark',             'booking-declined',       'booking-declined.php',            6,  1, 1, NOW(), NOW()),
(36, 0,  'Program',             'fas fa-list-check',        NULL,                     NULL,                              10, 1, 1, NOW(), NOW()),
(37, 36, 'Programs',            'fas fa-list',              'list-programs',          'program-list.php',                1,  1, 1, NOW(), NOW()),
(38, 36, 'Allocations',         'fas fa-layer-group',       'list-allocations',       'program-allocation.php',          2,  1, 1, NOW(), NOW()),
(39, 36, 'Add Program',         'fas fa-plus',              'add-program',            'program-add.php',                 3,  0, 1, NOW(), NOW()),
(40, 36, 'Add Allocation',      'fas fa-plus',              'add-allocation',         'program-allocation-add.php',      4,  0, 1, NOW(), NOW()),
(41, 36, 'Edit Allocation',     'fas fa-edit',              'edit-allocation',        'program-allocation-edit.php',     5,  0, 1, NOW(), NOW()),
(42, 36, 'Program Beneficiaries','fas fa-users',            'list-program-beneficiary','program-beneficiary.php',        6,  0, 1, NOW(), NOW()),
(43, 0,  'Training',            'fas fa-chalkboard-user',   NULL,                     NULL,                              11, 1, 1, NOW(), NOW()),
(44, 43, 'Available Training',  'fas fa-calendar-check',    'training-available',     'training-available.php',          1,  1, 1, NOW(), NOW()),
(45, 43, 'Enroll in Training',  'fas fa-user-plus',         'booking-training',       'booking-training.php',            2,  1, 1, NOW(), NOW()),
(46, 43, 'Training List',       'fas fa-list',              'list-training',          'training-list.php',               3,  1, 1, NOW(), NOW()),
(47, 43, 'Training Admission',  'fas fa-users',             'training-admission',     'training-admission.php',          4,  1, 1, NOW(), NOW()),
(48, 43, 'Add Training',        'fas fa-plus',              'add-training',           'training-add.php',                5,  0, 1, NOW(), NOW()),
(49, 0,  'Land',                'fas fa-earth-asia',        NULL,                     NULL,                              12, 1, 1, NOW(), NOW()),
(50, 49, 'Land Monitoring Logs','fas fa-map-location-dot', 'list-land-monitoring',   'land-monitoring-logs.php',        1,  1, 1, NOW(), NOW()),
(51, 49, 'Land Parcels',        'fas fa-map',               'list-land-parcel',       'land-monitoring-parcel.php',      2,  1, 1, NOW(), NOW()),
(52, 49, 'Land Records',        'fas fa-file-lines',        'list-land-records',      'land-monitoring-record.php',      3,  1, 1, NOW(), NOW()),
(53, 49, 'Add Land Parcel',     'fas fa-plus',              'add-land-parcel',        'land-monitoring-parcel-add.php',  4,  0, 1, NOW(), NOW()),
(54, 49, 'Edit Land Parcel',    'fas fa-edit',              'edit-land-parcel',       'land-monitoring-parcel-edit.php', 5,  0, 1, NOW(), NOW()),
(55, 49, 'Add Land Record',     'fas fa-plus',              'add-land-records',       'land-monitoring-record-add.php',  6,  0, 1, NOW(), NOW()),
(56, 49, 'Edit Land Record',    'fas fa-edit',              'edit-land-records',      'land-monitoring-record-edit.php', 7,  0, 1, NOW(), NOW()),
(57, 0,  'User',                'fas fa-user',              NULL,                     NULL,                              13, 1, 1, NOW(), NOW()),
(58, 57, 'List User',           'fas fa-list',              'user-list',              'users-list.php',                  1,  1, 1, NOW(), NOW()),
(59, 57, 'Add User',            'fas fa-user-plus',         'add-user',               'users-add.php',                   2,  1, 1, NOW(), NOW()),
(60, 57, 'Edit User',           'fas fa-edit',              'edit-user',              'users-edit.php',                  3,  0, 1, NOW(), NOW()),
(61, 57, 'View User',           'fas fa-eye',               'view-user',              'users-view.php',                  4,  0, 1, NOW(), NOW()),
(62, 57, 'User Profile',        'fas fa-user',              'profile',                'profile.php',                     5,  0, 1, NOW(), NOW()),
(63, 0,  'Settings',            'fas fa-gear',              NULL,                     NULL,                              14, 1, 1, NOW(), NOW()),
(64, 63, 'List Module',         'fas fa-list',              'module-list',            'module-list.php',                 1,  1, 1, NOW(), NOW()),
(65, 63, 'User Roles',          'fas fa-user-gear',         'user-roles',             'user-role.php',                   2,  1, 1, NOW(), NOW()),
(72, 0,  'Wallet',              'fas fa-wallet',            NULL,                     '',                                15, 1, 1, NOW(), NOW()),
(73, 72, 'Wallet List',         'fas fa-list',              'wallet-list',            'wallet-list.php',                 1,  1, 1, NOW(), NOW()),
(74, 63, 'All Logs',            'fas fa-clipboard-list',    'logs',                   'logs.php',                        3,  1, 1, NOW(), NOW());

SET FOREIGN_KEY_CHECKS=1;
COMMIT;