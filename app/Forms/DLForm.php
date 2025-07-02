<?php namespace App\Forms;

use App\Support\Enum\SecLevelList;
use AWS\CRT\HTTP\Request;
use Kris\LaravelFormBuilder\Form;

class DLForm extends Form
{
    public function buildForm()
    {

        $this->add('title', 'text', [
            'label' => 'Course Title',
            'attr' => [
                'required'           => true,
                'class'         => 'form-control',
                'placeholder'   => 'Enter the Title of the Course'
            ]
        ]);
        $this->add('type_id', 'hidden', [
            'value' => 3
        ]);

        $this->add('courseid', 'text', [
            'required'           => true,
            'label'             => 'Course ID',
            'attr'              => [
                'required'           => true,
                'class' => 'form-control'
            ]
        ]);

        $this->add('is_active', 'checkbox', [
            'label'             => 'Is Active',
            'attr'              => [
                'required'  => false,
                'class'     => 'checkbox'
            ]
        ]);

        $this->add('def_title', 'text', [
            'label'             => 'Default (Product Title)',
            'attr'              => [
                'required'           => true,
                'class' => 'form-control'
            ]
        ]);

        $this->add('def_price', 'text', [
            'label'             => 'Default (Product Price)',
            'attr'              => [
                'required'           => true,
                'class' => 'form-control'
            ]
        ]);

         $this->add('num_exam_questions', 'select', [
             'choices'           => ['Select a Number of questions'] +  range(1, 100),
             'label'             => 'Number of exams',
             'attr'              => [
                 'required'           => true,
                 'class' => 'form-control'
             ]
         ]);

        $this->add('def_seclvl', 'select', [
            'choices'           => ['Select a Security Level'] +  SecLevelList::options(),
            'label'             => 'Default (Security Level)',
            'attr'              => [
                'required'  => true,
                'class'     => 'form-control'
            ]
        ]);

        $this->add('timestr', 'text', [
            'label'             => 'DLCourse Total Time',
            'attr'              => [
                'required'  => true,
                'class'     => 'form-control'
            ]
        ]);

        $this->add('description', 'textarea', [
            'required'           => false,
            'label'             => 'DL Description',
            'attr'              => ['class' => 'form-control']
        ]);

        $this->add('Process', 'submit', [
            'attr'              => [
                'class'     => 'btn btn-success'
            ]]);
    }
}
