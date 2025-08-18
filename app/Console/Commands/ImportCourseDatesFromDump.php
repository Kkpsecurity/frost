<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ImportCourseDatesFromDump extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'frost:import-course-dates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import course_dates from docs/dump(1).sql';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $file = base_path('docs/dump(1).sql');
        if (!file_exists($file)) {
            $this->error('SQL dump file not found: ' . $file);
            return 1;
        }

        $this->info('Reading dump file...');
        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $inCopy = false;
        $rows = [];
        foreach ($lines as $line) {
            if (strpos($line, 'COPY public.course_dates') === 0) {
                $inCopy = true;
                continue;
            }
            if ($inCopy && trim($line) === '\\.') {
                break;
            }
            if ($inCopy) {
                // Parse row: id\tis_active\tcourse_unit_id\tstarts_at\tends_at
                $cols = explode("\t", $line);
                if (count($cols) !== 5) continue;
                $rows[] = [
                    'id' => (int)$cols[0],
                    'is_active' => $cols[1] === 't',
                    'course_unit_id' => (int)$cols[2],
                    'starts_at' => preg_replace('/([+-][0-9]{2}):[0-9]{2}$/', '', $cols[3]),
                    'ends_at' => preg_replace('/([+-][0-9]{2}):[0-9]{2}$/', '', $cols[4]),
                ];
            }
        }

        if (empty($rows)) {
            $this->error('No course_dates found in dump.');
            return 1;
        }

        $this->info('Importing ' . count($rows) . ' course_dates...');
        $chunks = array_chunk($rows, 500);
        foreach ($chunks as $chunk) {
            \DB::table('course_dates')->upsert($chunk, ['id'], ['is_active', 'course_unit_id', 'starts_at', 'ends_at']);
        }
        $this->info('Import complete!');
        return 0;
    }
}
