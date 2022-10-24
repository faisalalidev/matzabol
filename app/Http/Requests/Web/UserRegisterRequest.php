<?php

namespace App\Http\Requests\Frontend;

use App\Http\Requests\Jsonify as Request;

class UserRegisterRequest extends Request {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation messages.
     *
     * @return array
     */
    public function messages() {
        return [
            'email.required' => 'email is required',
            'email.unique' => 'email already found in our system, please try another one.',
            'password.required' => 'password is required.',
            'password.confirmed' => 'passwords do not match',
            'name.required' => 'first_name is required.',
            'lastname.required' => 'last name is required.',
            'password.regex' => 'only UAE numbers',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|confirmed|min:6',
            'name' => 'required|string|max:255',
            'lastname' => 'required|string',
            'phonenumber' => 'string|regex:^(?:\+971|00971|0)(?!2)((?:2|3|4|5|6|7|9|50|51|52|55|56)[0-9]{7,})',
            'address' => 'max:255',
//            'device_type' => 'required|string',
//            'device_token' => 'required|string',
//            'profile_picture' => 'required|image| mimes:jpeg,jpg,png',
        ];
    }

}
