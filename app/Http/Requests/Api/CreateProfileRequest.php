<?php

namespace App\Http\Requests\Api;

use App\Helpers\RESTAPIHelper;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class CreateProfileRequest extends FormRequest
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

    protected function failedValidation(Validator $validator) {
        $response = RESTAPIHelper::response([],404,$validator->errors()->first());
        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'full_name' => 'required',
            'phone_number' => 'required|unique:users,phone_number,NULL,id,deleted_at,NULL',
            'dob' => 'required',
            'gender' => 'required',
            'marrital_status' => 'required',
            'religion_cast' => 'required',
            'height' => 'required',
            'country' => 'required',
            'ethnicity' => 'required',
            'nationality' => 'required',
            'language' => 'required',
            'profession' => 'required',
            'education' => 'required',
            'religion' => 'required',
            'my_status' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'device_type' => 'required',
            'device_token' => 'required',
            'image.*' => 'image|mimes:jpeg,bmp,png|max:2000'
        ];



        return $rules;
    }
}
