<?php

namespace App\Traits;

/**
 * Trait TogglesBooleans
 * 
 * Provides methods to toggle boolean attributes on models
 */
trait TogglesBooleans
{
    /**
     * Toggle a boolean attribute
     *
     * @param string $attribute
     * @return bool
     */
    public function toggle(string $attribute): bool
    {
        $this->{$attribute} = !$this->{$attribute};
        $this->save();
        
        return $this->{$attribute};
    }

    /**
     * Toggle the is_active attribute
     *
     * @return bool
     */
    public function toggleActive(): bool
    {
        return $this->toggle('is_active');
    }

    /**
     * Activate the model
     *
     * @return bool
     */
    public function activate(): bool
    {
        $this->is_active = true;
        $this->save();
        
        return true;
    }

    /**
     * Deactivate the model
     *
     * @return bool
     */
    public function deactivate(): bool
    {
        $this->is_active = false;
        $this->save();
        
        return true;
    }

    /**
     * Check if the model is active
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    /**
     * Check if the model is inactive
     *
     * @return bool
     */
    public function isInactive(): bool
    {
        return !$this->isActive();
    }
}
