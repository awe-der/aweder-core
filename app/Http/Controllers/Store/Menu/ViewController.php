<?php

namespace App\Http\Controllers\Store\Menu;

use App\Contract\Repositories\OrderContract;
use App\Http\Controllers\Controller;
use App\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ViewController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param OrderContract $orderRepository
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, OrderContract $orderRepository): Response
    {
        $merchant = $request->merchant;

        $editable = true;

        if ($request->order instanceof Order) {
            $order = $request->order;

            if (!$order->isEditable()) {
                $editable = false;
            }

            $order->load('items', 'items.inventory');
        }

        $merchant->load(
            'visibleCategories',
            'openingHours',
            'categories.inventoriesAvailable.variants',
            'categories.inventoriesAvailable.optionGroups.items'
        );

        return response()->view(
            'merchant.inventory',
            [
                'merchant' => $merchant,
                'order' => $order ?? null,
                'editable' => $editable,
                'bodyClass' => 'body-menu',
            ]
        );
    }
}
