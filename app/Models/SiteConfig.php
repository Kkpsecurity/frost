<?php

namespace App\Models;

/**
 * @file SiteConfig.php
 * @brief Model for site_configs table.
 * @details This model represents site configuration settings, including attributes like config name,
 * config value, and cast type. It provides methods for sanitizing input and serializing values.
 */

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

use RCache;
use App\Helpers\TextTk;
use App\RCache\RCacheModelTrait;


class SiteConfig extends Model
{

    use RCacheModelTrait;


    protected $table        = 'site_configs';
    protected $primaryKey   = 'id';
    public    $timestamps   = false;

    protected $casts        = [

        'id'                => 'integer',
        'cast_to'           => 'string',  // 16  see Casts()
        'config_name'       => 'string',  // 32
        'config_value'      => 'string',

    ];

    protected $guarded      = ['id'];


    public function __toString()
    {
        return $this->config_value;
    }


    //
    // incoming data filters
    //


    public static function SlugConfigName($str = null): ?string
    {
        return $str ? Str::of(TextTk::Sanitize($str))->slug('_') : null;
    }

    public function setConfigNameAttribute($value)
    {
        $this->attributes['config_name'] = self::SlugConfigName($value);
    }


    /**
     *
     * allowed casts
     *
     */


    public static function Casts(): array
    {
        return [
            'bool'      => 'Boolean',
            'int'       => 'Integer',
            'float'     => 'Decimal Number',
            'text'      => 'Text',
            'longtext'  => 'Long Text',
            'htmltext'  => 'HTML Text',
        ];
    }


    /**
     *
     * getter / setter
     *
     */


    public function getConfigValueAttribute($value)
    {

        return self::isSerialized($value)
            ? unserialize(substr($value, strlen(self::SERIALIZED_PREFIX)))
            : $value;
    }


    public function setConfigValueAttribute($value): bool
    {

        if (self::isSerialized($value)) {
            return true;
        }

        //
        // cast $value before serializing
        //

        switch ($this->cast_to) {

            case 'bool':
                $value =  (bool) $value;
                break;
            case 'int':
                $value =   (int) $value;
                break;
            case 'float':
                $value = (float) $value;
                break;

            case 'text':
            case 'longtext':
                $value = TextTk::Sanitize($value);
                break;

            case 'htmltext':
                $value = TextTk::Sanitize($value, TextTk::SANITIZE_NO_STRIPTAGS);
                break;

            default:
                throw new Exception(__CLASS__ . " Unknown cast_to '{$this->cast_to}'");
                return false;
        }

        //
        // serialize value
        //

        $this->attributes['config_value'] = self::SERIALIZED_PREFIX . serialize($value);
        return true;
    }


    /**
     *
     * serializer
     *
     */


    protected const SERIALIZED_PREFIX = '%%SER%%';

    protected static function isSerialized(&$value): bool
    {

        if (! is_string($value)) {
            return false;
        }

        return self::SERIALIZED_PREFIX == substr($value, 0, strlen(self::SERIALIZED_PREFIX));
    }
}
