<?php namespace App\Forms;

use App\Models\User;
use Kris\LaravelFormBuilder\Form;

class UserForm extends Form
{
    public function buildForm()
    {

        $this->add('fname', 'text', [
            'require',
            'label' => 'First Name',
            'attr' => ['class' => 'form-control']
        ])

        ->add('lname', 'text', [
            'require',
            'label' => 'Last Name',
            'attr' => ['class' => 'form-control']
        ])
        ->add('email', 'text', [
            'require',
            'attr' => ['class' => 'form-control']
        ])
        ->add('role_id', 'select', [
            'require',
            'choices' => [
                '0' => 'Select an Option',
                '1' => 'Sys Admin',
                '2' => 'Administrator',
                '3' => 'Instructor',
                '4' => 'Student',
            ],
            'attr' => ['class' => 'form-control']
        ])
        ->add('password', 'password', [
            'require',
            'attr' => ['class' => 'form-control', 'placeholder'=>'Leave Blank to Not Change the Password.'],
            'help_block' => [
                'text' => '<span id="show-password" class="bold p-2"></span><a href="#" id="generate-password" class="btn btn-xs btn-link">Generate Password</a>',
                'tag' => 'p',
                'attr' => ['class' => 'help-block']
            ],
        ])
        ->add('password_confirmation', 'password', [
            'require',
             'attr' => ['class' => 'form-control']
        ])
        ->add('id', 'hidden');
    }
}
