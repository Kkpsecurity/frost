<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ZoomCredsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $zoomCreds = [
            [
                'id' => 1,
                'zoom_email' => 'instructor_admin@stgroupusa.com',
                'zoom_password' => 'eyJpdiI6IkhkQ1lDZlNUSjBid1gzL0dyQ2toaVE9PSIsInZhbHVlIjoiVkJvUWJhTzBzN1cyRXk2TzNxVXBtL2gvRmZFay9uUGIxaEI0amFBalB4dz0iLCJtYWMiOiI5YmU4MGEyNzI3NDI4MDEwMjE2NTQ4ZjljYTg3ZWQ4YTgwYmQ1YTFjM2VlOTY2YzdkMTE1MjQ2NzM1OGNlNGIzIiwidGFnIjoiIn0=',
                'zoom_passcode' => 'eyJpdiI6IkYyeHpVWURIUUtJZjZLRUh3OGl2UXc9PSIsInZhbHVlIjoiT2FKdjlnSUlPL0JOQ09FVHhhcGNGQT09IiwibWFjIjoiMzVmMzcwOWZmZTRhOTc4NzJiNjgyMWZhODEzMmRlNWE1MWM4ZWM0ZTdkYzI1NDQ0NzM4NDUxYzZmMmE0MzMwOCIsInRhZyI6IiJ9',
                'zoom_status' => 'disabled',
                'pmi' => '9024930994',
                'use_pmi' => true
            ],
            [
                'id' => 2,
                'zoom_email' => 'instructor_d@stgroupusa.com',
                'zoom_password' => 'eyJpdiI6IitiQ3BIYW1kQy9OSGZXd1VsaDUzVXc9PSIsInZhbHVlIjoiT2MycFQ5NWRLellqWkFDbFppZ2FyY3d3RnNqbGVwQ3BZakFCSEwyUlZsQT0iLCJtYWMiOiJiMGYyNDI3MGVkN2JkMTA0M2Q1ZjdhZDQzMTg5ZTYyMjM3OTdiMWNjMDgxZGEzM2U5MTY5NjU5M2YwMzFmNmFmIiwidGFnIjoiIn0=',
                'zoom_passcode' => 'eyJpdiI6IjNkZlFaSXBhWTJqNW8xSE9DQ3hJcXc9PSIsInZhbHVlIjoiWDhRTFZudmpHNXR3Y0NKcUx4QUh1Zz09IiwibWFjIjoiZDMyNTY1MjcxNjBiMDk5MDU5NTEwNTFmOWM1OTRiZWVlNmRkYzNlNjFkYzg2ZDE1ZjRiY2I3MTk2ZDc4ZGI3MyIsInRhZyI6IiJ9',
                'zoom_status' => 'disabled',
                'pmi' => '3624812631',
                'use_pmi' => true
            ],
            [
                'id' => 3,
                'zoom_email' => 'instructor_g@stgroupusa.com',
                'zoom_password' => 'eyJpdiI6ImlJYzlucGxpUXNlTHdBRVh3RFFDekE9PSIsInZhbHVlIjoiNEVCRGdSMUJMcDc3MXRxYWFrR2lUdHhqaWpVWS9qUUxUaEYrc2F5MHpFYz0iLCJtYWMiOiJiNzIyMGYxYjllZmVkNWM3OWY0NzhkOTZlZTNlNDE1NjNjNTExNTBjN2JhODg1MjhhMjYyNTI5NGMwMTU5NTg3IiwidGFnIjoiIn0=',
                'zoom_passcode' => 'eyJpdiI6IlZwWTFJYnZRNDBhbCs4VWUwRTk0QUE9PSIsInZhbHVlIjoiTEp1cks1VWNmRWZMcW1SOWNmY2IwQT09IiwibWFjIjoiZmY4ZGMwODc0OGFmNjdjMmU0MjJmODJiZjQ0YmFmNWM1ZjhlNzc3YzAyODRlYzIzYWZkYzg1MmVjZTIxZDg1NyIsInRhZyI6IiJ9',
                'zoom_status' => 'disabled',
                'pmi' => '2593826971',
                'use_pmi' => true
            ]
        ];

        // Clear existing data
        DB::table('zoom_creds')->truncate();

        // Insert new data
        foreach ($zoomCreds as $zoomCred) {
            DB::table('zoom_creds')->insert($zoomCred);
        }

        echo "Seeded " . count($zoomCreds) . " Zoom credentials successfully.\n";
    }
}
