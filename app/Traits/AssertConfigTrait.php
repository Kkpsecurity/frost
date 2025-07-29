<?php

namespace App\Traits;

use stdClass;


trait AssertConfigTrait
{

    /**
     * Verify config keys exist; logs and aborts if missing
     * Returns config as stdClass
     *   $required_keys may include dotted notation
     *
     * @param   string     $config_key
     * @param   array      $required_keys
     * @return  \stdClass
     */
    public static function AssertConfig(string $config_key, array $required_keys): stdClass
    {

        if (! $config = config($config_key)) {
            self::_AssertConfigFail("Config '{$config_key}' missing");
        }


        $missing = [];

        foreach ($required_keys as $key) {

            if (strpos($key, '.') === false) {

                if (! array_key_exists($key, $config)) {
                    array_push($missing, $key);
                }
            } else {

                //
                // search dotted notation
                //

                $current_config = $config;

                foreach (explode('.', $key) as $current_key) {

                    if (! array_key_exists($current_key, $current_config)) {
                        array_push($missing, $key);
                        break;
                    }

                    $current_config = $current_config[$current_key];
                }
            }
        }


        if ($missing) {
            self::_AssertConfigFail("Config '{$config_key}' missing items: " . join(', ', $missing));
        }

        return arrayToObject(config($config_key));
    }


    private static function _AssertConfigFail(string $msg): void
    {
        logger($msg);
        api_abort(500, $msg);
    }
}
