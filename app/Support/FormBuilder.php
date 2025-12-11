<?php
namespace App\Support;

use Collective\Html\FormFacade as Form;
use Illuminate\Database\Eloquent\Model;

/**
 * Class FormBuilder
 * @version 1.0.0
 * @package App\Support
 * @desc: This class is used to generate the form fields
 */

class FormBuilder
{


    protected $model;

    /** @var array */
    protected $fields = [];

    /**
     * @desc: Generate the OPEN form tag
     * @param $options array
     * @return string
     */
    public function openForm($options)
    {
        return Form::open($options);
    }

    /**
     * @desc: Generate the CLOSE form tag
     * @return string
     */
    public function closeForm()
    {
        return Form::close();
    }


    /**
     * Set The Lable
     */
    public function getFieldLabel($field)
    {
        if (isset($field['label'])) {
            return $field['label'];
        } else {
            return $this->formatTitle($field['name']);
        }
    }

    /**
     * @desc: Get Field Type
     */
    public function getFieldType($field)
    {
        if (isset($field['type'])) {
            return $field['type'];
        } else {
            return 'text';
        }
    }

    /**
     * Formats Label
     */
    public function formatTitle($title): string
    {
        if (strpos($title, '_id') !== false) {
            $title = str_replace('_id', '', $title);
        }

        if ($title == 'fname') {
            return 'First Name';
        }
        if ($title == 'lname') {
            return 'Last Name';
        }

        return ucwords(humanize($title));
    }

    /**
     * @desc: Generate select options
     * @param $field
     * @return array
     */
    protected function generateSelectOptions($field): array
    {
        switch ($field['name']) {
            case 'role_id':
                return $this->getRoleOptions();

            default:
                return [];
        }
    }

    /**
     * Get options for role select field
     *
     * @return array
     */
    protected function getRoleOptions(): array
    {
        return \App\Models\Role::all()
            ->pluck('name', 'id')
            ->toArray();
    }

    /**
     * Bind the model instance to the form builder.
     *
     * @param  mixed  $model
     * @param  array  $options
     * @return void
     */
    public function model($model, array $options = [])
    {
        $this->model = $model;
        $this->setModelBindings($model, $options);
    }

    /**
     * Bind the model's values to the form fields.
     *
     * @param  mixed  $model
     * @param  array  $options
     * @return void
     */
    protected function setModelBindings($model, array $options = [])
    {
        if ($model instanceof Model) {
            $this->bindModelValues($model, $options);
        }
    }

    /**
     * Bind values to the form fields.
     *
     * @param  Model  $model
     * @param  array  $options
     * @return void
     */
    protected function bindModelValues(Model $model, array $options = [])
    {
        foreach ($this->fields as $field) {
            $name = $field->getName();

            if (isset($options['value'][$name])) {
                $field->setValue($options['value'][$name]);
            } elseif (property_exists($model, $name)) {
                $field->setValue($model->$name);
            }
        }
    }

    /**
     * @desc: Generate the form type based on the column name
     * @param string $column
     * @return string
     */
    public function getFormType(string $column): string
    {
        $typeMap = [
            'id' => 'hidden',
            'email' => 'email',
            'password' => 'password',
            'is_active' => 'switch',
            'use_gravatar' => 'switch',
            'role_id' => 'select',
            'status_id' => 'select',
            'created_at' => 'date',
            'updated_at' => 'date',
            'deleted_at' => 'date',
            'avatar' => 'file',
            'file' => 'file',
            'image' => 'file',
            'photo' => 'file',
            'description' => 'textarea',
            'desc' => 'textarea',
            'body' => 'editor',
            'content' => 'editor',
            'text' => 'editor',
            'text_body' => 'editor'
        ];

        return $typeMap[$column] ?? 'text';
    }

    /**
     * @desc: Generate the form fields
     * @param array $field
     * @param array $options
     * @param mixed $data
     * @return string
     */
    public function generateFormField($field, $options, $data)
    {
        $typeHandlers = [
            'hidden' => function ($field) {
                return Form::hidden($field['name'], $field['value'], $field['attributes']);
            },

            'select' => function ($field) {
                return $this->wrapFormGroup($field, function ($field) {
                    return Form::select($field['name'], $this->generateSelectOptions($field), $field['value'], $field['attributes']);
                });
            },

            'textarea' => function ($field) {
                return $this->wrapFormGroup($field, function ($field) {
                    return Form::textarea($field['name'], $field['value'], $field['attributes']);
                });
            },

            'checkbox' => function ($field) {
                return $this->wrapFormGroup($field, function ($field) {
                    return Form::checkbox($field['name'], $field['value'], $field['attributes']);
                });
            },

            'radio' => function ($field) {
                return $this->wrapFormGroup($field, function ($field) {
                    return Form::radio($field['name'], $field['value'], $field['attributes']);
                });
            },

            'file' => function ($field) use ($data) {
                return $this->wrapFormGroup($field, function ($field) use ($data) {
                    $form = Form::label($field['label'], $this->formatTitle($field['name']));

                    if (!empty($data)) {
                        $form .= '<div class="mt-2">';
                        $form .= Form::hidden($field['name'], $data);
                        $form .= '  <img src="' . $data . '" alt="' . $this->formatTitle($field['name']) . '" style="max-height: 60px;">';
                        $form .= '</div>';

                        // Add a checkbox to allow for file deletion
                        $form .= '<div class="form-check mt-2">';
                        $form .= '  <input class="form-check-input" type="checkbox" name="delete_' . $field['name'] . '" id="delete_' . $field['name'] . '">';
                        $form .= '  <label class="form-check-label" for="delete_' . $field['name'] . '">Delete ' . $this->formatTitle($field['name']) . '</label>';
                        $form .= '</div>';
                    } else {
                        $form .= Form::file($field['name'], $field['attributes']);
                    }

                    return $form;
                });
            },
            
            'standard_input' => function($field) {
                // Special handling for password
                if ($field['type'] == 'password') {
                    $field['value'] = null;
                }
            
                return $this->wrapFormGroup($field, function($field) {
                    // Dynamically call the appropriate form method
                    $formMethod = $field['type'];
                    return Form::$formMethod($field['name'], $field['value'] ?? null, $field['attributes']);
                });
            },
            

            'default' => function ($field) {
                return $this->wrapFormGroup($field, function ($field) {
                    return Form::input($field['type'], $field['name'], $field['value'], $field['attributes']);
                });
            }
        ];

        $handler = $typeHandlers[$field['type']] ?? $typeHandlers['default'];

        return $handler($field);
    }

    private function wrapFormGroup($field, $callback)
    {
        $form = '<div class="form-group">';
        $form .= Form::label($field['label'], $this->formatTitle($field['name']));

        // Check if the 'class' key is set in attributes
        if (isset($field['attributes']['class'])) {
            $field['attributes']['class'] = 'form-control ' . $field['attributes']['class'];
        } else {
            $field['attributes']['class'] = 'form-control';
        }

        if ($field['type'] == 'checkbox') {
            // For checkbox type, override to use 'form-check-input'
            $field['attributes']['class'] = 'form-check-input';
        }

        $form .= $callback($field);
        $form .= '</div>';

        return $form;
    }
}