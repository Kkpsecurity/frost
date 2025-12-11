<?php namespace App\Forms;

use Kris\LaravelFormBuilder\Form;
use App\Support\Enum\ProductTypes;

class ProductSelectForm extends Form
{
    public function buildForm()
    {

        $this->add('type_id', 'select', [
            'label' => 'Select A Product Type',
            'choices' => ['Select a Product Type'] + ProductTypes::options(),
            'selected' => $this->getData('type_id')
        ]);

        $this->add('Process', 'submit', [
            'label' => 'Select A Product Type',
            'attr'  => [
                'class' => 'btn btn-success pull-right'
            ]


        ]);
    }
}
