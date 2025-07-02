<?php

namespace App\Http\Requests\Admin\Users;

use Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use KKP\TextTk;


class UpdateRequest extends FormRequest
{

    //
    // NOTE: this does not check password;
    //       that should be a different form
    //


    public function authorize() : bool
    {
        return Auth::user()->IsSupport();
    }


    public function prepareForValidation()
    {

        //
        // ensure sanitized fields are not empty
        //

        $this->merge([

            'fname' => TextTk::Sanitize( $this->fname ),
            'lname' => TextTk::Sanitize( $this->lname ),
            'email' => TextTk::Sanitize( $this->email ),

        ]);

    }


    public function rules() : array
    {
        return [

            'fname' => 'required|min:2|max:255',
            'lname' => 'required|min:1|max:255',
            'email' => [
                'required',
                'max:255',
                'email:strict',
                Rule::unique( User::class, 'email' )->ignore( $this->route( 'user' )->id )
            ],

        ];
    }


    public function messages() : array
    {
        return [

            'fname.required' => 'First Name may not be empty.',
            'fname.min'      => 'First Name must be at least 2 characters.',

            'lname.required' => 'Last Name may not be empty.',
            'lname.min'      => 'Last Name must be at least 1 character.',

            'email.required' => 'Email Address may not be empty.',
            'email.email'    => 'Invalid Email Address.',
            'email.unique'   => 'Email Address is already registered.',

        ];
    }


}
