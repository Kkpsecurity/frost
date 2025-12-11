<?php namespace App\Forms;

use App\Support\Enum\CountriesList;
use Kris\LaravelFormBuilder\Form;


class UserProfileAddOnForm extends Form
{
    public function buildForm()
    {
        $this->add('profile_company', 'text', [
            'label' => 'Profile Company',
            'value' => $this->getData('profile_company')
        ])
        ->add('profile_title', 'text', [
            'label' => 'Profile Title',
            'value' => $this->getData('profile_title')
        ])
        ->add('shipping_address', 'textarea', [
            'label' => 'Shipping Address',
            'value' => $this->getData('shipping_address')
        ])
        ->add('country', 'select', [
            'label' => 'Shipping Country',
            'choices' => ['Select a Country'] + CountriesList::options(),
            'selected' => $this->getData('country')
        ]);


    }
}
