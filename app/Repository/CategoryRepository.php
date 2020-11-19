<?php

namespace App\Repository;

use App\Merchant;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Psr\Log\LoggerInterface;
use App\Contract\Repositories\CategoryContract;
use App\Category;

/**
 * Class CategoryRepository
 * @package App\Repository
 */
class CategoryRepository implements CategoryContract
{
    protected Category $model;

    protected LoggerInterface $logger;

    /**
     * CategoryRepository constructor.
     * @param \App\Category $model
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(Category $model, LoggerInterface $logger)
    {
        $this->model = $model;

        $this->logger = $logger;
    }

    public function createEmptyCategory(int $merchantId): SupportCollection
    {
        return $this->createCategories([''], $merchantId);
    }

    /**
     * @param int $merchantId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getCategoryAndInventoryListForUser(int $merchantId): Collection
    {
        return $this->getModel()
            ->query()
            ->where('merchant_id', '=', $merchantId)
            ->with('inventories')
            ->get();
    }

    public function createCategories(array $categories, int $merchant_id): SupportCollection
    {
        $categories = array_values($categories);

        $categoryCollection = new SupportCollection();

        foreach ($categories as $key => $value) {
            $category = $this->getModel()->create([
                'merchant_id' => $merchant_id,
                'order' => $key + 1,
                'title' => $value
            ]);
            $categoryCollection->add($category);
        }

        return $categoryCollection;
    }

    public function updateCategories(array $categories, int $merchantId): bool
    {
        foreach ($categories as $key => $category) {
            $categoryModel = $this->getModel()
                ->where('order', $key)
                ->where('merchant_id', $merchantId)
                ->first();

            if ($categoryModel instanceof Category) {
                $categoryModel->update([
                    'title' => $category
                ]);
            }
        }

        return true;
    }

    /**
     * @return Category
     */
    protected function getModel(): Category
    {
        return $this->model;
    }

    public function addCategoryToMerchant(Merchant $merchant, Category $category): bool
    {
        return (bool) $merchant->categories()->save($category);
    }

    public function getCategoryMaxOrderForMerchant(Merchant $merchant): int
    {
        return $merchant->categories()->max('order');
    }

    public function addCategoryByStringToMerchant(Merchant $merchant, string $categoryTitle): bool
    {
        $category = new Category([
            'title' => $categoryTitle,
            'order' => $this->getCategoryMaxOrderForMerchant($merchant) + 1
        ]);

        return $this->addCategoryToMerchant($merchant, $category);
    }

    public function addSubCategoryToCategory(Category $category, Category $subCategory): bool
    {
        return (bool) $category->subcategories()->save($subCategory);
    }

    public function addSubCategoryToCategoryByString(Category $category, string $subCategoryTitle): bool
    {
        $subcategory = new Category([
            'title' => $subCategoryTitle
        ]);

        return $this->addSubCategoryToCategory($category, $subcategory);
    }

    public function deleteCategory(Category $category): bool
    {
        return $category->delete();
    }

    public function getCategoryByOrderAndMerchant(Merchant $merchant, int $order): ?Category
    {
        return $this->getModel()
            ->where('merchant_id', $merchant->id)
            ->where('order', $order)
            ->first();
    }
}
