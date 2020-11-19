<?php

namespace App;

use Iatstuti\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Category
 *
 * @property int $id
 * @property int $merchant_id
 * @property int $category_id
 * @property string $title
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Inventory[] $inventories
 * @property-read int|null $inventories_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Category query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Category whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Category whereMerchantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Category whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Category whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Inventory[] $inventoriesAvailable
 * @property-read int|null $inventories_available_count
 */
class Category extends Model
{
    use SoftDeletes;
    use CascadeSoftDeletes;

    protected $fillable = [
        'merchant_id',
        'parent_category_id',
        'order',
        'title',
        'visible',
        'image'
    ];

    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    public function inventoriesAvailable(): HasMany
    {
        return $this->hasMany(Inventory::class, 'category_id')->where('available', 1);
    }

    public function parentCategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_category_id', 'id');
    }

    public function subcategories(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_category_id', 'id');
    }
}
