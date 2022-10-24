<?php
/**
 * Created by PhpStorm.
 * User: shakirmirza
 * Date: 6/19/2018
 * Time: 2:07 PM
 */

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use App\Helpers\RESTAPIHelper;

class NotificationIDRequest extends FormRequest
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

    protected function failedValidation(Validator $validator)
    {
        $response = RESTAPIHelper::response([], 404, $validator->errors()->first());
        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_id'     => 'required',
            'messages_id' => 'required'
        ];
    }
}