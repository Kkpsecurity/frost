<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

use App\Models\CourseDate;


class LoadNewDates extends Command
{

    protected $signature   = 'command:load_new_dates';
    protected $description = 'Load New Dates';


    public function handle(): int
    {

        foreach (['d40_dates.txt', 'g28_dates.txt'] as $file) {

            $abspath = base_path("xfer/$file");

            if (! file_exists($abspath)) {
                $this->error("Not found: {$abspath}");
                return 1;
            }
        }


        $d40_count = 0;
        $g28_count = 0;


        //
        // D40
        //


        $this->newline();
        $this->info('Loading D40');

        foreach (file(base_path("xfer/d40_dates.txt"), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $course_date) {

            if (! $course_date) {
                continue;
            }


            $Date = Carbon::parse($course_date, 'America/New_York');

            $course_unit_id = intval($Date->isoFormat('e'));
            $starts_at      = (clone $Date)->hour('8')->tz('UTC');
            $ends_at        = (clone $Date)->hour('17')->tz('UTC');

            if ($CourseDate = CourseDate::where('starts_at', $starts_at)->where('course_unit_id', $course_unit_id)->first()) {

                $this->line("SKIPPING {$course_date} :: {$CourseDate->id}");
            } else {

                CourseDate::create([
                    'course_unit_id' => $course_unit_id,
                    'starts_at'      => $starts_at,
                    'ends_at'        => $ends_at,
                ]);

                $d40_count++;
            }
        }


        //
        // G28
        //


        $this->newline();
        $this->info('Loading G28');

        foreach (file(base_path("xfer/g28_dates.txt"), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $course_date) {

            if (! $course_date) {
                continue;
            }


            $Date = Carbon::parse($course_date, 'America/New_York');


            switch ($Date->isoFormat('e')) {

                case 1:
                    $course_unit_id = 16;
                    $starts_at      = (clone $Date)->hour('8')->tz('UTC');
                    $ends_at        = (clone $Date)->hour('17')->tz('UTC');
                    break;

                case 2:
                    $course_unit_id = 17;
                    $starts_at      = (clone $Date)->hour('9')->tz('UTC');
                    $ends_at        = (clone $Date)->hour('17')->tz('UTC');
                    break;

                case 3:
                    $course_unit_id = 18;
                    $starts_at      = (clone $Date)->hour('9')->tz('UTC');
                    $ends_at        = (clone $Date)->hour('16')->tz('UTC');
                    break;

                default:
                    $this->error("Bad G28 date {$course_date}");
                    continue 2;
            }


            if ($CourseDate = CourseDate::where('starts_at', $starts_at)->where('course_unit_id', $course_unit_id)->first()) {

                $this->line("SKIPPING {$course_date} :: {$CourseDate->id}");
            } else {

                CourseDate::create([
                    'course_unit_id' => $course_unit_id,
                    'starts_at'      => $starts_at,
                    'ends_at'        => $ends_at,
                ]);

                $g28_count++;
            }
        }


        //
        // report
        //


        $this->newline();
        $this->line("Loaded D40: {$d40_count}");
        $this->line("Loaded G28: {$g28_count}");

        return 0;
    }
}
