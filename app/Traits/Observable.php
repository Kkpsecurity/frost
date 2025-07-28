<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

/**
 * @file Observable.php
 * @brief Trait for adding observable functionality to models.
 * @details Provides methods for adding observers and event handling to models.
 */

trait Observable
{
    /**
     * Boot the observable trait for the model.
     */
    protected static function bootObservable()
    {
        // Register observers for this model
        static::created(function ($model) {
            $model->fireObservableEvent('created');
        });

        static::updated(function ($model) {
            $model->fireObservableEvent('updated');
        });

        static::deleted(function ($model) {
            $model->fireObservableEvent('deleted');
        });
    }

    /**
     * Fire an observable event
     *
     * @param string $event
     * @return void
     */
    protected function fireObservableEvent($event)
    {
        // Log or handle the event as needed
        Log::info("Model {$this->getTable()} {$event}", [
            'id' => $this->getKey(),
            'model' => get_class($this)
        ]);
    }

    /**
     * Get the observable events
     *
     * @return array
     */
    public function getObservableEvents()
    {
        return [
            'retrieved', 'creating', 'created', 'updating', 'updated',
            'saving', 'saved', 'deleting', 'deleted', 'restoring', 'restored',
        ];
    }
}
