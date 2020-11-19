<?php

namespace App\Http\Controllers\Admin\Inventory\Category;

use App\Contract\Service\CategoryContract;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Inventory\Category\UpdateCategoryRequest;

class UpdateController extends Controller
{
    public function __invoke(UpdateCategoryRequest $request, CategoryContract $categoryService)
    {
        $payload = $request->validated();
        $merchant = $request->getMerchant();

        if (!$merchant) {
            $request->session()->flash('error', 'No Merchant found');
            return redirect()->to(route('admin.inventory'));
        }

        if (!$categoryService->updateCategoriesAndSubCategoriesByMerchantFromPayload($merchant, $payload)) {
            $request->session()->flash('error', 'There was an error adding this category');
            return redirect()->to(route('admin.inventory'));
        }

        $request->session()->flash('success', 'Category Updated');
        return redirect()->to(route('admin.inventory'));
    }
}
