<?php

namespace App\Helpers;

use DB;
use Exception;
use stdClass;
use Illuminate\Support\Collection;


class PgTk
{

    /**
     * retrieve unique timestamp from Postgres
     *
     * @return  string  timestamp
     */
    public static function now(): string
    {
        return DB::selectOne(DB::raw('SELECT current_timestamp'))->current_timestamp;
    }


    /**
     * retrieve UUID from Postgres
     *
     * @return  string  uuid
     */
    public static function UUID_v4(): string
    {
        return DB::selectOne(DB::raw('SELECT * FROM uuid_generate_v4()'))->uuid_generate_v4;
    }


    /**
     * convert array to Postgres string
     *
     * @param   array   $arr
     * @return  string
     */
    public static function toPgString(array $arr): string
    {

        return "('{"
            . join(',', array_map(function ($a) {
                return (is_numeric($a) ? $a : "\"{$a}\"");
            }, $arr))
            . "}')";
    }


    /**
     * convert Laravel's array of stdClass to simple array
     *
     * @param   array  $arr [ stored procedure result ]
     * @return  array  simple array of items
     */
    public static function toSimple(array $arr): array
    {

        return array_map(function ($stdClass) {

            return array_values((array) $stdClass)[0];
        }, $arr);
    }


    /**
     * convert Laravel's array of stdClass to single value
     *   useful for stored procedure returning COUNT()
     *
     * @param   array  $arr [ stored procedure result ]
     * @return  mixed
     */
    public static function toValue(array $arr)
    {
        return array_values((array) ($arr)[0])[0];
    }


    /**
     * convert Laravel's array of stdClass to KVP array or stdClass
     *
     * @param   array         $arr [ stored procedure result ]
     * @param   boolean       $as_object
     * @return  array|object  KVP array|stdClass
     */
    public static function toKVP(array $arr, $as_object = false): array|object
    {

        $kvp = [];

        array_walk($arr, function ($stdClass) use (&$kvp) {

            list($key, $val) = array_values((array) $stdClass);
            $kvp[$key] = $val;
        });

        return ($as_object ? (object) $kvp : $kvp);
    }


    /**
     * convert Laravel's array of stdClass to keyed array
     *
     * @param   array  $arr [ stored procedure result ]
     * @return  array  keyed array of records
     */
    public static function toKeyedArr(array $arr): array
    {

        $keyed = [];

        array_walk($arr, function ($stdClass) use (&$keyed) {

            $conv = (array) $stdClass;
            $keyed[$conv[array_key_first($conv)]] = $conv;

            # fails in php8
            #$keyed[ $stdClass->{ key( $stdClass ) } ] = (array) $stdClass;

        });

        return $keyed;
    }


    /**
     * convert Laravel's array of stdClass to keyed stdClass
     *
     * @param   array   $arr [ stored procedure result ]
     * @return  object  keyed stdClass of records
     */
    public static function toKeyedObj(array $arr): stdClass
    {

        $keyed = new stdClass;

        array_walk($arr, function ($stdClass) use (&$keyed) {

            $keyed->{$stdClass->{array_key_first((array) $stdClass)}} = $stdClass;

            # fails in php8
            #$keyed->{ $stdClass->{ key( $stdClass ) } } = $stdClass;

        });

        return $keyed;
    }


    /**
     * convert Laravel's array of stdClass to Collection of models
     *
     * @param   string  $model_class
     * @param   array   $arr [ stored procedure result ]
     * @return  object  keyed Collection of models
     */
    public static function toModels(string $model_class, array $arr): Collection
    {

        //
        // validate $model_class
        //

        if (! class_exists($model_class)) {
            throw new Exception("Class '{$model_class}' not defined");
        }

        if (! is_subclass_of($model_class, 'Illuminate\Database\Eloquent\Model')) {
            throw new Exception("Class '{$model_class}' is not a Model");
        }


        //
        // create Collection of model objects
        //

        $models = new Collection;

        array_walk($arr, function ($stdClass) use ($model_class, &$models) {

            $model = $model_class::hydrate([(array) $stdClass])[0];
            $models->put($model->getKey(), $model);
        });

        return $models;
    }


    /**
     * convert Laravel's array of stdClass to Collection of models
     *
     * @param   string  $table_name
     * @param   string  $column_name
     * @return  array
     */
    public static function ColumnCounts(string $table_name, string $column_name): array
    {

        $records = DB::table($table_name)
            ->whereNotNull($column_name)
            ->select($column_name, DB::raw('COUNT(*) as count'))
            ->groupBy($column_name)
            ->orderBy($column_name)
            ->get();

        $counts = [];

        foreach ($records as $record) {
            $counts[$record->{$column_name}] = $record->count;
        }

        return $counts;
    }
}
