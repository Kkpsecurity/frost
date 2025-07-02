<?php namespace App\Forms;

use App\Support\Enum\SecLevelList;
use AWS\CRT\HTTP\Request;
use Kris\LaravelFormBuilder\Form;

class DLLinkedForm extends Form
{
    public function buildForm()
    {

        $this->add('lesson_id', 'hidden', [
            'require',
            'value' => Request()->segment(9)
        ]);
        $this->add('title', 'text', [
            'require',
            'label' => 'Linked Title',
            'attr' => [
                'class'         => 'form-control',
                'placeholder'   => 'Enter the Text to display in link.'
            ]
        ]);
        $this->add('url', 'text', [
            'require'
        ]);

        $this->add('submit', 'button', [
            'label' => 'Process',
            'attr' => [
                'id'        => 'submit-linked-trigger',
                'class'     => 'btn btn-success pull-right mb-5'
            ]
        ]);
    }
}
