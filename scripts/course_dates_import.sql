-- Course Dates Import Script - SAFE VERSION
-- Run this script in pgAdmin Query Tool

-- Step 1: Clear existing data (only records with ID >= 10000)
DELETE FROM public.course_dates WHERE id >= 10000;

-- Step 2: Insert all course date records
INSERT INTO public.course_dates (id, is_active, course_unit_id, starts_at, ends_at) VALUES
(10000, true, 1, '2023-07-24 08:00:00-04', '2023-07-24 17:00:00-04'),
(10001, true, 2, '2023-07-25 08:00:00-04', '2023-07-25 17:00:00-04'),
(10002, true, 3, '2023-07-26 08:00:00-04', '2023-07-26 17:00:00-04'),
(10003, true, 4, '2023-07-27 08:00:00-04', '2023-07-27 17:00:00-04'),
(10004, true, 5, '2023-07-28 08:00:00-04', '2023-07-28 17:00:00-04');

-- Step 3: Update sequence
SELECT pg_catalog.setval('public.course_dates_id_seq', 10530, true);
