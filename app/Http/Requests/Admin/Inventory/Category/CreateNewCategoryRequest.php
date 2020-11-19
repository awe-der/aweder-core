<?php

namespace App\Http\Requests\Admin\Inventory\Category;

use App\Traits\RequestGetMerchantTrait;
use Illuminate\Foundation\Http\FormRequest;

class CreateNewCategoryRequest extends FormRequest
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
            'image' => ['image'],
            'merchant' => ['string', 'required'],
            'title' => ['string', 'required'],
            'visible' => ['string', 'required', 'in:true,false'],
            'subCategories' => ['string', 'nullable']
        ];
    }
}
