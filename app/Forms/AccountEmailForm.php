<?php namespace App\Forms;


use Kris\LaravelFormBuilder\Form;

class AccountEmailForm extends Form
{

    public function buildForm()
    {
        $this->add('email', 'email', [
            'attr'  => [
                'class'     => 'form-control',
                'id'        => 'email'
            ]
        ])
        ->add('email_confirmation', 'email', [
            'attr'  => [
                'class'     => 'form-control',
                'id'        => 'email_confirmation'
            ]
        ])
        ->add('password', 'password', [
            'attr'  => [
                'class'     => 'form-control',
                'id'        => 'password'
            ]
        ])
        ->add('password_confirmation', 'password', [
            'attr'  => [
                'class'     => 'form-control',
                'id'        => 'password_confirmation',
            ]
        ])
        ->add('id', 'hidden')
        ->add('submit', 'submit',  [
            'label' => 'Update Auth Info',
            'attr'  => [
                'class' =>'btn btn-success pull-right',
                'id'    => 'account-auth-trigger'
            ]
        ]);
    }
}
