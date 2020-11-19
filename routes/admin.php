<?php

use Illuminate\Routing\Router;

Route::middleware(['auth', 'has-user-completed-registration-stage:0'])
    ->namespace('Admin')->prefix('admin')->group(function (Router $router) {
        $router->get('dashboard', ['uses' => 'Dashboard\\DashboardController'])
            ->middleware(['merchant-has-completed-setup'])
            ->name('admin.dashboard');

        $router->get('edit-details', ['uses' => 'Details\\EditDetailsController'])
            ->name('admin.details.edit');

        $router->post('edit-details', ['uses' => 'Details\\PostEditDetailsController'])
            ->name('admin.details.edit.post');

        $router->get('view-order/{order}', ['uses' => 'Order\\ViewOrderController'])
            ->middleware(['order-belongs-to-merchant'])
            ->name('admin.view-order');

        $router->post('view-order/{order}/reject', ['uses' => 'Order\\RejectOrderController'])
            ->middleware(['order-belongs-to-merchant', 'has-order-gone-past-stage:rejected'])
            ->name('admin.reject-order');

        $router->post('view-order/{order}/accept', ['uses' => 'Order\\AcceptOrderController'])
            ->middleware(['order-belongs-to-merchant', 'has-order-gone-past-stage:accepted'])
            ->name('admin.accept-order');

        $router->get('view-order/{order}/fulfilled', ['uses' => 'Order\\MarkOrderAsFulfilledController'])
            ->middleware(['order-belongs-to-merchant', 'has-order-gone-past-stage:fulfilled'])
            ->name('admin.order-fulfilled');

        $router->get('/opening-hours', ['uses' => 'OpeningHours\\SetupController'])
            ->name('admin.opening-hours');
        $router->post('/opening-hours', ['uses' => 'OpeningHours\\CreateController'])
            ->name('admin.opening-hours.post');
        $router->get('/categories', ['uses' => 'Categories\\SetupController'])
            ->name('admin.categories');
        $router->post('/categories', ['uses' => 'Categories\\CreateController'])
            ->name('admin.categories.post');
        $router->get('/orders', ['uses' => 'Order\\ViewAllOrdersController'])
            ->name('admin.orders.view-all');

        //manage inventory
        $router->get('/inventory', ['uses' => 'Inventory\\SetupController'])
            ->name('admin.inventory');
        $router->post('/inventory', ['uses' => 'Inventory\\CreateController'])
            ->name('admin.inventory.post');
        $router->get('/inventory/delete/{id}', ['uses' => 'Inventory\\DeleteController'])
            ->name('admin.inventory.delete');
        $router->get('/inventory/status/{id}', ['uses' => 'Inventory\\StatusController'])
            ->name('admin.inventory.status');
        $router->put('/inventory/{inventory}/update', ['uses' => 'Inventory\\UpdateController'])
            ->name('admin.inventory.update');
        $router->post('/inventory/category', ['uses' => 'Inventory\\Category\\CreateController'])
            ->name('admin.inventory.category.post');
        $router->patch('/inventory/category', ['uses' => 'Inventory\\Category\\UpdateController'])
            ->name('admin.inventory.category.update');

        $router->get(
            '/payment/stripe/oauth',
            [
                'uses' => 'Payment\\Stripe\\OAuthRedirectController',
            ]
        )->name('admin.stripe-oauth.redirect');

        $router->get(
            '/payment/stripe/deauthorize',
            [
                'uses' => 'Payment\\Stripe\\DeauthorizeController',
            ]
        )->name('admin.stripe-oauth.deauthorize');
    });
