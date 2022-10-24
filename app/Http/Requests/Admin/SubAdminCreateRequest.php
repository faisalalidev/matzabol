<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Request as Request;
use Carbon\Carbon;

class SubAdminCreateRequest extends Request
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
     * Get the validation messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'full_name.required'         => 'Name Is Required',
            'email.required'             => 'Email Is Required',
            'password.required'          => 'Password Is Required',
            'phone_number.required'      => 'Phone Number Is Required',
            'password_confirmation.same' => 'The password & confirmation password doesn\'t match',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'full_name'             => 'required|string',
            'email'                 => 'required|email|unique:users,email',
            'password'              => 'required|min:6',
            'password_confirmation' => 'required_with:password|same:password',
            'phone_number'          => 'required|phone|unique:users,phone_number',
            'status'                => 'required'
        ];
        return $rules;
    }

}
