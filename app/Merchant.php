<?php

namespace App;

use App\Traits\HelperTrait;
use App\Traits\TrackChanges;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

/**
 * App\Merchant
 *
 * @property int $id
 * @property string $url_slug
 * @property string $name
 * @property string|null $contact_email
 * @property string $contact_number
 * @property string $address
 * @property string|null $mobile_number the mobile number for SMS notifications
 * @property string|null $notification_method the method to use for notifications
 * @property string|null $customer_phone_number the telephone number to display to customers
 * @property int|null $allow_delivery whether the merchant allows delivery
 * @property int|null $allow_collection whether the merchant allows collection
 * @property string|null $address_name_number the building name or number for the address
 * @property string|null $address_street the street for the address
 * @property string|null $address_city the city for the address
 * @property string|null $address_county the county for the address
 * @property string|null $address_postcode the postcode for the address
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Category[] $categories
 * @property-read int|null $categories_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Inventory[] $inventories
 * @property-read int|null $inventories_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Merchant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Merchant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Merchant query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Merchant whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Merchant whereAddressCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Merchant whereAddressCounty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Merchant whereAddressLocality($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Merchant whereAddressNameNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Merchant whereAddressPostcode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Merchant whereAddressStreet($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Merchant whereAllowCollection($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Merchant whereAllowDelivery($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Merchant whereContactEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Merchant whereContactNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Merchant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Merchant whereCustomerPhoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Merchant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Merchant whereMobileNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Merchant whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Merchant whereNotificationMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Merchant whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Merchant whereUrlSlug($value)
 * @mixin \Eloquent
 * @property string|null $salt
 * @property int|null $registration_stage
 * @property string|null $description
 * @property string|null $logo
 * @property int $delivery_cost
 * @property int $delivery_radius
 * @property-read string $formatted_u_k_price
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\NormalOpeningHour[] $openingHours
 * @property-read int|null $opening_hours_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Provider[] $paymentProviders
 * @property-read int|null $payment_providers_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Merchant whereDeliveryCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Merchant whereDeliveryRadius($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Merchant whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Merchant whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Merchant whereRegistrationStage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Merchant whereSalt($value)
 * @property int $allow_table_service
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Merchant whereAllowTableService($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Order[] $orders
 * @property-read int|null $orders_count
 */
class Merchant extends Model
{
    use TrackChanges;
    use HelperTrait;

    protected $fillable = [
        'salt',
        'registration_stage',
        'url_slug',
        'name',
        'description',
        'logo',
        'contact_email',
        'contact_number',
        'address',
        'mobile_number',
        'notification_method',
        'customer_phone_number',
        'allow_delivery',
        'allow_collection',
        'allow_table_service',
        'delivery_cost',
        'delivery_radius',
        'address_name_number',
        'address_street',
        'address_city',
        'address_county',
        'address_postcode',
    ];

    /**
     * Events to be tracked
     *
     * @var array $trackingEvents
     */
    protected static array $tracking_events = [
        'created',
        'updated'
    ];

    /**
     * Data to be stored by tracker
     *
     * @var array $filtered_data
     */
    protected static array $filtered_data = [
        'registration_stage'
    ];

    /**
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'merchant_users');
    }

    /**
     * returns the categories in the menu
     * @return HasMany
     */
    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function visibleCategories(): HasMany
    {
        return $this->categories()->where('visible', 1);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'merchant_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    /**
     * @return BelongsToMany
     */
    public function paymentProviders(): BelongsToMany
    {
        return $this->belongsToMany(Provider::class, 'merchant_payment')
            ->withPivot('data');
    }

    /**
     * @return HasMany
     */
    public function openingHours(): HasMany
    {
        return $this->hasMany(NormalOpeningHour::class);
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'url_slug';
    }

    /**
     * method to check if the merchant allows both delivery and collection
     * @return bool
     */
    public function doesMerchantAllowDeliveryAndCollection(): bool
    {
        return $this->allow_delivery === 1 && $this->allow_collection === 1;
    }

    /**
     * checks if the merchant allows delivery
     * @return bool
     */
    public function deliveryOnly(): bool
    {
        return $this->allow_delivery === 1;
    }

    public function doesMerchantSupportDeliveryType(string $deliveryType): bool
    {
        if ($deliveryType === 'delivery' && $this->allow_delivery === 1) {
            return true;
        }

        if ($deliveryType === 'collection' && $this->allow_collection === 1) {
            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    public function getTemporaryLogoLink(): string
    {
        if (!$this->logo) {
            return '';
        }

        if (strpos($this->logo, 'http') === 0) {
            return $this->logo;
        }

        return Storage::temporaryUrl($this->logo, now()->addMinutes(10));
    }

    /**
     * @return bool
     */
    public function hasStripePaymentsIntegration(): bool
    {
        $provider = $this->paymentProviders->where('name', 'Stripe')->first();

        if ($provider instanceof Provider) {
            return true;
        }
        return false;
    }

    /**
     * @return Provider|null
     */
    public function stripePayment(): ?Provider
    {
        return $this->paymentProviders
            ->where('name', 'Stripe')
            ->first();
    }

    public function getMerchantAcceptableOrderTypes(): array
    {
        $orderTypes = [];

        if ($this->allow_collection === 1) {
            $orderTypes[] = 'collection';
        }

        if ($this->allow_delivery === 1) {
            $orderTypes[] = 'delivery';
        }

        if ($this->allow_table_service === 1) {
            $orderTypes[] = 'table';
        }

        return $orderTypes;
    }
}
