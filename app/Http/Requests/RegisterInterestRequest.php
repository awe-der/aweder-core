<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterInterestRequest extends FormRequest
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
        return [
            'business' => ['required'],
            'location' => ['required'],
            'email' => ['required', 'email:rfc'],
            'business_type' => ['required', Rule::in(['restaurant', 'pub', 'cafe', 'retailer', 'other'])],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'business.required' => 'A business name is required',
            'location.required'  => 'A location is required',
            'email.required' => 'A email is required',
            'email.email' => 'A valid email address is required',
            'business_type.required' => 'A business type is required',
        ];
    }
}
