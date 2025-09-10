-- Sample data for testing the student dashboard
-- This creates course authorizations for user ID 2 (Richard Clark)

-- First, let's check if we have courses and add some if needed
INSERT INTO courses (id, name, description, created_at, updated_at)
VALUES (1, 'Basic Security Training', 'Fundamental security training course', NOW(), NOW())
ON CONFLICT (id) DO NOTHING;

-- Create course authorizations for user ID 2
-- Active (incomplete) course auth
INSERT INTO course_auths (user_id, course_id, created_at, updated_at, agreed_at, start_date, expire_date)
VALUES (2, 1, NOW(), NOW(), NOW(), CURRENT_DATE, CURRENT_DATE + INTERVAL '30 days')
ON CONFLICT (user_id, course_id) DO UPDATE SET
    agreed_at = NOW(),
    start_date = CURRENT_DATE,
    expire_date = CURRENT_DATE + INTERVAL '30 days',
    updated_at = NOW();

-- Completed course auth (for a different course)
INSERT INTO courses (id, name, description, created_at, updated_at)
VALUES (2, 'Advanced Security Protocols', 'Advanced security training course', NOW(), NOW())
ON CONFLICT (id) DO NOTHING;

INSERT INTO course_auths (user_id, course_id, created_at, updated_at, agreed_at, completed_at, is_passed, start_date, expire_date)
VALUES (2, 2, NOW() - INTERVAL '30 days', NOW(), NOW() - INTERVAL '30 days', NOW() - INTERVAL '5 days', true, CURRENT_DATE - INTERVAL '30 days', CURRENT_DATE + INTERVAL '365 days')
ON CONFLICT (user_id, course_id) DO UPDATE SET
    completed_at = NOW() - INTERVAL '5 days',
    is_passed = true,
    updated_at = NOW();

-- Verify the data
SELECT 'Course Auths for User 2:' as info;
SELECT ca.*, c.name as course_name
FROM course_auths ca
JOIN courses c ON ca.course_id = c.id
WHERE ca.user_id = 2;
