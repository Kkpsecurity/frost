<?php

namespace App\Models\Traits\User;

/**
 * @file RolesTrait.php
 * @brief Trait for managing user roles.
 * @details This trait provides methods to check user roles and return the appropriate dashboard URL.
 */


trait RolesTrait
{

    /**
     *
     * RoleIDs
     * 1    SysAdmin
     * 2    Administrator
     * 3    Support
     * 4    Instructor
     * 5    Student
     *
     */


    public function IsSysAdmin(): bool
    {
        return $this->role_id == 1;
    }

    public function IsAdministrator(): bool
    {
        return $this->role_id <= 2;
    }

    public function IsSupport(): bool
    {
        return $this->role_id <= 3;
    }

    public function IsInstructor(): bool
    {
        return $this->role_id <= 4;
    }

    public function IsStudent(): bool
    {
        return $this->role_id == 5;
    }

    public function IsAnyAdmin(): bool
    {
        return $this->role_id <= 4;
    }


    public function Dashboard(): string
    {

        switch ($this->role_id) {
            case '1':
                return route('admin.dashboard');
                break;
            case '2':
                return route('admin.dashboard');
                break;
            case '3':
                return route('admin.dashboard');
                break;
            case '4':
                return route('admin.instructors.dashboard');
                break;
            default:
                return route('classroom.dashboard');
                break;
        }
    }
}
