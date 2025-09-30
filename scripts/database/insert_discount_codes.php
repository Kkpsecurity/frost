<?php

use Illuminate\Support\Facades\DB;

// First, let's see what discount codes currently exist
echo "Current discount codes:\n";
$existing = DB::table('discount_codes')->get();
foreach ($existing as $code) {
    echo "ID: {$code->id}, Code: {$code->code}\n";
}

echo "\n--- Inserting new discount codes ---\n";

// Discount codes data to insert
$discountCodes = [
    ['id' => 1, 'code' => 'Admin_Free_D40', 'created_at' => '2023-07-24 13:36:54.623907-04', 'expires_at' => null, 'course_id' => 1, 'set_price' => 0.00, 'percent' => null, 'max_count' => 10, 'client' => null, 'uuid' => null],
    ['id' => 2, 'code' => 'ReturningStudentD', 'created_at' => '2023-07-26 14:09:49.002313-04', 'expires_at' => null, 'course_id' => 1, 'set_price' => 0.00, 'percent' => null, 'max_count' => 5, 'client' => null, 'uuid' => null],
    ['id' => 3, 'code' => 'ReturningStudentG', 'created_at' => '2023-07-26 14:09:51.793283-04', 'expires_at' => null, 'course_id' => 3, 'set_price' => 0.00, 'percent' => null, 'max_count' => 5, 'client' => null, 'uuid' => null],
    ['id' => 4, 'code' => 'Guardian_Sec_D40', 'created_at' => '2023-09-19 13:24:41.536933-04', 'expires_at' => null, 'course_id' => 1, 'set_price' => 0.00, 'percent' => null, 'max_count' => 20, 'client' => 'Guardian Professional Security', 'uuid' => '02d1d93b-82aa-44be-a095-94192b447993'],
    ['id' => 5, 'code' => 'FL_Panthers_D40', 'created_at' => '2023-09-25 11:57:45.132432-04', 'expires_at' => null, 'course_id' => 1, 'set_price' => 0.00, 'percent' => null, 'max_count' => 25, 'client' => 'Florida Panthers', 'uuid' => '3f8ffd3a-9d52-4a4b-9441-42b050906c6a'],
    ['id' => 6, 'code' => 'Allied_Pensacola_D40', 'created_at' => '2023-10-02 15:07:01.037599-04', 'expires_at' => null, 'course_id' => 1, 'set_price' => 0.00, 'percent' => null, 'max_count' => 351, 'client' => 'Allied Pensacola', 'uuid' => '2c5a35d4-59f1-48f6-b1bf-9666161a6885'],
    ['id' => 7, 'code' => 'Allied_Lauderdale_G28', 'created_at' => '2023-10-09 14:10:23.816145-04', 'expires_at' => null, 'course_id' => 3, 'set_price' => 0.00, 'percent' => null, 'max_count' => 20, 'client' => 'Allied Ft. Lauderdale', 'uuid' => '3729e5c5-4792-4949-9fff-fec87d3a371a'],
    ['id' => 8, 'code' => 'Allied_Jacksonville_D40', 'created_at' => '2023-10-20 15:47:27.498293-04', 'expires_at' => null, 'course_id' => 1, 'set_price' => 0.00, 'percent' => null, 'max_count' => 10, 'client' => 'Allied Jacksonville', 'uuid' => '5594580a-8a49-4163-bcc5-c03b25199401'],
    ['id' => 9, 'code' => 'Allied_Tampa_D40', 'created_at' => '2023-11-28 12:55:25.348155-05', 'expires_at' => null, 'course_id' => 1, 'set_price' => 0.00, 'percent' => null, 'max_count' => 10, 'client' => 'Allied Tampa', 'uuid' => '47e811a9-ac73-4606-8e25-e7f7431ef22e'],
    ['id' => 10, 'code' => 'FSResidential_D40', 'created_at' => '2023-12-06 12:27:20.90418-05', 'expires_at' => null, 'course_id' => 1, 'set_price' => 0.00, 'percent' => null, 'max_count' => 17, 'client' => 'First Residential', 'uuid' => 'b56df24b-0677-4efa-b76a-b7667404cf10'],
    ['id' => 11, 'code' => 'Two_by_Two_D40', 'created_at' => '2024-01-16 13:49:43.792973-05', 'expires_at' => null, 'course_id' => 1, 'set_price' => 0.00, 'percent' => null, 'max_count' => 6, 'client' => 'Two by Two', 'uuid' => '90aefffe-5f2d-428d-af12-de95c04a367f'],
    ['id' => 12, 'code' => 'Allied_Maitland_D40', 'created_at' => '2024-01-16 13:55:24.36778-05', 'expires_at' => null, 'course_id' => 1, 'set_price' => 0.00, 'percent' => null, 'max_count' => 140, 'client' => 'Allied Maitland', 'uuid' => '54a6789a-655e-403c-88d1-8035828444b3'],
    ['id' => 13, 'code' => 'GardaWorld_D40_TPA', 'created_at' => '2024-01-25 09:51:46.869327-05', 'expires_at' => null, 'course_id' => 1, 'set_price' => 0.00, 'percent' => null, 'max_count' => 10, 'client' => 'GardaWorld Tampa', 'uuid' => 'ea7024b2-b63b-4fff-bb57-3cb17d45037d'],
    ['id' => 14, 'code' => 'GardaWorld_D40_JAX', 'created_at' => '2024-02-20 14:12:01.611695-05', 'expires_at' => null, 'course_id' => 1, 'set_price' => 0.00, 'percent' => null, 'max_count' => 10, 'client' => 'GardaWorld Jacksonville', 'uuid' => '2114b075-c43a-4ec1-8878-1bad213a57b0'],
    ['id' => 15, 'code' => 'Admin_D40_2024', 'created_at' => '2024-04-10 16:54:04.064109-04', 'expires_at' => null, 'course_id' => 1, 'set_price' => 0.00, 'percent' => null, 'max_count' => 70, 'client' => null, 'uuid' => null],
    ['id' => 16, 'code' => 'Admin_G28_2024', 'created_at' => '2024-04-10 16:54:04.067688-04', 'expires_at' => null, 'course_id' => 3, 'set_price' => 0.00, 'percent' => null, 'max_count' => 70, 'client' => null, 'uuid' => null],
    ['id' => 17, 'code' => 'FLDOL_D40', 'created_at' => '2024-04-26 11:58:28.185181-04', 'expires_at' => null, 'course_id' => 1, 'set_price' => 0.00, 'percent' => null, 'max_count' => 1, 'client' => null, 'uuid' => '86b57add-877d-4330-9dfe-ea6f868f8a82'],
    ['id' => 18, 'code' => 'FLDOL_G28', 'created_at' => '2024-04-26 11:58:47.444206-04', 'expires_at' => null, 'course_id' => 3, 'set_price' => 0.00, 'percent' => null, 'max_count' => 1, 'client' => null, 'uuid' => '939ee63c-dc39-4431-a7e8-07ac45576b7b'],
    ['id' => 19, 'code' => 'ZooTampa_D40', 'created_at' => '2024-05-31 10:08:42.798747-04', 'expires_at' => null, 'course_id' => 1, 'set_price' => 0.00, 'percent' => null, 'max_count' => 4, 'client' => 'Zoo Tampa', 'uuid' => 'a23cf602-1f77-4d73-89c8-3eebbc57e41a'],
    ['id' => 20, 'code' => 'USSA_D40', 'created_at' => '2024-06-21 18:54:40.621752-04', 'expires_at' => null, 'course_id' => 1, 'set_price' => 0.00, 'percent' => null, 'max_count' => 5, 'client' => 'eric@ussa.us', 'uuid' => '4c923a2d-a879-442d-8dd1-8ae7dc61a211'],
    ['id' => 21, 'code' => 'InterCont_Miami_D40', 'created_at' => '2024-08-23 10:23:07.410689-04', 'expires_at' => null, 'course_id' => 1, 'set_price' => 0.00, 'percent' => null, 'max_count' => 12, 'client' => 'InterContinental Miami', 'uuid' => '748ca7d1-b9d1-44be-a5bf-0da456a69c6d'],
];

DB::beginTransaction();
try {
    // Insert or update each discount code
    foreach ($discountCodes as $codeData) {
        $existing = DB::table('discount_codes')->where('id', $codeData['id'])->first();

        if ($existing) {
            DB::table('discount_codes')->where('id', $codeData['id'])->update($codeData);
            echo "Updated discount code: {$codeData['code']} (ID: {$codeData['id']})\n";
        } else {
            DB::table('discount_codes')->insert($codeData);
            echo "Inserted discount code: {$codeData['code']} (ID: {$codeData['id']})\n";
        }
    }

    // Update the sequence
    DB::statement("SELECT setval('discount_codes_id_seq', 21, true)");

    DB::commit();
    echo "\n✅ All discount codes have been successfully inserted/updated!\n";
    echo "✅ Sequence updated to 21\n";

    // Show final count
    $finalCount = DB::table('discount_codes')->count();
    echo "\nFinal discount codes count: {$finalCount}\n";

} catch (Exception $e) {
    DB::rollback();
    echo "\n❌ Error occurred: " . $e->getMessage() . "\n";
}
