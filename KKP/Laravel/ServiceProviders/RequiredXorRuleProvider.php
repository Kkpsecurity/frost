<?php

//
// jonesy: this needs work
//


// https://gist.github.com/tdhsmith/df797334060462936fb62acb5fdf5489
namespace KKP\Laravel\ServiceProviders;

use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;
use Validator;

class RequiredXorRuleProvider extends ServiceProvider
{
    // Define rule message constant. This might be better in the language resource file,
    // but I personally prefer to keep the extension self-contained.
    ###const REQUIRED_XOR_FAILURE_MESSAGE = 'Exactly one of the :attribute or :values fields must be present.';
    const REQUIRED_XOR_FAILURE_MESSAGE = 'Exactly one of the :attribute or :values fields must be present.';

    /**
     * Bootstrap the service and extend the validator with any custom rules we want.
     *
     * @return void
     */
    public function boot()
    {
        // Must be *implicit* or the validator won't run it when the attribute isn't present.
        Validator::extendImplicit('required_xor', function ($attribute, $value, $parameters, $validator) {
            // To examine values of attributes other than the one this rule applied to
            // we need the getValue function, which requires data and files arrays.
            $this->data = $validator->getData();
            ### $this->files = $validator->getFiles();

            return $this->validateRequiredXOR($attribute, $value, $parameters);
        }, self::REQUIRED_XOR_FAILURE_MESSAGE);
    }

    /**
     * Register the service provider.
     * We don't actually need to register anything, but this is an abstract method
     * on ServiceProvider, so we must provide an (empty) implementation here.
     *
     * @return void
     */
    public function register()
    {
    }

    // This is the rule as it would likely exist within the validator class
    protected function validateRequiredXOR($attribute, $value, $parameters)
    {
        $this->requireParameterCount(1, $parameters, 'required_xor');

        if ($this->validateRequired($attribute, $value)) {
            return !$this->validateRequired($parameters[0], $this->getValue($parameters[0]));
        } else {
            return $this->validateRequired($parameters[0], $this->getValue($parameters[0]));
        }
    }

    // The following are all functions copied from the Laravel 5.2 standard Validator.
    // Since they are all protected, we have to recreate them to use them in this
    // service provider (or do weird inheritance trickery).

    // https://github.com/laravel/framework/blob/5.2/src/Illuminate/Validation/Validator.php#L677
    protected function validateRequired($attribute, $value)
    {
        if (is_null($value)) {
            return false;
        } elseif (is_string($value) && trim($value) === '') {
            return false;
        } elseif ((is_array($value) || $value instanceof Countable) && count($value) < 1) {
            return false;
        } elseif ($value instanceof File) {
            return (string) $value->getPath() != '';
        }
        return true;
    }

    // https://github.com/laravel/framework/blob/5.2/src/Illuminate/Validation/Validator.php#L3143
    protected function requireParameterCount($count, $parameters, $rule)
    {
        if (count($parameters) < $count) {
            throw new InvalidArgumentException("Validation rule $rule requires at least $count parameters.");
        }
    }

    // https://github.com/laravel/framework/blob/5.2/src/Illuminate/Validation/Validator.php#L519
    protected function getValue($attribute)
    {
        if (! is_null($value = Arr::get($this->data, $attribute))) {
            return $value;
        ###} elseif (! is_null($value = Arr::get($this->files, $attribute))) {
        ###    return $value;
        }
    }

}
