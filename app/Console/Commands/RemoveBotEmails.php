<?php
// app/Console/Commands/RemoveBotEmails.php

namespace App\Console\Commands;

/**
 * @file RemoveBotEmails.php
 * @brief Command to remove bot emails older than 10 days and save them to a blacklist file.
 * @details This command identifies bot emails based on a specific pattern, checks their age,
 *          and removes them from the database while saving them to a blacklist file.
 */

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;


use Carbon\Carbon;
use App\Models\User;

class RemoveBotEmails extends Command
{
    protected $signature = 'remove:bot-emails';
    protected $description = 'Remove bot emails older then 30 days and has no courseAuth from the database and save them to a blacklist file';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Define pattern for bot emails
        $pattern = '^[a-z]{1}[0-9]{7,}@gmail\.com$'; // Matches emails like b0348747@gmail.com

        $dateThreshold = Carbon::now()->subDays(10);

        // Build the query
        $botUsers = User::where('email', '~', $pattern)
            ->where('created_at', '<', $dateThreshold)
            ->whereDoesntHave('courseAuths')
            ->get();

        // Display the emails for confirmation
        $this->info('Found bot emails:');
        foreach ($botUsers as $user) {
            $this->info($user->email);
        }

        if ($this->confirm('Do you wish to delete these bot emails and save them to a blacklist file?')) {
            // Save to blacklist file
            $blacklist = $botUsers->pluck('email')->toArray();
            Storage::append('blacklist.txt', implode(PHP_EOL, $blacklist));

            // Delete bot emails from the database
            // $deleted = User::whereIn('id', $botUsers->pluck('id'))->delete();
            // $this->info("Deleted $deleted bot emails and saved them to the blacklist file.");
        } else {
            $this->info('No emails were deleted.');
        }
    }
}
