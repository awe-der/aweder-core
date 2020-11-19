<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Contract\Repositories\InventoryContract;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateInventoryRequest;
use Illuminate\Http\RedirectResponse;

class CreateController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param CreateInventoryRequest $request
     *
     * @param InventoryContract $inventoryRepo
     * @return RedirectResponse
     */
    public function __invoke(CreateInventoryRequest $request, InventoryContract $inventoryRepo): RedirectResponse
    {
        $user = $request->user();

        $merchant = $user->merchants->first();

        if ($inventoryRepo->createNewInventoryItemForMerchant($merchant->id, $request->except('_token'))) {
            $request->session()->flash('success', 'The item has been added to your inventory');
        }

        return redirect()->to(route('admin.inventory'));
    }
}
