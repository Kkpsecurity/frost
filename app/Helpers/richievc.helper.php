<?php

use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Greets the user with the current date and appropriate holiday/time-based greeting
 * with time-based emoji icons
 *
 * @return string
 */
if (!function_exists('dateGreeter')) {
    function dateGreeter(): string
    {
        $now = Carbon::now();
        $currentDate = $now->format('Y-m-d');
        $year = $now->year;

        // Cache holidays for performance
        static $holidays = null;

        if ($holidays === null) {
            $holidays = getHolidaysForYear($year);
        }

        // Check for holiday
        if (isset($holidays[$currentDate])) {
            return $holidays[$currentDate];
        }

        // Time-based greetings with emojis
        $hour = $now->hour;
        $dayName = $now->format('D');
        $dateFormatted = $now->format('M d, Y');

        $timeGreeting = match (true) {
            $hour >= 5 && $hour < 12 => '🌅 Good Morning',
            $hour >= 12 && $hour < 17 => '☀️ Good Afternoon',
            $hour >= 17 && $hour < 21 => '🌇 Good Evening',
            default => '🌙 Good Night'
        };

        return "{$timeGreeting}: {$dayName} - {$dateFormatted}";
    }
}

/**
 * Get all holidays for a given year with enhanced greetings
 *
 * @param int $year
 * @return array
 */
if (!function_exists('getHolidaysForYear')) {
    function getHolidaysForYear(int $year): array
    {
        $holidays = [
            // Fixed holidays with emojis
            "{$year}-01-01" => '🎉 Happy New Year\'s Day!',
            "{$year}-02-02" => '🦔 Happy Groundhog Day!',
            "{$year}-02-12" => '🎩 Lincoln\'s Birthday',
            "{$year}-02-14" => '💘 Happy Valentine\'s Day!',
            "{$year}-03-17" => '🍀 Happy St. Patrick\'s Day!',
            "{$year}-04-01" => '🤪 Happy April Fool\'s Day!',
            "{$year}-04-22" => '🌍 Happy Earth Day!',
            "{$year}-06-14" => '🇺🇸 Happy Flag Day!',
            "{$year}-07-04" => '🎆 Happy Independence Day!',
            "{$year}-09-11" => '🇺🇸 Patriot Day - Remember 9/11',
            "{$year}-10-16" => "👔 Happy Boss's Day!",
            "{$year}-10-31" => '🎃 Happy Halloween!',
            "{$year}-11-01" => '😇 Happy All Saints\' Day',
            "{$year}-11-11" => '🎖️ Happy Veterans Day!',
            "{$year}-12-24" => '🎄 Merry Christmas Eve!',
            "{$year}-12-25" => '🎅 Merry Christmas!',
            "{$year}-12-26" => '🕯️ Happy Kwanzaa!',
            "{$year}-12-31" => '🥂 Happy New Year\'s Eve!',
        ];

        // Variable holidays using Carbon
        $holidays[Carbon::parse("{$year}-01-01")->nthOfMonth(3, Carbon::MONDAY)->format('Y-m-d')] = '✊🏾 Happy Martin Luther King Jr. Day!';
        $holidays[Carbon::parse("{$year}-02-01")->nthOfMonth(3, Carbon::MONDAY)->format('Y-m-d')] = '🇺🇸 Happy Presidents\' Day!';
        $holidays[Carbon::parse("{$year}-05-01")->nthOfMonth(2, Carbon::SUNDAY)->format('Y-m-d')] = '👩 Happy Mother\'s Day!';
        $holidays[Carbon::parse("{$year}-05-31")->previous(Carbon::MONDAY)->format('Y-m-d')] = '⚔️ Happy Memorial Day!';
        $holidays[Carbon::parse("{$year}-06-01")->nthOfMonth(3, Carbon::SUNDAY)->format('Y-m-d')] = '👨 Happy Father\'s Day!';
        $holidays[Carbon::parse("{$year}-09-01")->nthOfMonth(1, Carbon::MONDAY)->format('Y-m-d')] = '👷 Happy Labor Day!';
        $holidays[Carbon::parse("{$year}-10-01")->nthOfMonth(2, Carbon::MONDAY)->format('Y-m-d')] = '⛵ Happy Columbus Day!';
        $holidays[Carbon::parse("{$year}-11-01")->nthOfMonth(4, Carbon::THURSDAY)->format('Y-m-d')] = '🦃 Happy Thanksgiving!';

        // Easter calculation
        $easterDays = easter_days($year);
        $easterDate = Carbon::parse("{$year}-03-21")->addDays($easterDays)->format('Y-m-d');
        $holidays[$easterDate] = '🐣 Happy Easter!';

        return $holidays;
    }
}

/**
 * Convert string to underscore format
 *
 * @param string $str Input string
 * @return string Underscored string
 */
if (!function_exists('underscore')) {
    function underscore(string $str): string
    {
        return Str::snake(trim($str));
    }
}

/**
 * Convert underscore/separator string to human readable format
 *
 * @param string $str Input string
 * @param string $separator Input separator
 * @return string Human readable string
 */
if (!function_exists('humanize')) {
    function humanize(string $str, string $separator = '_'): string
    {
        return Str::title(str_replace($separator, ' ', trim($str)));
    }
}

