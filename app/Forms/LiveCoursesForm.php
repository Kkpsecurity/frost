<?php namespace App\Forms;


use App\Support\Enum\ProductTypes;
use Kris\LaravelFormBuilder\Form;

class LiveCoursesForm extends Form
{
    public function buildForm()
    {
        $this->add('type_id', 'select', [
            'label' => 'Select A Product Type',
            'choices' => ['Select a Product Type'] + ProductTypes::options(),
            'selected' => $this->getData('type_id')
        ]);
    }
}
