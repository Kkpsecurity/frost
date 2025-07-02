<?php namespace App\Forms;

use App\BCache;
use Kris\LaravelFormBuilder\Form;

class AccountProfileForm extends Form
{
    public function buildForm()
    {

        $this->add('fname', 'text', [
            'label' => 'First Name',
            'require'
        ])
        ->add('lname', 'text', [
            'label' => 'Last Name',
            'require'
        ])
        ->add('account', 'form', [
            'class' => $this->formBuilder->create( UserProfileAddOnForm::class, [], $this->getModel()->Profile->toArray())
        ])
        ->add('id', 'hidden')
        ->add('submit', 'submit',  [
            'label' => 'Update Profile Data',
            'attr'  => [
                'class'         => 'btn btn-success pull-right',
                'id'            => 'account-profile-form'
            ]
        ]);
    }
}
