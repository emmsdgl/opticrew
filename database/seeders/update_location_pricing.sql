-- Update Location Pricing Data
-- Generated from clients.csv data

-- ===================================================
-- KAKSLAUTTANEN (contracted_client_id = 1)
-- ===================================================

-- Small Cabin (12 cabins)
UPDATE locations
SET normal_rate_per_hour = 42.00,
    sunday_holiday_rate = 84.00
WHERE contracted_client_id = 1 AND location_type = 'Small Cabin';

-- Medium Cabin (6 cabins)
UPDATE locations
SET normal_rate_per_hour = 51.00,
    sunday_holiday_rate = 102.00
WHERE contracted_client_id = 1 AND location_type = 'Medium Cabin';

-- Big Cabin (13 cabins)
UPDATE locations
SET normal_rate_per_hour = 60.00,
    sunday_holiday_rate = 120.00
WHERE contracted_client_id = 1 AND location_type = 'Big Cabin';

-- Queen Suite (5 cabins)
UPDATE locations
SET normal_rate_per_hour = 60.00,
    sunday_holiday_rate = 120.00
WHERE contracted_client_id = 1 AND location_type = 'Queen Suite';

-- Traditional House (1 cabin)
UPDATE locations
SET normal_rate_per_hour = 60.00,
    sunday_holiday_rate = 120.00
WHERE contracted_client_id = 1 AND location_type = 'Traditional House';

-- Turf Chamber (1 cabin)
UPDATE locations
SET normal_rate_per_hour = 60.00,
    sunday_holiday_rate = 120.00
WHERE contracted_client_id = 1 AND location_type = 'Turf Chamber';

-- Igloos (20 cabins)
UPDATE locations
SET normal_rate_per_hour = 30.00,
    sunday_holiday_rate = 60.00
WHERE contracted_client_id = 1 AND location_type = 'Igloo';

-- ===================================================
-- AIKAMATKAT (contracted_client_id = 2)
-- ===================================================

-- Panimo Cabins (12 cabins)
UPDATE locations
SET normal_rate_per_hour = 68.25,
    sunday_holiday_rate = 120.50,
    deep_cleaning_rate = 210.00,
    light_deep_cleaning_rate = 110.00,
    student_rate = 36.75,
    student_sunday_holiday_rate = 55.50
WHERE contracted_client_id = 2 AND location_type = 'Panimo Cabins';

-- Metsakoti A (1 cabin)
UPDATE locations
SET normal_rate_per_hour = 84.00,
    sunday_holiday_rate = 126.00,
    deep_cleaning_rate = 210.00,
    light_deep_cleaning_rate = 110.00,
    student_rate = 36.75,
    student_sunday_holiday_rate = 55.50
WHERE contracted_client_id = 2 AND location_name = 'Metsakoti A';

-- Metsakoti B (1 cabin)
UPDATE locations
SET normal_rate_per_hour = 84.00,
    sunday_holiday_rate = 126.00,
    deep_cleaning_rate = 210.00,
    light_deep_cleaning_rate = 110.00,
    student_rate = 47.25,
    student_sunday_holiday_rate = 70.90
WHERE contracted_client_id = 2 AND location_name = 'Metsakoti B';

-- Kermikkas
UPDATE locations
SET normal_rate_per_hour = 36.75,
    sunday_holiday_rate = 55.50,
    student_rate = 20.00,
    student_sunday_holiday_rate = 30.00
WHERE contracted_client_id = 2 AND location_name = 'Kermikkas';

-- Hirvasaho A2 and B1
UPDATE locations
SET normal_rate_per_hour = 36.75,
    sunday_holiday_rate = 55.50,
    student_rate = 20.00,
    student_sunday_holiday_rate = 30.00
WHERE contracted_client_id = 2 AND location_name = 'Hirvasaho A2 and B1';

-- Hirvasaho B2
UPDATE locations
SET normal_rate_per_hour = 68.25,
    sunday_holiday_rate = 102.50,
    student_rate = 36.75,
    student_sunday_holiday_rate = 55.50
WHERE contracted_client_id = 2 AND location_name = 'Hirvasaho B2';

-- Hirvas Apartments
UPDATE locations
SET normal_rate_per_hour = 36.75,
    sunday_holiday_rate = 55.50,
    student_rate = 20.00,
    student_sunday_holiday_rate = 30.00
WHERE contracted_client_id = 2 AND location_type = 'Hirvas Apartments';

-- Voursa 3A and 3B
UPDATE locations
SET normal_rate_per_hour = 36.75,
    sunday_holiday_rate = 55.50,
    student_rate = 20.00,
    student_sunday_holiday_rate = 30.00
WHERE contracted_client_id = 2 AND location_name = 'Voursa 3A and 3B';

-- Voursa 3C
UPDATE locations
SET normal_rate_per_hour = 68.25,
    sunday_holiday_rate = 102.50,
    student_rate = 36.75,
    student_sunday_holiday_rate = 55.50
WHERE contracted_client_id = 2 AND location_name = 'Voursa 3C';

-- Moitakuru C31 and C32
UPDATE locations
SET normal_rate_per_hour = 57.75,
    sunday_holiday_rate = 87.50,
    student_rate = 31.50,
    student_sunday_holiday_rate = 47.25
WHERE contracted_client_id = 2 AND location_name = 'Moitakuru C31 and C32';

-- Luulampi
UPDATE locations
SET normal_rate_per_hour = 68.25,
    sunday_holiday_rate = 102.50,
    student_rate = 36.75,
    student_sunday_holiday_rate = 55.50
WHERE contracted_client_id = 2 AND location_name = 'Luulampi';

-- Metashirvas
UPDATE locations
SET normal_rate_per_hour = 73.50,
    sunday_holiday_rate = 110.25,
    student_rate = 39.75,
    student_sunday_holiday_rate = 59.50
WHERE contracted_client_id = 2 AND location_name = 'Metashirvas';

-- Kelotähti
UPDATE locations
SET normal_rate_per_hour = 73.50,
    sunday_holiday_rate = 110.25,
    student_rate = 39.75,
    student_sunday_holiday_rate = 59.50
WHERE contracted_client_id = 2 AND location_name LIKE 'Kelot%';

-- Raahenmaja
UPDATE locations
SET normal_rate_per_hour = 94.50,
    sunday_holiday_rate = 141.75,
    student_rate = 51.00,
    student_sunday_holiday_rate = 76.50
WHERE contracted_client_id = 2 AND location_name = 'Raahenmaja';
