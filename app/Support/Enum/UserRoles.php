<?php

namespace App\Support\Enum;

use App\Models\Role;

class UserRoles
{
    /**
     * Returns an array of user roles options
     * 
     * @return array
     */
    public static function options()
    {
        $types = Role::all();

        $lists = [];
        if (count($types) > 0) {
            foreach ($types as $type) {
                $lists[$type->id] = $type->name;
            }
        }

        return $lists;
    }

    /**
     * Returns the name of the user role with the given ID
     * 
     * @param int $id The ID of the user role
     * @return string|null The name of the user role, or null if not found
     */
    public static function name($id)
    {
        $role = Role::find($id);
        if ($role) {
            return $role->name;
        } else {
            return null;
        }
    }
}
