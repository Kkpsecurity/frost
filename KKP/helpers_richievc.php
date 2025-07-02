<?php


/**
 * @desc: Greets the user with the current date and the current greeting
 *        according to Holidays or time of ady
 *
 * @param $currentDate
 * @return mixed|string
 */
if (!function_exists('dateGreeter')) {

    function dateGreeter(): mixed
    {
        $currentDate = date('Y-m-d');
        $year = date('Y');

        $fixed_holidays = [
            $year . '-01-01' => 'Happy New Year\'s Day',
            $year . '-02-02' => 'Happy Groundhog\'s Day',
            $year . '-02-12' => 'Lincoln\'s Birthday',
            $year . '-02-14' => 'Happy Valentine\'s Day',
            $year . '-03-17' => 'Happy St. Patrick\'s Day',
            $year . '-04-01' => 'Happy April Fool\'s Day',
            $year . '-04-22' => 'Happy Earth Day',
            $year . '-06-14' => 'Happy Flag Day',
            $year . '-07-04' => 'Happy Independence Day',
            $year . '-09-11' => 'Happy Patriot Day / Remember 9-11',
            $year . '-10-16' => 'Happy Bosses\' Day',
            $year . '-10-31' => 'Happy Halloween',
            $year . '-11-01' => 'Happy All Saint\'s Day',
            $year . '-11-11' => 'Happy Veterans Day',
            $year . '-12-24' => 'Merry Christmas Eve',
            $year . '-12-25' => 'Merry Christmas Day',
            $year . '-12-26' => 'Happy Kwanzaa',
            $year . '-12-31' => 'Happy New Year\'s Eve',
        ];

        $holidays = $fixed_holidays;

        // Martin Luther King Jr. Day (Third Monday in January)
        $holidayDay = date("Y-m-d", strtotime($year . "-01 third monday"));
        $holidays[$holidayDay] = 'Happy Martin Luther King Jr. Day';

        // Presidents' Day (Third Monday in February)
        $holidayDay = date("Y-m-d", strtotime($year . "-02 third monday"));
        $holidays[$holidayDay] = 'Happy Presidents\' Day';

        // Mother's Day (Second Sunday in May)
        $holidayDay = date("Y-m-d", strtotime($year . "-05 second sunday"));
        $holidays[$holidayDay] = 'Happy Mother\'s Day';

        // Memorial Day (Last Monday in May)
        $holidayDay = date("Y-m-d", strtotime($year . "-06-01 last monday"));
        $holidays[$holidayDay] = 'Happy Memorial Day';

        // Father's Day (Third Sunday in June)
        $holidayDay = date("Y-m-d", strtotime($year . "-06 third sunday"));
        $holidays[$holidayDay] = 'Happy Father\'s Day';

        // Labor Day (First Monday in September)
        $holidayDay = date("Y-m-d", strtotime($year . "-09 first monday"));
        $holidays[$holidayDay] = 'Happy Labor Day';

        // Columbus Day (Second Monday in October)
        $holidayDay = date("Y-m-d", strtotime($year . "-10 second monday"));
        $holidays[$holidayDay] = 'Happy Columbus Day';

        // Thanksgiving Day (Fourth Thursday in November)
        $holidayDay = date("Y-m-d", strtotime($year . "-11 fourth thursday"));
        $holidays[$holidayDay] = 'Happy Thanksgiving Day';

        // Easter holidays
        $easterDays = easter_days($year); // # days after March 21st
        $easterDate = date("Y-m-d", strtotime("+$easterDays days", mktime(1, 1, 1, 3, 21, $year)));
        $holidays[$easterDate] = 'Happy Easter';

        if (!isset($holidays[$currentDate])) {
            return ' Greetings: ' . date('D - M d, Y');
        } else {
            return ' Greetings: ' . $holidays[$currentDate];
        }
    }
}

/**
 * Underscore
 *
 * Takes multiple words separated by spaces and underscores them
 *
 * @param string $str	Input string
 * @return    string
 **/
if (!function_exists('underscore')) {

    function underscore(string $str): string
    {
        $str = trim($str);
        $str = mb_strtolower($str);
        $str = preg_replace('/[\s]+/', '_', $str);

        return $str;
    }
}

/**
 * Humanize
 *
 * Takes multiple words separated by the separator and changes them to spaces
 *
 * @param string $str		Input string
 * @param string $separator	Input separator
 * @return    string
 */
if (!function_exists('humanize')) {

    function humanize($str, $separator = '_')
    {
        $str = trim($str);
        $str = mb_strtolower($str);
        $str = preg_replace('/[' . preg_quote($separator) . ']+/', ' ', $str);
        $str = ucwords($str);

        return $str;
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
 * Summary of set_random_date
 * @return string
 */
function set_random_date($range = '1 Year Ago'): string
{
    $int = mt_rand(strtotime("now"), strtotime($range));
    $string = date("Y-m-d H:i:s", $int);
    return $string;
}
function randomDate($start_date, $end_date): string
{
    // Convert to timetamps
    $min = strtotime($start_date);
    $max = strtotime($end_date);

    // Generate random number using above bounds
    $val = rand($min, $max);

    // Convert back to desired date format
    return date('Y-m-d H:i:s', $val);
}
/**
 * @param $priority
 * @return string
 */
function set_priority_icon($priority): string
{
    if ($priority == "" || $priority == 'normal') {
        return '';
    } else if ($priority == 'danger') {
        return '<i class="fa fa-exclamation-triangle"></i> ';
    } else if ($priority == 'warning') {
        return '<i class="fa fa-exclamation"></i> ';
    } else if ($priority == 'info') {
        return '<i class="fa fa-question"></i> ';
    } else if ($priority == 'success') {
        return '<i class="fa fa-check"></i> ';
    }
    return '';
}

/**
 * @return string
 */
function address_block(): string
{
    $signature = setting('address') . PHP_EOL;
    $signature .= setting('address2') . PHP_EOL;
    $signature .= setting('city') . ' ' . setting('state') . ' ' . setting('zip') . PHP_EOL;
    $signature .= setting('sales_phone') . PHP_EOL;
    $signature .= setting('sales_email') . PHP_EOL;

    return nl2br($signature);
}