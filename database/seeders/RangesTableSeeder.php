<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RangesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ranges = [
            [
                'id' => 1,
                'is_active' => false,
                'name' => 'Palm Beach Shooting Center',
                'city' => 'Boynton Beach',
                'address' => "501 Industrial St\nLake Worth Beach, FL 33461",
                'inst_name' => 'Scott Steiman',
                'inst_email' => 'ssteiman@stgroupusa.com',
                'inst_phone' => null,
                'price' => 150.00,
                'times' => '1000 - 2000',
                'appt_only' => false,
                'range_html' => "<b>School Address:</b>\n3200 S. Congress Ave, Suite #203\nBoynton Beach, FL 33426\n\n<b>Report to the school first.</b> This is an indoor range, students must wear proper range attire, long pants with a belt, long or short sleeve high collar shirt. Closed toe shoes. Firearms and gear are available for rent at no charge. Ammunition is available for purchase at $25 per box of 9mm. Students will get a break for lunch (one hour) there is a refrigerator, microwave and water dispenser if students care to bring their own food and drinks. Students will conduct dry fire training from 0800-1200, at 1200hrs instructors will administer the safety check exam, students must pass the safety check to continue on to live fire training. The lunch break will be from 1300-1400. The live fire portion of training will not begin until 1800hrs and conclude not later than 2000hrs."
            ],
            [
                'id' => 2,
                'is_active' => false,
                'name' => 'Wyoming Antelope Club',
                'city' => 'Clearwater',
                'address' => "3700 126th Ave N\nClearwater, FL 33762",
                'inst_name' => 'Kelly Flanagan',
                'inst_email' => 'psg.ceo1@gmail.com',
                'inst_phone' => '(813) 928-4120',
                'price' => 150.00,
                'times' => '0730 - 1600',
                'appt_only' => false,
                'range_html' => "The Wyoming Antelope Club has very strict policy concerning the speed limit, which is 9mph. If you are seen on camera driving faster than the speed limit, you will be asked to depart the range and not return.\n\nOnce you arrive, drive on the entrance road not faster than the 9mph limit and you will come upon the \"main range area\" continue until you see a sign that says \"Concealed Weapons Classes Turn here\" at which time you will turn left and continue south toward the only two male and female restrooms. The range you will be training on is the last range on the property located on the southernmost portion of the property.\n\nThis is an outdoor range. Students not properly attired will not be permitted on the range. Long pants with a belt, high collar shirt, closed toe shoes, no shorts, no flip-flops, no tank tops. Students must wear a hat, baseball style recommended. No red or orange colored shirts.\n\nFirearms: if you are bringing your own duty firearm ensure it is unloaded, magazines (at least 3-4) are unloaded and all firearms and equipment shall be left in your vehicle until the Instructor directs you to retrieve it. Holsters must be outside the waistband, duty type retention holsters.\n\nIf you need to qualify with any caliber other than 9mm you must bring your own firearm and ammunition.\n\nThere is a fee to qualify with multiple calibers. Contact the school for details.\n\n<b>Ammunition is not included in the course price.</b>\n\nIf you are bringing your own ammunition, you need 144 rounds. If you need to purchase ammunition, exact cash only, no credit/debit, you must contact the school for pricing and to pre-purchase ammunition at 1-888-822-2948, 1-813-928-4120 or <a href=\"mailto:psg.academy.info@gmail.com\">psg.academy.info@gmail.com</a>"
            ],
            [
                'id' => 3,
                'is_active' => false,
                'name' => 'American Police Hall of Fame Range',
                'city' => 'Cocoa',
                'address' => "1365 N. Courtenay Parkway Suite B\nMerritt Island, FL 32952",
                'inst_name' => 'Sean Conover',
                'inst_email' => 'contactrightatp@yahoo.com',
                'inst_phone' => '(321) 506-6058',
                'price' => 125.00,
                'times' => '0800 - 1700',
                'appt_only' => false,
                'range_html' => "<b>School Address:</b>\n3700 N Courtenay Pkwy STE 112\nMerritt Island, FL 32953\n\n<b>Students must report to the school</b> for dry fire training from 1200 - 1630 on the first of the two scheduled days.\n\n<b>Students must report to the school</b> on range day at 1030 and will then travel to the range to qualify. The training will be over at approximately 1430.\n\nFirearms and ammunition are available for rent/purchase, contact instructor for pricing. This is an indoor range, proper range attire is required."
            ],
            [
                'id' => 4,
                'is_active' => false,
                'name' => 'Florida Gun Center',
                'city' => 'Hialeah',
                'address' => "1770 W 38th Pl, \nHialeah, FL 33012",
                'inst_name' => 'Scott J. Palumbo',
                'inst_email' => 'spalumbo308@gmail.com',
                'inst_phone' => '(561) 232-9312',
                'price' => 150.00,
                'times' => '1100 - 1900',
                'appt_only' => false,
                'range_html' => "Park across the street from the range in the Westland Promenade parking lot. The classroom is on the 2nd floor above the range. $125 includes range fee. Students can purchase 3 boxes of 50 rounds at current market price. Firearm rental with all gear $25. This is an indoor range, proper range attire is required."
            ],
            [
                'id' => 5,
                'is_active' => false,
                'name' => 'Saltwaters Shooting Club',
                'city' => 'St. Augustine',
                'address' => "900 Big Oak Rd\nSt. Augustine, FL 32095",
                'inst_name' => 'Dave Charles',
                'inst_email' => 'dave.hardpointtraining@gmail.com',
                'inst_phone' => '(904) 372-8803',
                'price' => 125.00,
                'times' => '0900 - 1700',
                'appt_only' => false,
                'range_html' => "Ammo is for sale at $30 a box, $25 for firearm and gear rental.\n\nPark closest to the camouflage painted building on the right as you enter, the classroom is located inside.\n\nThis is an outdoor range, proper range attire is required. Plan for inclement weather. Shorts with a sturdy belt are allowed, otherwise long pants with a sturdy belt, long or short sleeve high collar shirt, no open toed shoes. Hats are strongly encouraged. No orange/red shirts or hats."
            ],
            [
                'id' => 6,
                'is_active' => false,
                'name' => 'Firearms Training Club of America',
                'city' => 'Lakeland',
                'address' => "1421 Fish Hatchery Rd\nLakeland, FL 33801",
                'inst_name' => 'Henry Brieger',
                'inst_email' => 'info@lakelandfirearms.com',
                'inst_phone' => '(863) 617-8787',
                'price' => 125.00,
                'times' => '0800 - 1600',
                'appt_only' => false,
                'range_html' => "<b>School Address:</b>\n1709 E Memorial Blvd, \nLakeland, FL 33801\n\n<b>Students report to the school first.</b> This is an outdoor range, proper range attire is required. Closed toe shoes, long pants with a belt, no shorts. Shirts must have a high collar. Hat is strongly recommended. Plan for inclement weather. Ammunition may be purchased at current market price or students can bring their own at least 144 rounds."
            ],
            [
                'id' => 7,
                'is_active' => false,
                'name' => 'Groveland Range',
                'city' => 'Orlando',
                'address' => "6800 FL-50\nGroveland, FL 34736",
                'inst_name' => 'Sean Conover',
                'inst_email' => 'contactrightatp@yahoo.com',
                'inst_phone' => '(321) 506-6058',
                'price' => 125.00,
                'times' => 'Day 1: 1200 - 1630; Day 2: 0800 - 1200',
                'appt_only' => false,
                'range_html' => "<b>School Address:</b>\n5104 N Orange Blossom Trail\nSuite #222\nOrlando, FL 32810\n\n<b>Students must report to the school</b> for dry fire training from 1200 - 1630 on the first of the two scheduled days.\n\n<b>Students must report to the range</b> on the second of the two scheduled days from 0800 - 1200.\n\nFirearms and ammunition are available for rent/purchase, contact instructor for pricing. This is an outdoor range, proper range attire is required, plan for inclement weather."
            ],
            [
                'id' => 8,
                'is_active' => false,
                'name' => 'Declaration Defense',
                'city' => 'Pompano Beach',
                'address' => "1315 SW 1st Ct.\nPompano Beach, FL 33069",
                'inst_name' => 'Scott J. Palumbo',
                'inst_email' => 'spalumbo308@gmail.com',
                'inst_phone' => '(561) 232-9312',
                'price' => 150.00,
                'times' => '1100 - 1900',
                'appt_only' => false,
                'range_html' => "The classroom is on the 2nd floor. $125 includes range fee. Students can purchase 3 boxes of 50 rounds at current market price. Firearm rental with all gear $25. This is an indoor range, proper range attire is required."
            ],
        ];

        foreach ($ranges as $range) {
            DB::table('ranges')->insert($range);
        }

        // Add remaining ranges (9-14 and special entry -1)
        $ranges2 = [
            [
                'id' => 9,
                'is_active' => false,
                'name' => 'Gator Guns & Archery Center',
                'city' => 'West Palm Beach',
                'address' => "2154 Zip Code Pl. STE 7\nWest Palm Beach, FL 33409",
                'inst_name' => 'James Dingle',
                'inst_email' => 'jdingle2001@yahoo.com',
                'inst_phone' => '(561) 727-4391',
                'price' => 125.00,
                'times' => 'Day 1: 1200 - 1600; Day 2: 0800 - 1200',
                'appt_only' => false,
                'range_html' => "<b>Special Instructions:</b> Report to the school on Saturday, not later than 11:45 for training from 1200 - 1600. Report to the range on Sunday not later than 0745 for training beginning at 0800. If you are late more than 5 minutes for either of the start times, you will not be permitted into class and must reschedule. This is an indoor range, Students not properly attired will not be permitted into the range. Long pants with a belt, high collar shirt, closed toe shoes, no shorts. Firearm rental is $25. Ammunition is available for purchase at current market price. Students may bring their own ammo, but only if they have their own firearm. If a student rents a firearm they must purchase the ammo as well. If you bring your own firearm, ensure it is unloaded, magazines unloaded and no live ammunition in the classroom."
            ],
            [
                'id' => 10,
                'is_active' => false,
                'name' => 'Continental Shooting Center',
                'city' => 'Stuart',
                'address' => "3091 SE Jay St\nStuart, FL 34997",
                'inst_name' => 'Vincent R. Onorato',
                'inst_email' => 'breakleather@hotmail.com',
                'inst_phone' => '(800) 772-2935',
                'price' => 150.00,
                'times' => 'By Appointment Only',
                'appt_only' => true,
                'range_html' => "Price is $150, Range fee is $25. Firearm and Gear Rental $30. Ammunition not included, if you rent our firearms, you must purchase our ammunition. Students can bring their own ammo, no steel case rounds. This is an indoor range, proper range attire is required, no open toe shoes, sandals, flipflops or slides. No low cut shirts."
            ],
            [
                'id' => 11,
                'is_active' => false,
                'name' => 'Southwest Florida Range & Gun Club',
                'city' => 'Immokalee',
                'address' => "31101 Nafi Dr\nImmokalee, FL 34142",
                'inst_name' => 'Tony Lauer',
                'inst_email' => 'info@ppft.us',
                'inst_phone' => '(239) 300-1920',
                'price' => 175.00,
                'times' => 'By Appointment Only',
                'appt_only' => true,
                'range_html' => "Students should have their own gun/gear/ammo.\n\nThis is an outdoor range; dress appropriately and bring water/drinks, snacks, hat, sunscreen, etc. If you are not sure what appropriate dress is, contact the instructor.\n\nOther ranges, including indoor ranges, <i>may</i> be available; check with the instructor.\n\nContact the instructor for additional information."
            ],
            [
                'id' => 12,
                'is_active' => false,
                'name' => 'Tango Down Shooting Complex',
                'city' => 'Jacksonville',
                'address' => "18811 Maxville-Macclenny Road\nJacksonville, FL 32234",
                'inst_name' => 'John McCartney',
                'inst_email' => 'owner.mtt@gmail.com',
                'inst_phone' => '(954) 650-1221',
                'price' => 150.00,
                'times' => 'By Appointment Only',
                'appt_only' => true,
                'range_html' => "<b>Notice:</b> All students must complete a pre-training questionnaire at the URL below, then the instructor will contact you for scheduling and details.\n\n<a href=\"https://www.annualg.com/g-survey\" target=\"_blank\">https://www.annualg.com/g-survey</a>"
            ],
            [
                'id' => 13,
                'is_active' => false,
                'name' => 'PBSC',
                'city' => 'Lake Worth',
                'address' => "501 Industrial St\nLake Worth, FL 33461",
                'inst_name' => 'Michael Jordan',
                'inst_email' => 'ccwgunclasses@gmail.com',
                'inst_phone' => '(561) 346-2313',
                'price' => 200.00,
                'times' => 'By Appointment Only',
                'appt_only' => true,
                'range_html' => "<b>Range Fee:</b> $25 paid by the student to the range upon arrival.\n\n<b>Gear rental available:</b> Firearm, 9mm Semi Auto (Glock 19 Gen 5 or Springfield XD9), duty belt with level 2 holster and 1 dual magazine holster with 4 magazines.\n\n<b>Special Instructions:</b>\n\nAmmunition - you may bring your own firearm and ammunition; however, it must be new, factory range ammo. All ammo is subject to approval by range staff and the instructor.\n\nIf you rent our firearm, you must purchase our ammunition. Call instructor for current pricing.\n\nAppropriate range attire required. Long pants with a belt and a tightly collared shirt are REQUIREMENTS. You may bring your own eye and ear protection, rent or purchase it at the range. A billed hat such as a ball cap is highly recommended.\n\nScheduling/Cancellations/Rescheduling; You MUST schedule your range training with your instructor listed above. Your training appointment will only be confirmed with full payment of the course fee ($175) in advance. Contact instructor if you need to reschedule. No show and no call = no refund and a $75 reschedule fee. We will respect your time and effort to be professional, please respect ours as well.\n\nIf you have never shot a handgun before or it has been a \"long time\" since you have shot and you are nervous about qualifying for your license, then we recommend that you take advantage of a one-hour coaching assessment with us. We will assess your current skill level and shooting ability and have you shoot the course of fire required by the DOL for qualification. If necessary, you may then take advantage of further coaching sessions prior to your range class and qualification attempt. The qualification for your Class G license is not easy if you are not practiced and skilled and competent at drawing, reloading, clearing malfunctions and shooting against a timer at distance. Coaching with us will give new shooters or rusty shooters a greatly enhanced opportunity to become competent and to pass your qualification to receive your license on your first attempt, thereby saving you time, effort and perhaps a significant amount of money. Call us to schedule a one-hour initial coaching assessment."
            ],
            [
                'id' => 14,
                'is_active' => false,
                'name' => 'Lions Club Range',
                'city' => 'Groveland',
                'address' => "6800 FL-50\nGroveland, FL 34736",
                'inst_name' => 'Alan Riddle',
                'inst_email' => 'training@eastsecinc.com',
                'inst_phone' => '(321) 417-9880',
                'price' => 125.00,
                'times' => '0800 - 1600',
                'appt_only' => false,
                'range_html' => "Students must report to the range from 0800 - 1600 (Approximate end time)\n\nFirearms and ammunition are available for rent/purchase, contact instructor for pricing. This is an outdoor range, proper range attire is required, plan for inclement weather.\n\n<b>REMINDER:</b> You MUST schedule your range training via the QR-code or URL\nlisted Below. You can select your range dates, and make payment through our website.\nPlease contact the instructor listed above if you need to reschedule.\n\n<b>No Refund will be given for No Call - No Shows</b>"
            ],
            [
                'id' => -1,
                'is_active' => true,
                'name' => 'No Range Date Selected',
                'city' => '',
                'address' => '',
                'inst_name' => '',
                'inst_email' => null,
                'inst_phone' => null,
                'price' => 0.00,
                'times' => '',
                'appt_only' => true,
                'range_html' => null
            ],
        ];

        foreach ($ranges2 as $range) {
            DB::table('ranges')->insert($range);
        }

        // Set the sequence to continue from 14
        DB::statement("SELECT setval('ranges_id_seq', 14, true);");
    }
}
