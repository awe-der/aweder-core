<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class RegisterUserRequest
 * @package App\Http\Requests
 */
class RegisterUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email:rfc', 'unique:users,email'],
            'password' => ['zxcvbn_min:2', 'required'],
            'password-confirmed' => ['required', 'same:password'],
            'name' => ['required'],
            'url-slug' => ['required', 'alpha_dash', 'unique:merchants,url_slug'],
            'api-key' => ['sometimes', 'required'],
            'address-name-number' => ['required'],
            'address-street' => ['required'],
            'address-city' => ['required'],
            'address-postcode' => ['required'],
            'customer-phone-number' => ['required'],
            'collection_type' => ['required', Rule::in(['collection', 'both', 'delivery'])],
            'delivery_cost' => [
                Rule::requiredIf(function () {
                    return (request()->get('collection_type') === 'both'
                        || request()->get('collection_type') === 'delivery');
                })
            ],
            'delivery_radius' => [
                Rule::requiredIf(function () {
                    return (request()->get('collection_type') === 'both'
                        || request()->get('collection_type') === 'delivery');
                })
            ],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'username.required' => 'A username is required',
            'email.required' => 'A email is required',
            'email.email' => 'A valid email address is required',
            'password.required' => 'A password is required',
            'password-confirmed.confirmed' => 'Please confirm your password',
            'password-confirmed.same' => 'password and confirmation did not match',
            'name.required' => 'A business name is required',
            'url-slug.required' => 'A url slug is required',
            'api-key.required' => 'The API key is required',
            'address-name-number.required' => 'A number or name is required',
            'address-street.required' => 'A street is required',
            'address-city.required' => 'A city is required',
            'address-postcode.required' => 'A postcode is required',
            'customer-phone-number.required' => 'A contact number for customers is required',
            'password.zxcvbn_min' => 'Your password is not strong enough!',
        ];
    }
}
