-- Clear existing discount codes first (optional - remove if you want to keep existing data)
-- TRUNCATE discount_codes RESTART IDENTITY CASCADE;

-- Insert discount codes data
INSERT INTO discount_codes (id, code, created_at, expires_at, course_id, set_price, percent, max_count, client, uuid) VALUES
(1, 'Admin_Free_D40', '2023-07-24 13:36:54.623907-04', NULL, 1, 0.00, NULL, 10, NULL, NULL),
(2, 'ReturningStudentD', '2023-07-26 14:09:49.002313-04', NULL, 1, 0.00, NULL, 5, NULL, NULL),
(3, 'ReturningStudentG', '2023-07-26 14:09:51.793283-04', NULL, 3, 0.00, NULL, 5, NULL, NULL),
(4, 'Guardian_Sec_D40', '2023-09-19 13:24:41.536933-04', NULL, 1, 0.00, NULL, 20, 'Guardian Professional Security', '02d1d93b-82aa-44be-a095-94192b447993'),
(5, 'FL_Panthers_D40', '2023-09-25 11:57:45.132432-04', NULL, 1, 0.00, NULL, 25, 'Florida Panthers', '3f8ffd3a-9d52-4a4b-9441-42b050906c6a'),
(6, 'Allied_Pensacola_D40', '2023-10-02 15:07:01.037599-04', NULL, 1, 0.00, NULL, 351, 'Allied Pensacola', '2c5a35d4-59f1-48f6-b1bf-9666161a6885'),
(7, 'Allied_Lauderdale_G28', '2023-10-09 14:10:23.816145-04', NULL, 3, 0.00, NULL, 20, 'Allied Ft. Lauderdale', '3729e5c5-4792-4949-9fff-fec87d3a371a'),
(8, 'Allied_Jacksonville_D40', '2023-10-20 15:47:27.498293-04', NULL, 1, 0.00, NULL, 10, 'Allied Jacksonville', '5594580a-8a49-4163-bcc5-c03b25199401'),
(9, 'Allied_Tampa_D40', '2023-11-28 12:55:25.348155-05', NULL, 1, 0.00, NULL, 10, 'Allied Tampa', '47e811a9-ac73-4606-8e25-e7f7431ef22e'),
(10, 'FSResidential_D40', '2023-12-06 12:27:20.90418-05', NULL, 1, 0.00, NULL, 17, 'First Residential', 'b56df24b-0677-4efa-b76a-b7667404cf10'),
(11, 'Two_by_Two_D40', '2024-01-16 13:49:43.792973-05', NULL, 1, 0.00, NULL, 6, 'Two by Two', '90aefffe-5f2d-428d-af12-de95c04a367f'),
(12, 'Allied_Maitland_D40', '2024-01-16 13:55:24.36778-05', NULL, 1, 0.00, NULL, 140, 'Allied Maitland', '54a6789a-655e-403c-88d1-8035828444b3'),
(13, 'GardaWorld_D40_TPA', '2024-01-25 09:51:46.869327-05', NULL, 1, 0.00, NULL, 10, 'GardaWorld Tampa', 'ea7024b2-b63b-4fff-bb57-3cb17d45037d'),
(14, 'GardaWorld_D40_JAX', '2024-02-20 14:12:01.611695-05', NULL, 1, 0.00, NULL, 10, 'GardaWorld Jacksonville', '2114b075-c43a-4ec1-8878-1bad213a57b0'),
(15, 'Admin_D40_2024', '2024-04-10 16:54:04.064109-04', NULL, 1, 0.00, NULL, 70, NULL, NULL),
(16, 'Admin_G28_2024', '2024-04-10 16:54:04.067688-04', NULL, 3, 0.00, NULL, 70, NULL, NULL),
(17, 'FLDOL_D40', '2024-04-26 11:58:28.185181-04', NULL, 1, 0.00, NULL, 1, NULL, '86b57add-877d-4330-9dfe-ea6f868f8a82'),
(18, 'FLDOL_G28', '2024-04-26 11:58:47.444206-04', NULL, 3, 0.00, NULL, 1, NULL, '939ee63c-dc39-4431-a7e8-07ac45576b7b'),
(19, 'ZooTampa_D40', '2024-05-31 10:08:42.798747-04', NULL, 1, 0.00, NULL, 4, 'Zoo Tampa', 'a23cf602-1f77-4d73-89c8-3eebbc57e41a'),
(20, 'USSA_D40', '2024-06-21 18:54:40.621752-04', NULL, 1, 0.00, NULL, 5, 'eric@ussa.us', '4c923a2d-a879-442d-8dd1-8ae7dc61a211'),
(21, 'InterCont_Miami_D40', '2024-08-23 10:23:07.410689-04', NULL, 1, 0.00, NULL, 12, 'InterContinental Miami', '748ca7d1-b9d1-44be-a5bf-0da456a69c6d')
ON CONFLICT (id) DO UPDATE SET
    code = EXCLUDED.code,
    created_at = EXCLUDED.created_at,
    expires_at = EXCLUDED.expires_at,
    course_id = EXCLUDED.course_id,
    set_price = EXCLUDED.set_price,
    percent = EXCLUDED.percent,
    max_count = EXCLUDED.max_count,
    client = EXCLUDED.client,
    uuid = EXCLUDED.uuid;

-- Update the sequence to the correct value
SELECT setval('discount_codes_id_seq', 21, true);
