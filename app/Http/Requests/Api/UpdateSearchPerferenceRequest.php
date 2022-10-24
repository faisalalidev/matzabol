<?php

namespace App\Http\Requests\Api;

use App\Helpers\RESTAPIHelper;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSearchPerferenceRequest extends FormRequest
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

    public function rules()
    {
        return [
            'user_id' => 'required',
            'by_location' => 'boolean',
            'distance' => 'required_if:by_location,1',
            'by_country' => 'boolean',
            'country' => 'required_if:by_country,1',
            'by_age_range' => 'boolean',
            'age_range' => 'required_if:by_age_range,1',
            'by_ethnicity' => 'boolean',
            'ethnicity' => 'required_if:by_ethnicity,1'
        ];
    }
}
