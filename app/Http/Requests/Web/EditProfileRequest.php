<?php

namespace App\Http\Requests\Frontend;

use App\Http\Requests\Jsonify as Request;
use App\Http\Traits\JWTUserTrait;

class EditProfileRequest extends Request {

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
            'email.unique' => 'Email already found in our system, please try another one.',
            'profile_picture.mimes' => 'Only JPEG, PNG & BMP images are allowed',
            'profile_picture.max' => 'Sorry! Maximum allowed size for an image is 4 MB',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $userInstance = JWTUserTrait::getUserInstance();

        return [
            'email' => 'email|unique:users,email,' . $userInstance->id . ',id',
            'password' => 'string|min:6',
            'first_name' => 'string',
            'last_name' => 'string',
            'state' => 'string',
            'country' => 'string',
            'phone' => 'string',
            'company_name' => 'string',
            'profile_picture' => 'mimes:jpg,jpeg,png,bmp|max:4096'
        ];
    }

}
