<?php

namespace App\Http\Requests\Api;

use App\Helpers\RESTAPIHelper;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UpdateForgotPasswordRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    protected function failedValidation(Validator $validator) {
        $response = RESTAPIHelper::response([],404,$validator->errors()->first());
        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }



    public function rules()
    {
        return [
            'email' => 'required',
            'password' => 'required|confirmed',
            'device_token' => 'required'
        ];
    }
    
}
