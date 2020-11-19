<?php

namespace Tests\Feature\Admin\Inventory;

use App\Contract\Repositories\InventoryContract;
use App\Http\Controllers\Admin\Inventory\UpdateController;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UpdateCategoryControllerTest extends TestCase
{
    use RefreshDatabase;


    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @test
     */
    public function can_update_entire_category_with_payload(): void
    {
        $user = factory(User::class)->create();

        $merchant = $this->createAndReturnMerchant([]);
        $category = $this->createAndReturnCategory(
            [
                'title' => 'Szechuan Sauce',
                'merchant_id' => $merchant->id,
                'order' => 2,
                'image' => 'IWantThatSzechuanSauceMorty.jpg'
            ]
        );

        $this->assertDatabaseHas('categories', [
            'title' => 'Szechuan Sauce',
            'merchant_id' => $merchant->id,
            'order' => 2,
            'image' => 'IWantThatSzechuanSauceMorty.jpg',
            'visible' => true
        ]);

        $this->actingAs($user);

        $response = $this->patch(route('admin.inventory.category.update', [
            'title' => 'Dipping Sauce',
            'merchant' => $merchant->url_slug,
            'order' => 2,
            'visible' => "false",
            'subCategories' => 'Car,Person,TV'
        ]));

        $this->assertDatabaseMissing('categories', [
            'title' => 'Szechuan Sauce',
            'merchant_id' => $merchant->id,
            'order' => 2,
            'image' => 'IWantThatSzechuanSauceMorty.jpg',
            'visible' => true
        ]);
    }

    /**
     * @test
     */
    public function cannot_update_entire_category_without_merchant(): void
    {
        $user = factory(User::class)->create();

        $merchant = $this->createAndReturnMerchant([]);
        $category = $this->createAndReturnCategory(
            [
                'title' => 'Szechuan Sauce',
                'merchant_id' => $merchant->id,
                'order' => 2,
                'image' => 'IWantThatSzechuanSauceMorty.jpg'
            ]
        );

        $this->assertDatabaseHas('categories', [
            'title' => 'Szechuan Sauce',
            'merchant_id' => $merchant->id,
            'order' => 2,
            'image' => 'IWantThatSzechuanSauceMorty.jpg',
            'visible' => true
        ]);

        $this->actingAs($user);

        $response = $this->patch(route('admin.inventory.category.update', [
            'title' => 'Dipping Sauce',
            'order' => 2,
            'visible' => "false",
            'subCategories' => 'Car,Person,TV'
        ]));

        $response->assertStatus(Response::HTTP_FOUND);

        $this->assertDatabaseHas('categories', [
            'title' => 'Szechuan Sauce',
            'merchant_id' => $merchant->id,
            'order' => 2,
            'image' => 'IWantThatSzechuanSauceMorty.jpg',
            'visible' => true
        ]);
    }
}
