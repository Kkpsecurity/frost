<?php

use Illuminate\Support\Collection;

/**
 * Recursively convert array to stdClass object
 *
 * @param mixed $param Input array or value
 * @return object|mixed
 */
if (!function_exists('arrayToObject')) {
    function arrayToObject(mixed $param): mixed
    {
        if (is_array($param)) {
            return (object) array_map(__FUNCTION__, $param);
        }

        return $param;
    }
}

/**
 * Determine if array is simple (indexed/sequential)
 *
 * @param array $arr Input array
 * @return bool
 */
if (!function_exists('isSimpleArray')) {
    function isSimpleArray(array $arr): bool
    {
        return array_is_list($arr); // PHP 8.1+ native function
    }
}

/**
 * Convert indexed array to key-value pairs (hash)
 *
 * @param array $arr Input indexed array
 * @return array
 * @throws InvalidArgumentException
 */
if (!function_exists('indexedToHash')) {
    function indexedToHash(array $arr): array
    {
        if (!isSimpleArray($arr)) {
            throw new InvalidArgumentException('Function requires indexed array');
        }

        return array_combine($arr, $arr);
    }
}


/**
 * Set no-cache headers (Laravel-friendly way)
 *
 * @return void
 */
if (!function_exists('noCacheHeaders')) {
    function noCacheHeaders(): void
    {
        if (function_exists('response')) {
            // Laravel way - use response helper if available
            response()->headers->add([
                'Cache-Control' => 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0',
                'Pragma' => 'no-cache',
                'Expires' => 'Mon, 01 Jan 1970 00:00:00 GMT',
                'Last-Modified' => gmdate('D, d M Y H:i:s \G\M\T'),
            ]);
        } else {
            // Fallback to native PHP headers
            header('Expires: Mon, 01 Jan 1970 00:00:00 GMT');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s \G\M\T'));
            header('Cache-Control: no-store, no-cache, must-revalidate');
            header('Cache-Control: post-check=0, pre-check=0', false);
            header('Pragma: no-cache');
        }
    }
}


/**
 * Get short method name from backtrace without namespace
 *
 * @return string
 */
if (!function_exists('getShortMethodName')) {
    function getShortMethodName(): string
    {
        $caller = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1] ?? null;

        if (!$caller || !isset($caller['class'], $caller['function'])) {
            return 'Unknown::method';
        }

        $className = substr($caller['class'], strrpos($caller['class'], '\\') + 1);

        return "{$className}::{$caller['function']}";
    }
}



/**
 * Print array with <pre> tags (Laravel-friendly debug helper)
 *
 * @param mixed $data Data to print
 * @param bool $return Whether to return or echo
 * @return string|null
 */
if (!function_exists('debugArray')) {
    function debugArray(mixed $data, bool $return = false): ?string
    {
        // Use Laravel's dump/dd if available for better formatting
        if (function_exists('dump') && !$return) {
            dump($data);
            return null;
        }

        $output = is_array($data) || is_object($data)
            ? print_r($data, true)
            : (string) $data;

        $html = "<pre style='background: #f8f9fa; padding: 1rem; border: 1px solid #dee2e6; border-radius: 0.25rem; overflow-x: auto;'>\n"
              . htmlspecialchars($output)
              . "</pre>\n";

        if ($return) {
            return $html;
        }

        echo $html;
        return null;
    }
}


/**
 * Enhanced coalescing with logging (use ?? operator instead for simple cases)
 *
 * @param mixed $value Primary value to check
 * @param mixed $fallback Fallback value
 * @param bool $log Whether to log the reason for fallback
 * @return mixed
 */
if (!function_exists('coalesceWithLog')) {
    function coalesceWithLog(mixed $value, mixed $fallback, bool $log = false): mixed
    {
        $reason = match (true) {
            !isset($value) => 'not set',
            is_null($value) => 'is null',
            empty($value) && $value !== 0 && $value !== '0' && $value !== false => 'empty',
            $value === false => 'false',
            $value === 0 => 'zero',
            $value === '0' => 'string zero',
            default => null
        };

        if ($reason !== null) {
            if ($log) {
                logger("coalesce: {$reason}", ['value' => $value, 'fallback' => $fallback]);
            }
            return $fallback;
        }

        return $value;
    }
}


/**
 * Generate Lorem Ipsum text
 *
 * @param int|null $version Version of lorem ipsum (1-3)
 * @return string
 */
if (!function_exists('lorem')) {
    function lorem(?int $version = 1): string
    {
        $texts = [
            1 => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',

            2 => 'Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?',

            3 => 'At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat.'
        ];

        return $texts[$version] ?? $texts[1];
    }
}

/**
 * Convert object to array recursively
 *
 * @param mixed $data Object or array to convert
 * @return array
 */
if (!function_exists('objectToArray')) {
    function objectToArray(mixed $data): array
    {
        if (is_object($data)) {
            $data = get_object_vars($data);
        }

        if (is_array($data)) {
            return array_map(__FUNCTION__, $data);
        }

        return (array) $data;
    }
}

/**
 * Check if value is blank (null, empty string, or whitespace only)
 *
 * @param mixed $value Value to check
 * @return bool
 */
if (!function_exists('isBlank')) {
    function isBlank(mixed $value): bool
    {
        return is_null($value) ||
               (is_string($value) && trim($value) === '') ||
               (is_array($value) && empty($value));
    }
}

/**
 * Generate a random string with specified length
 *
 * @param int $length Length of the string
 * @param string $characters Character set to use
 * @return string
 */
if (!function_exists('randomString')) {
    function randomString(int $length = 10, string $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'): string
    {
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }

        return $randomString;
    }
}
