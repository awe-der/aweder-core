<?php

namespace Tests\Unit\Service;

use App\Contract\Service\CategoryContract;
use App\Service\CategoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Class CategoryServiceTest
 * @package Tests\Services
 * @coversDefaultClass \App\Service\OrderService
 */
class CategoryServiceTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * @var CategoryContract|mixed
     */
    private $categoryService;

    public function setUp(): void
    {
        parent::setUp();
        $this->categoryService = $this->app->make(CategoryContract::class);
    }

    /**
     * @test
     */
    public function can_add_category_by_payload_without_image(): void
    {
        $merchant = $this->createAndReturnMerchant();
        $payload = [
            'title' => 'Blurnsball',
            'visible' => 'true',
            'order' => 2,
        ];

        $this->assertCount(1, $merchant->categories()->get());

        $this->categoryService->addCategoriesAndSubCategoriesToMerchantFromPayload($merchant, $payload);
        $this->assertDatabaseHas('categories', [
            'title' => 'Blurnsball',
            'visible' => true,
            'order' => 2,
            'merchant_id' => $merchant->id
        ]);
    }

    /**
     * @test
     */
    public function can_add_category_by_payload_with_image(): void
    {
        Storage::fake('s3');

        $merchant = $this->createAndReturnMerchant();
        $image = UploadedFile::fake()->image('testimage.png', 100, 100)->size(100);

        $payload = [
            'title' => 'Blurnsball',
            'visible' => 'true',
            'order' => 2,
            'image' => $image
        ];

        $this->assertCount(1, $merchant->categories()->get());

        $this->categoryService->addCategoriesAndSubCategoriesToMerchantFromPayload($merchant, $payload);

        $this->assertDatabaseHas('categories', [
            'title' => 'Blurnsball',
            'visible' => true,
            'order' => 2,
            'merchant_id' => $merchant->id,
        ]);

        $this->assertDatabaseMissing('categories', [
            'title' => 'Blurnsball',
            'visible' => true,
            'order' => 2,
            'merchant_id' => $merchant->id,
            'image' => null
        ]);
    }

    public function cannot_add_category_from_payload_without_title(): void
    {
        $merchant = $this->createAndReturnMerchant();
        $payload = [
            'title' => null,
            'visible' => 'true',
            'order' => 2,
        ];

        $this->assertCount(1, $merchant->categories()->get());

        $return = $this->categoryService->addCategoriesAndSubCategoriesToMerchantFromPayload($merchant, $payload);
        $this->assertFalse($return);
        $this->assertCount(1, $merchant->categories()->get());
    }

    /**
     * @test
     */
    public function can_add_category_with_subcategories_by_payload(): void
    {
        $merchant = $this->createAndReturnMerchant();
        $subcategoryString = 'Person,Woman,Man,Camera,TV';

        $payload = [
            'title' => 'Blurnsball',
            'visible' => 'true',
            'order' => 2,
            'subCategories' => $subcategoryString
        ];

        $this->assertCount(1, $merchant->categories()->get());

        $this->categoryService->addCategoriesAndSubCategoriesToMerchantFromPayload($merchant, $payload);

        $this->assertDatabaseHas('categories', [
            'title' => 'Blurnsball',
            'visible' => true,
            'order' => 2,
            'merchant_id' => $merchant->id,
        ]);

        $this->assertCount(2, $merchant->categories()->get());
        $parentCategory = $merchant->categories()->where('title', 'Blurnsball')->first();
        $i = 0;

        $unpackedSubcategories = explode(',' , $subcategoryString);

        foreach ($parentCategory->subcategories() as $subcategory) {
            $this->assertEquals($unpackedSubcategories[$i], $subcategory->title);
            $i++;
        }
    }

    public function can_synchronize_subcategories(): void
    {
        $merchant = $this->createAndReturnMerchant();
        $category = $merchant->categories()->first();

        $subcategoryOne = $this->createAndReturnCategory([
            'title' => 'Old Title One',
            'parent_category_id' => $category->id
        ]);

        $subcategoryTwo = $this->createAndReturnCategory([
            'title' => 'Old Title Two',
            'parent_category_id' => $category->id
        ]);

        $newSubcategoryStrings = [
            'New Category One',
            'New Category Two'
        ];

        $this->assertEquals('Old Title One', $merchant->categories()->first()->subcategories()->first()->title);
        $this->assertEquals('Old Title Two', $merchant->categories()->first()->subcategories()->slice(2, 1)->title);

        $this->categoryService->synchronizeCategorySubcategories($category, $newSubcategoryStrings);

        $this->assertEquals('New Category One', $merchant->categories()->first()->subcategories()->first()->title);
        $this->assertEquals('New Category Two', $merchant->categories()->first()->subcategories()->slice(2, 1)->title);
        $this->assertCount(0, $merchant->categories()->first()->subcategories()->where('title', 'Old Title One'));
    }

    /**
     * @test
     */
    public function can_update_categories_from_payload(): void
    {
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

        $this->assertCount(0, $category->subcategories()->get());

        $payload = [
            'title' => 'Dipping Sauce',
            'merchant' => $merchant->url_slug,
            'order' => 2,
            'visible' => "false",
            'subCategories' => explode(',', 'Car,Person,TV')
        ];

        $this->categoryService->updateCategoriesAndSubCategoriesByMerchantFromPayload($merchant, $payload);

        $this->assertDatabaseMissing('categories', [
            'title' => 'Szechuan Sauce',
            'merchant_id' => $merchant->id,
            'order' => 2,
            'image' => 'IWantThatSzechuanSauceMorty.jpg',
            'visible' => true
        ]);

        $this->assertDatabaseHas('categories', [
            'title' => 'Dipping Sauce',
            'merchant_id' => $merchant->id,
            'order' => 2,
            'image' => 'IWantThatSzechuanSauceMorty.jpg',
            'visible' => false
        ]);

        $this->assertCount(3, $category->subcategories()->get());
    }

    /**
     * @test
     */
    public function can_update_category_image(): void
    {
        Storage::fake('s3');

        $merchant = $this->createAndReturnMerchant();
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

        $payload = [
            'title' => 'Dipping Sauce',
            'merchant' => $merchant->url_slug,
            'order' => 2,
            'visible' => "false",
            'image' => UploadedFile::fake()->image('testimage.png', 100, 100)->size(100),
        ];

        $this->categoryService->updateCategoriesAndSubCategoriesByMerchantFromPayload($merchant, $payload);

        $this->assertDatabaseMissing('categories', [
            'title' => 'Szechuan Sauce',
            'merchant_id' => $merchant->id,
            'order' => 2,
            'image' => 'IWantThatSzechuanSauceMorty.jpg',
            'visible' => true
        ]);

        $this->assertDatabaseHas('categories', [
            'title' => 'Dipping Sauce',
            'merchant_id' => $merchant->id,
            'order' => 2,
            'visible' => false
        ]);

        $this->assertDatabaseMissing('categories', [
            'title' => 'Dipping Sauce',
            'merchant_id' => $merchant->id,
            'order' => 2,
            'image' => 'IWantThatSzechuanSauceMorty.jpg',
        ]);
    }

    /**
     * @test
     */
    public function can_add_image_to_category(): void
    {
        Storage::fake('s3');
        $category = $this->createAndReturnCategory();

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'image' => null
        ]);

        $image = UploadedFile::fake()->image('testimage.png', 100, 100)->size(100);
        $this->categoryService->addImageToCategory($category, $image);

        Storage::disk('s3')->assertExists($category->image);

        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
            'image' => null
        ]);
    }
}
