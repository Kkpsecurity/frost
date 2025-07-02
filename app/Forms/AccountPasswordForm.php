<?php namespace App\Forms;

use App\Models\User;
use Kris\LaravelFormBuilder\Form;

class AccountPasswordForm extends Form
{

    /**
     * @return mixed|void
     */
    public function buildForm()
    {
        $this->add('old_password', 'password', [
            'attr'  => [
                'class'     => 'form-control',
                'id'        => 'old_password',
                'required'  => 'required'
            ]
        ])
        ->add('password', 'password', [
             'attr'  => [
                'class'     => 'form-control',
                'id'        => 'password',
                 'required' => 'required'
              ]
         ])
         ->add('password_confirmation', 'password', [
             'attr'  => [
                'class'     => 'form-control',
                'id'        => 'password_confirmation',
                'required'  => 'required'
             ]
        ])
         ->add('id', 'hidden')
         ->add('submit', 'submit',  [
            'label' => 'Update Password Data',
            'attr'  => [
                'class' =>'btn btn-success pull-right',
                'id'    => 'account-form-trigger'
            ]
         ]);
    }
}