/**
 * Returns the English ordinal numeral for a given number
 *
 * @param  int    $number
 * @return string
 */
if (!function_exists('ordinal_format')) {

    function ordinal_format($number)
    {
        if (!ctype_digit((string) $number) or $number < 1) {
            return $number;
        }

        $last_digit = array(
            0 => 'th',
            1 => 'st',
            2 => 'nd',
            3 => 'rd',
            4 => 'th',
            5 => 'th',
            6 => 'th',
            7 => 'th',
            8 => 'th',
            9 => 'th'
        );

        if (($number % 100) >= 11 && ($number % 100) <= 13) {
            return $number . 'th';
        }

        return $number . $last_digit[$number % 10];
    }
}

/*****************************************************
 * US STATES					  |
 *****************************************************
 * @desc: array of US State and Providences
 * @return array
 */
function us_states(): array
{
    $state_list = array(
        '-1' => 'Select a State',
        'AL' => "Alabama",
        'AK' => "Alaska",
        'AZ' => "Arizona",
        'AR' => "Arkansas",
        'CA' => "California",
        'CO' => "Colorado",
        'CT' => "Connecticut",
        'DE' => "Delaware",
        'DC' => "District Of Columbia",
        'FL' => "Florida",
        'GA' => "Georgia",
        'HI' => "Hawaii",
        'ID' => "Idaho",
        'IL' => "Illinois",
        'IN' => "Indiana",
        'IA' => "Iowa",
        'KS' => "Kansas",
        'KY' => "Kentucky",
        'LA' => "Louisiana",
        'ME' => "Maine",
        'MD' => "Maryland",
        'MA' => "Massachusetts",
        'MI' => "Michigan",
        'MN' => "Minnesota",
        'MS' => "Mississippi",
        'MO' => "Missouri",
        'MT' => "Montana",
        'NE' => "Nebraska",
        'NV' => "Nevada",
        'NH' => "New Hampshire",
        'NJ' => "New Jersey",
        'NM' => "New Mexico",
        'NY' => "New York",
        'NC' => "North Carolina",
        'ND' => "North Dakota",
        'OH' => "Ohio",
        'OK' => "Oklahoma",
        'OR' => "Oregon",
        'PA' => "Pennsylvania",
        'RI' => "Rhode Island",
        'SC' => "South Carolina",
        'SD' => "South Dakota",
        'TN' => "Tennessee",
        'TX' => "Texas",
        'UT' => "Utah",
        'VT' => "Vermont",
        'VA' => "Virginia",
        'WA' => "Washington",
        'WV' => "West Virginia",
        'WI' => "Wisconsin",
        'WY' => "Wyoming"
    );
    return $state_list;
}

/**
 * Generate random date within specified range
 *
 * @param string $range Date range (e.g., '1 Year Ago')
 * @return string Formatted date
 */
if (!function_exists('setRandomDate')) {
    function setRandomDate(string $range = '1 Year Ago'): string
    {
        try {
            $startTime = Carbon::now()->timestamp;
            $endTime = Carbon::parse($range)->timestamp;

            $randomTime = random_int(min($startTime, $endTime), max($startTime, $endTime));

            return Carbon::createFromTimestamp($randomTime)->format('Y-m-d H:i:s');
        } catch (Exception $e) {
            return Carbon::now()->format('Y-m-d H:i:s');
        }
    }
}

/**
 * Generate random date between two dates
 *
 * @param string $startDate Start date
 * @param string $endDate End date
 * @return string Formatted date
 */
if (!function_exists('randomDate')) {
    function randomDate(string $startDate, string $endDate): string
    {
        try {
            $startTime = Carbon::parse($startDate)->timestamp;
            $endTime = Carbon::parse($endDate)->timestamp;

            $randomTime = random_int($startTime, $endTime);

            return Carbon::createFromTimestamp($randomTime)->format('Y-m-d H:i:s');
        } catch (Exception $e) {
            return Carbon::now()->format('Y-m-d H:i:s');
        }
    }
}
/**
 * Get priority icon for different alert types
 *
 * @param string $priority Priority level
 * @return string HTML icon
 */
if (!function_exists('setPriorityIcon')) {
    function setPriorityIcon(string $priority): string
    {
        return match($priority) {
            'danger' => '<i class="fas fa-exclamation-triangle text-danger"></i> ',
            'warning' => '<i class="fas fa-exclamation text-warning"></i> ',
            'info' => '<i class="fas fa-info-circle text-info"></i> ',
            'success' => '<i class="fas fa-check-circle text-success"></i> ',
            default => ''
        };
    }
}

/**
 * Generate formatted address block from settings
 *
 * @return string HTML formatted address
 */
if (!function_exists('addressBlock')) {
    function addressBlock(): string
    {
        // Use Laravel config or fallback to empty string if setting() function doesn't exist
        $getSetting = function($key, $default = '') {
            if (function_exists('setting')) {
                return setting($key, $default);
            }
            return config("company.{$key}", $default);
        };

        $addressParts = array_filter([
            $getSetting('address'),
            $getSetting('address2'),
            trim($getSetting('city') . ' ' . $getSetting('state') . ' ' . $getSetting('zip')),
            $getSetting('sales_phone'),
            $getSetting('sales_email')
        ]);

        return nl2br(implode(PHP_EOL, $addressParts));
    }
}
