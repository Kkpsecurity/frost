<?php

namespace KKP\Laravel\HashIDs;

use Exception;
use KKP\Laravel\HashIDs\HashID;


trait BasicHashID
{

    /**
     * Convert model id (or other field) to hash_id
     *
     * @param   string|null  $field
     * @return  string|null  (hash_id)
     */
    public function hash_id(?string $field = null): ?string
    {

        $field = $field ?: $this->getRouteKeyName();

        if ($value = $this->getAttribute($field)) {
            $model_field = get_class($this) . "->{$field}";
            return HashID::Validate_Encode($value, $model_field);
        }

        return null;
    }
}
