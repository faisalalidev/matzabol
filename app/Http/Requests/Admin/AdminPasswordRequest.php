<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminPasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'old'      => 'required',
            'password' => 'required|min:6|confirmed',
        ];
        return $rules;
    }

    public function messages()
    {
        return [
            'old.required'       => 'Current Password is required',
            'password.confirmed' => ' Password do not match',
            'password.min'       => ' Min 6 charaters',

        ];
    }
}
