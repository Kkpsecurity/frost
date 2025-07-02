<?php namespace App\Forms\Fields;

use Kris\LaravelFormBuilder\Fields\FormField;

class MediaFile extends FormField {

    protected function getTemplate()
    {
        // At first, it tries to load config variable,
        // and if fails falls back to loading view
        // resources/views/fields/datetime.blade.php
        return 'fields.media-file';
    }

    public function render(array $options = [], $showLabel = true, $showField = true, $showError = true)
    {
        $options['media-text'] = 'Select a File';

        return parent::render($options, $showLabel, $showField, $showError);
    }
}
