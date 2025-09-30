-- Create Course Date for Today's Live Class
-- Date: September 19, 2025
-- Created for live class preparation

-- Insert a course date for today - using Course Unit 1 (Day 1 of FL-D40 Day Course)
-- Time: 8:00 AM to 5:00 PM Eastern Time
INSERT INTO public.course_dates (is_active, course_unit_id, starts_at, ends_at)
VALUES (
    true,
    1,
    '2025-09-19 08:00:00-04',
    '2025-09-19 17:00:00-04'
);

-- Query to verify the insertion
SELECT
    cd.id,
    cd.is_active,
    cd.course_unit_id,
    cu.title as course_unit_title,
    cu.admin_title,
    c.title as course_title,
    cd.starts_at,
    cd.ends_at
FROM course_dates cd
JOIN course_units cu ON cd.course_unit_id = cu.id
JOIN courses c ON cu.course_id = c.id
WHERE cd.starts_at::date = '2025-09-19'
ORDER BY cd.starts_at;
