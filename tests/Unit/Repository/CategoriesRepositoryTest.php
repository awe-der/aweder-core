<?php

namespace Tests\Unit\Repository;

use App\Category;
use App\Contract\Repositories\CategoryContract;
use App\Inventory;
use App\Merchant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

/**
 * Class CategoriesRepository
 * @package Tests\Unit\Repository
 * @group Categories
 */
class CategoriesRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var CategoryContract
     */
    protected CategoryContract $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = app()->make(CategoryContract::class);
    }

    /**
     * @test
     */
    public function create_categories_with_valid_data(): void
    {
        $merchant_id = 123;

        factory(Merchant::class)->create([
            'id' => $merchant_id
        ]);

        $categories = [
            'breakfast',
            'lunch',
            'dinner',
            'dessert'
        ];

        $response = $this->repository->createCategories($categories, $merchant_id);

        $this->assertInstanceOf(Collection::class, $response);

        foreach ($response as $category) {
            $this->assertDatabaseHas('categories', [
                'merchant_id' => $merchant_id,
                'order' => $category->order,
                'title' => $category->title
            ]);
        }
    }
    /**
     * @test
     */
    public function create_categories_with_some_null_fields(): void
    {
        $merchant_id = 123;

        factory(Merchant::class)->create([
            'id' => $merchant_id
        ]);

        $categories = [
            'breakfast',
            null,
            'dinner',
            null
        ];
        $response = $this->repository->createCategories($categories, $merchant_id);

        $this->assertInstanceOf(Collection::class, $response);

        foreach ($response as $category) {
            $this->assertDatabaseHas('categories', [
                'merchant_id' => $merchant_id,
                'order' => $category->order,
                'title' => $category->title
            ]);
        }
    }
    /**
     * @test
     */
    public function create_empty_categories(): void
    {
        $merchant_id = 123;

        factory(Merchant::class)->create([
            'id' => $merchant_id
        ]);

        $response = $this->repository->createEmptyCategory($merchant_id);

        $this->assertInstanceOf(Collection::class, $response);

        $this->assertDatabaseHas('categories', [
            'merchant_id' => $merchant_id,
            'id' => $response->first()->id,
            'order' => $response->first()->order,
            'title' => ''
        ]);
    }
    /**
     * @test
     */
    public function get_categories_and_inventory_with_multiple_categories_on_merchant(): void
    {
        $merchant_id = 123;

        factory(Merchant::class)->create([
            'id' => $merchant_id
        ]);

        for ($i = 0; $i < 5; $i++) {
            $category = factory(Category::class)->create([
                'merchant_id' => $merchant_id
            ]);
            factory(Inventory::class)->create([
                'merchant_id' => $merchant_id,
                'category_id' => $category->id
            ]);
        }

        $collection = $this->repository->getCategoryAndInventoryListForUser($merchant_id);

        $this->assertInstanceOf(Collection::class, $collection);

        $arrayOfCategories = $collection->all();

        $this->assertCount(5, $arrayOfCategories);

        foreach ($arrayOfCategories as $category) {
            $this->assertInstanceOf(Collection::class, $category->inventories);
            foreach ($category->inventories as $inventory) {
                $this->assertInstanceOf(Inventory::class, $inventory);
            }
        }
    }
    /**
     * @test
     */
    public function get_categories_and_inventory_with_multiple_categories_on_merchant_with_no_inventory(): void
    {
        $merchant_id = 123;

        factory(Merchant::class)->create([
            'id' => $merchant_id
        ]);

        factory(Category::class)->times(5)->create([
            'merchant_id' => $merchant_id
        ]);

        $collection = $this->repository->getCategoryAndInventoryListForUser($merchant_id);

        $this->assertInstanceOf(Collection::class, $collection);

        $arrayOfCategories = $collection->all();

        $this->assertCount(5, $arrayOfCategories);

        foreach ($arrayOfCategories as $category) {
            $this->assertInstanceOf(Collection::class, $category->inventories);
            $this->assertTrue($category->inventories->isEmpty());
        }
    }
    /**
     * @test
     */
    public function update_categories_with_valid_data(): void
    {
        $merchant_id = 123;

        factory(Merchant::class)->create([
            'id' => $merchant_id
        ]);

        for ($i = 1; $i < 4; $i++) {
            factory(Category::class)->create([
                'merchant_id' => $merchant_id,
                'id' => $i,
                'order' => $i
            ]);
        }

        $categories = [
            1 => 'breakfast',
            2 => 'lunch',
            3 => 'dinner'
        ];

        foreach ($categories as $category) {
            $this->assertDatabaseMissing('categories', [
                'title' => $category
            ]);
        }

        $this->assertTrue($this->repository->updateCategories($categories, $merchant_id));

        foreach ($categories as $category) {
            $this->assertDatabaseHas('categories', [
                'title' => $category
            ]);
        }
    }

    /**
     * @test
     */
    public function update_categories_with_invalid_keys(): void
    {
        $merchant_id = 123;

        factory(Merchant::class)->create([
            'id' => $merchant_id
        ]);

        for ($i = 1; $i < 4; $i++) {
            factory(Category::class)->create([
                'merchant_id' => $merchant_id,
                'id' => $i
            ]);
        }

        $categories = [
            'test' => 'breakfast',
            'test2' => 'lunch',
            'test3' => 'dinner'
        ];

        foreach ($categories as $category) {
            $this->assertDatabaseMissing('categories', [
                'title' => $category
            ]);
        }

        $this->assertTrue($this->repository->updateCategories($categories, $merchant_id));

        foreach ($categories as $category) {
            $this->assertDatabaseMissing('categories', [
                'title' => $category
            ]);
        }
    }

    /**
     * @test
     */
    public function can_add_category_to_merchant(): void
    {
        $merchant = $this->createAndReturnMerchant();
        $this->assertCount(1, $merchant->categories()->get());

        $category = $this->createAndReturnCategory();
        $this->repository->addCategoryToMerchant($merchant, $category);
        $this->assertCount(2, $merchant->categories()->get());
    }

    /**
     * @test
     */
    public function can_add_sub_category_to_merchant(): void
    {
        $merchant = $this->createAndReturnMerchant();
        $this->assertCount(1, $merchant->categories()->get());

        $category = $this->createAndReturnCategory(['merchant_id' => $merchant->id]);
        $subCategory = $this->createAndReturnCategory(['title' => 'Blurnsball']);

        $this->repository->addSubCategoryToCategory($category, $subCategory);
        $relatedCategory = $merchant->categories()->where('id', $category->id)->first();
        $this->assertEquals(
            'Blurnsball',
            $relatedCategory->subcategories()->first()->title
        );
    }

    /**
     * @test
     */
    public function can_add_subcategory_to_category_by_string(): void
    {
        $merchant = $this->createAndReturnMerchant();
        $this->assertCount(1, $merchant->categories()->get());
        $existingCategory = $merchant->categories()->first();
        $this->assertCount(0, $existingCategory->subcategories()->get());
    }

    /**
     * @test
     */
    public function can_remove_sub_category_for_merchant_but_still_view_items(): void
    {
        $merchant = $this->createAndReturnMerchant();
        $this->assertCount(1, $merchant->categories()->get());

        $category = $this->createAndReturnCategory(['merchant_id' => $merchant->id]);
        $subCategory = $this->createAndReturnCategory(['title' => 'Blurnsball']);

        $this->repository->addSubCategoryToCategory($category, $subCategory);
        $relatedCategory = $merchant->categories()->where('id', $category->id)->first();
        $this->assertEquals(
            'Blurnsball',
            $relatedCategory->subcategories()->first()->title
        );

        $inventoryItem = $this->createAndReturnInventoryItem(
            [
                'merchant_id' => $merchant->id,
                'category_id' => $subCategory->id,
                'title' => 'Blurnsball'
            ]
        );

        $this->assertEquals('Blurnsball', $subCategory->inventories()->first()->title);
        $this->repository->deleteCategory($subCategory);

        $this->assertDatabaseMissing(
            'categories',
            [
                'id' => $subCategory->id,
                'deleted_at' => null
            ]
        );

        $this->assertEquals(
            'Blurnsball',
            $merchant->inventories()->where('title', '=', 'Blurnsball')->first()->title
        );
    }

    public function cascade_delete_sub_categories_when_deleting_category(): void
    {
        $merchant = $this->createAndReturnMerchant();
        $this->assertCount(1, $merchant->categories()->get());

        $category = $this->createAndReturnCategory(['merchant_id' => $merchant->id]);
        $subCategory = $this->createAndReturnCategory();
        $this->repository->addSubCategoryToCategory($category, $subCategory);

        $this->repository->deleteCategory($category);

        $this->assertDatabaseHas(
            'categories',
            [
                'id' => $category->id,
                'deleted_at' => !null
            ]
        );

        $this->assertDatabaseHas(
            'categories',
            [
                'id' => $subCategory->id,
                'deleted_at' => !null
            ]
        );
    }

    /**
     * @test
     */
    public function can_retain_category_order_correctly(): void
    {
        $merchant = $this->createAndReturnMerchant();

        for ($i = 1; $i < 4; $i++) {
            factory(Category::class)->create([
                'merchant_id' => $merchant->id,
                'id' => $i,
                'order' => $i
            ]);
        }

        $categories = [
            1 => 'category one',
            2 => 'category two',
            3 => 'category three'
        ];

        foreach ($categories as $category) {
            $this->assertDatabaseMissing('categories', [
                'title' => $category
            ]);
        }

        $this->assertTrue($this->repository->updateCategories($categories, $merchant->id));

        foreach ($categories as $category) {
            $this->assertDatabaseHas('categories', [
                'title' => $category
            ]);
        }

        $currentMaxOrder = $merchant->categories()->max('order');

        $newCategory = $this->repository->addCategoryByStringToMerchant($merchant, 'category four');

        $this->assertDatabaseHas('categories', [
            'title' => 'category four',
            'merchant_id' => $merchant->id,
            'order' => $currentMaxOrder + 1
        ]);
    }
}
