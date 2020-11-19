<?php

namespace App\Http\Requests\Admin\Inventory\Category;

use App\Traits\RequestGetMerchantTrait;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    use RequestGetMerchantTrait;

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
            'image' => ['image', 'nullable'],
            'merchant' => ['string', 'required'],
            'order' => ['integer', 'required'],
            'title' => ['string', 'nullable'],
            'visible' => ['string', 'nullable'],
            'subCategories' => ['string', 'nullable']
        ];
    }
}
