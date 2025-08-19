<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * All admin accounts use the global password: Secure$101
     */
    public function run(): void
    {
        // Global password for all admin accounts: Secure$101
        $globalPassword = Hash::make('Secure$101');

        $adminUsers = [
            // sys_admin (role_id: 1)
            [
                'id' => 1,
                'is_active' => true,
                'role_id' => 1,
                'lname' => 'Jones',
                'fname' => 'Chris',
                'email' => 'jonesy@cisworldservices.org',
                'created_at' => '2023-07-24 13:36:54',
                'updated_at' => '2023-09-02 13:16:51',
                'email_verified_at' => '2023-07-24 13:36:54',
                'password' => $globalPassword,
                'remember_token' => 'CnnsKgW4BWlHS69UYQ28deDoNhz4hDSqLmIezGl20wKnTQa21',
                'avatar' => null,
                'use_gravatar' => false,
                'student_info' => '{"dob":"1975-09-01T04:00:00.000Z","fname":"Chris"}',
                'email_opt_in' => false
            ],
            [
                'id' => 2,
                'is_active' => true,
                'role_id' => 1,
                'lname' => 'Clark',
                'fname' => 'Richard',
                'email' => 'richievc@gmail.com',
                'created_at' => '2023-07-24 13:36:54',
                'updated_at' => '2024-05-02 20:54:28',
                'email_verified_at' => '2023-07-24 13:36:54',
                'password' => $globalPassword,
                'remember_token' => 'VmO8F50mazEUegme1DCZaeGD5bHHh863zix9tkdKDWWN0ENJb',
                'avatar' => null,
                'use_gravatar' => false,
                'student_info' => '{"initials":"v","dob":"1985-04-25","suffix":null}',
                'email_opt_in' => false
            ],

            // admin (role_id: 2)
            [
                'id' => 3,
                'is_active' => true,
                'role_id' => 2,
                'lname' => 'Gundry',
                'fname' => 'Craig',
                'email' => 'cgundry@kkpsecuritygroup.com',
                'created_at' => '2023-07-24 13:36:54',
                'updated_at' => '2023-07-27 16:39:52',
                'email_verified_at' => '2023-07-24 13:36:54',
                'password' => $globalPassword,
                'remember_token' => 'SZbZdV0al95PccydDrmBv4UzFwCgpstETced3yXssFPde5qtb',
                'avatar' => null,
                'use_gravatar' => false,
                'student_info' => null,
                'email_opt_in' => false
            ],
            [
                'id' => 4,
                'is_active' => true,
                'role_id' => 2,
                'lname' => 'Gundry',
                'fname' => 'Sandra',
                'email' => 'sgundry@s2institute.com',
                'created_at' => '2023-07-24 13:36:54',
                'updated_at' => '2023-07-27 16:39:52',
                'email_verified_at' => '2023-07-24 13:36:54',
                'password' => $globalPassword,
                'remember_token' => 'qSc17VHKwRLg2gHTcDjBkAbRn6eNPCxIeId3i5XuCu1O33dEy',
                'avatar' => null,
                'use_gravatar' => false,
                'student_info' => null,
                'email_opt_in' => false
            ],
            [
                'id' => 5,
                'is_active' => true,
                'role_id' => 2,
                'lname' => 'Poulin',
                'fname' => 'KC',
                'email' => 'kc@cisworldservices.org',
                'created_at' => '2023-07-24 13:36:54',
                'updated_at' => '2023-07-27 16:39:52',
                'email_verified_at' => '2023-07-24 13:36:54',
                'password' => $globalPassword,
                'remember_token' => 'Ri7HGEsrzGhEHLgad3dXYeHnlUOZDMIUTQuiUQBp1aynOEK3n',
                'avatar' => null,
                'use_gravatar' => false,
                'student_info' => null,
                'email_opt_in' => false
            ],
            [
                'id' => 6,
                'is_active' => false,
                'role_id' => 2,
                'lname' => 'Casey',
                'fname' => 'Ashley',
                'email' => 'ashley@s2institute.com',
                'created_at' => '2023-07-24 13:36:54',
                'updated_at' => '2023-08-11 15:07:47',
                'email_verified_at' => '2023-07-24 13:36:54',
                'password' => $globalPassword,
                'remember_token' => '1MUe3gWeQLKqTD9phpOgJASXwjjoOaQhg8RyhVCvUX6jVisC2',
                'avatar' => null,
                'use_gravatar' => false,
                'student_info' => null,
                'email_opt_in' => false
            ],
            [
                'id' => 7,
                'is_active' => true,
                'role_id' => 2,
                'lname' => 'Pace',
                'fname' => 'Jay',
                'email' => 'pacejf@s2institute.com',
                'created_at' => '2023-07-24 13:36:54',
                'updated_at' => '2023-07-27 16:39:52',
                'email_verified_at' => '2023-07-24 13:36:54',
                'password' => $globalPassword,
                'remember_token' => '9r7zwbKRUJhhPnb1OcaBJi7iTZve36PTfeTKnrJEhPEpFR8ID',
                'avatar' => null,
                'use_gravatar' => false,
                'student_info' => null,
                'email_opt_in' => false
            ],
            [
                'id' => 8,
                'is_active' => true,
                'role_id' => 2,
                'lname' => 'Miller',
                'fname' => 'Patrick',
                'email' => 'pjmiller@stgroupusa.com',
                'created_at' => '2023-07-24 13:36:54',
                'updated_at' => '2023-08-31 19:28:51',
                'email_verified_at' => '2023-07-24 13:36:54',
                'password' => $globalPassword,
                'remember_token' => 'AecvEa2Y3ceYEJzonBCpIUeeqec4mrcGEkmRDAcnAEyh3U1oY',
                'avatar' => null,
                'use_gravatar' => false,
                'student_info' => '{"fname":"Patrick","initial":"","lname":"Miller"}',
                'email_opt_in' => false
            ],
            [
                'id' => 9,
                'is_active' => true,
                'role_id' => 2,
                'lname' => 'Noblin',
                'fname' => 'Phil',
                'email' => 'pnoblin@stgroupusa.com',
                'created_at' => '2023-07-24 13:36:54',
                'updated_at' => '2023-07-27 16:39:52',
                'email_verified_at' => '2023-07-24 13:36:54',
                'password' => $globalPassword,
                'remember_token' => 'LHMhJm0gy8Meb0uGZQIXHYzarbY9p52XRzuhMFyc2zc4dELK6',
                'avatar' => null,
                'use_gravatar' => false,
                'student_info' => null,
                'email_opt_in' => false
            ],
            [
                'id' => 10,
                'is_active' => true,
                'role_id' => 2,
                'lname' => 'Steiman',
                'fname' => 'Scott',
                'email' => 'ssteiman@stgroupusa.com',
                'created_at' => '2023-07-24 13:36:54',
                'updated_at' => '2025-05-21 14:49:06',
                'email_verified_at' => '2023-07-24 13:36:54',
                'password' => $globalPassword,
                'remember_token' => 'xwzsl6H7CffTZTuiPGoSKAMrKf9foNRJZDvJFFwU2JcxXQ2m0',
                'avatar' => null,
                'use_gravatar' => false,
                'student_info' => '{"fname":"Scott","initial":"j","lname":"Steiman"}',
                'email_opt_in' => false
            ],
            [
                'id' => 11,
                'is_active' => true,
                'role_id' => 2,
                'lname' => 'Rodriguez',
                'fname' => 'Hector',
                'email' => 'hrodriguez@cisworldservices.org',
                'created_at' => '2023-07-24 13:36:54',
                'updated_at' => '2023-09-12 13:22:58',
                'email_verified_at' => '2023-07-24 13:36:54',
                'password' => $globalPassword,
                'remember_token' => 'UPnL7aPasGOQQMnhtXf6bKFOBge5NYJa7ZdXV3s45t9xSYiiC',
                'avatar' => null,
                'use_gravatar' => false,
                'student_info' => '{"dob":"2023-09-12T04:00:00.000Z","fname":"Hector"}',
                'email_opt_in' => false
            ],
            [
                'id' => 19,
                'is_active' => true,
                'role_id' => 2,
                'lname' => 'Madonia',
                'fname' => 'Michelle',
                'email' => 'mmadonia@kkpsecuritygroup.com',
                'created_at' => '2023-07-27 16:39:39',
                'updated_at' => '2023-07-27 16:39:52',
                'email_verified_at' => '2023-07-27 16:39:39',
                'password' => $globalPassword,
                'remember_token' => 'lDtJNhtTnQ739AcKifo0sL9SMt5Vqbjggr2C7eITRzPyWrS6A',
                'avatar' => null,
                'use_gravatar' => false,
                'student_info' => null,
                'email_opt_in' => false
            ],

            // support (role_id: 3)
            [
                'id' => 5000,
                'is_active' => true,
                'role_id' => 3,
                'lname' => 'Manager',
                'fname' => 'Support',
                'email' => 'support@floridaonlinesecuritytraining.com',
                'created_at' => '2023-12-05 13:05:33',
                'updated_at' => '2024-05-03 15:46:42',
                'email_verified_at' => '2023-12-05 13:05:33',
                'password' => $globalPassword,
                'remember_token' => 'tg3lonkzyJFfxDatrm7xcifSnT58lHyajrBNF5zBxsCVG7uhB',
                'avatar' => null,
                'use_gravatar' => false,
                'student_info' => null,
                'email_opt_in' => false
            ],
            [
                'id' => 5999,
                'is_active' => true,
                'role_id' => 3,
                'lname' => 'Test',
                'fname' => 'Support',
                'email' => 'support_test@floridaonlinesecuritytraining.com',
                'created_at' => '2024-04-30 14:01:05',
                'updated_at' => '2024-04-30 14:01:05',
                'email_verified_at' => '2024-04-30 14:01:04',
                'password' => $globalPassword,
                'remember_token' => 'T8RI1zvfANItuvtZSrqpv3EF0ROAO1wLMdGof8kT8Qim2Dl8r',
                'avatar' => null,
                'use_gravatar' => false,
                'student_info' => null,
                'email_opt_in' => false
            ],

            // instructor (role_id: 4)
            [
                'id' => 12,
                'is_active' => true,
                'role_id' => 4,
                'lname' => 'Conover',
                'fname' => 'Sean',
                'email' => 'contactrightatp@yahoo.com',
                'created_at' => '2023-07-24 13:36:54',
                'updated_at' => '2023-07-27 16:39:52',
                'email_verified_at' => '2023-07-24 13:36:54',
                'password' => $globalPassword,
                'remember_token' => 'yX2jFxGCOU56Aj0bkbwmXqysvDvieu7x7twPpuhhwF0vHaqC8',
                'avatar' => null,
                'use_gravatar' => false,
                'student_info' => null,
                'email_opt_in' => false
            ],
            [
                'id' => 13,
                'is_active' => true,
                'role_id' => 4,
                'lname' => 'Cruz',
                'fname' => 'Ubaldo',
                'email' => 'neworleansbg1@gmail.com',
                'created_at' => '2023-07-24 13:36:54',
                'updated_at' => '2023-07-27 16:39:52',
                'email_verified_at' => '2023-07-24 13:36:54',
                'password' => $globalPassword,
                'remember_token' => 'wyjzV3eT3XxKIGHRDFKkNvX0DecHsOxB3ZaZGHw7zl00K47Hr',
                'avatar' => null,
                'use_gravatar' => false,
                'student_info' => null,
                'email_opt_in' => false
            ],
            [
                'id' => 14,
                'is_active' => true,
                'role_id' => 4,
                'lname' => 'Palumbo',
                'fname' => 'Scott',
                'email' => 'spalumbo308@gmail.com',
                'created_at' => '2023-07-24 13:36:54',
                'updated_at' => '2024-01-08 08:13:59',
                'email_verified_at' => '2023-07-24 13:36:54',
                'password' => $globalPassword,
                'remember_token' => 'j09VQ9OrtmctaOky4gwTs5a867LVuSDvIn6MJBw8AZUCx6KLE',
                'avatar' => null,
                'use_gravatar' => false,
                'student_info' => '{"dob":"06\/26\/1968","fname":"Scott","initial":null}',
                'email_opt_in' => false
            ]
        ];

        // Clear existing admin users (role_id <= 4)
        DB::table('users')->where('role_id', '<=', 4)->delete();

        // Insert new admin users
        foreach ($adminUsers as $user) {
            DB::table('users')->insert($user);
        }

        echo "Seeded " . count($adminUsers) . " admin users successfully.\n";
    }
}
