<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdminsettingRequest extends FormRequest
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

    public function messages()
    {
        return [
            'adminname.required' => 'Admin Is Required',

        ];
    }
    public function rules()
    {
        return [
            'adminname' => 'required|string|max:255',

        ];
    }
}
