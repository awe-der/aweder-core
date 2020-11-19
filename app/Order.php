<?php

namespace App;

use App\Traits\HelperTrait;
use App\Traits\TrackChanges;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Order
 *
 * @property int $id
 * @property string $url_slug
 * @property int $merchant_id
 * @property string|null $status
 * @property int $is_delivery
 * @property string|null $customer_name
 * @property string|null $customer_email
 * @property string|null $customer_address
 * @property string|null $customer_phone
 * @property string|null $available_time
 * @property string|null $rejection_reason
 * @property int|null $total_cost
 * @property int|null $final_cost
 * @property string|null $merchant_note
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read float $formatted_u_k_price
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\OrderItem[] $items
 * @property-read int|null $items_count
 * @property-read \App\Merchant $merchant
 * @method static Builder|\App\Order newModelQuery()
 * @method static Builder|\App\Order newQuery()
 * @method static Builder|\App\Order query()
 * @method static Builder|\App\Order whereAvailableTime($value)
 * @method static Builder|\App\Order whereCreatedAt($value)
 * @method static Builder|\App\Order whereCustomerAddress($value)
 * @method static Builder|\App\Order whereCustomerEmail($value)
 * @method static Builder|\App\Order whereCustomerName($value)
 * @method static Builder|\App\Order whereCustomerPhone($value)
 * @method static Builder|\App\Order whereFinalCost($value)
 * @method static Builder|\App\Order whereId($value)
 * @method static Builder|\App\Order whereIsDelivery($value)
 * @method static Builder|\App\Order whereMerchantId($value)
 * @method static Builder|\App\Order whereMerchantNote($value)
 * @method static Builder|\App\Order whereRejectionReason($value)
 * @method static Builder|\App\Order whereStatus($value)
 * @method static Builder|\App\Order whereTotalCost($value)
 * @method static Builder|\App\Order whereUpdatedAt($value)
 * @method static Builder|\App\Order whereUrlSlug($value)
 * @mixin \Eloquent
 * @property string|null $customer_note
 * @method static Builder|\App\Order whereCustomerNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order status($value)
 * @property string|null $order_submitted
 * @property string|null $customer_requested_time
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order whereCustomerRequestedTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order whereOrderSubmitted($value)
 * @property int $delivery_cost
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\OrderReminder[] $reminders
 * @property-read int|null $reminders_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order whereDeliveryCost($value)
 * @property string|null $payment_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order wherePaymentId($value)
 * @property int $is_table_service
 * @property int $is_collection
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order whereIsCollection($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order whereIsTableService($value)
 * @property int $table_number
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order whereTableNumber($value)
 */
class Order extends Model
{
    use HelperTrait;
    use TrackChanges;

    protected const PURCHASED = 'purchased';

    protected const INCOMPLETE = 'incomplete';

    protected const READY_TO_BUY = 'ready-to-buy';

    protected const PAYMENT_REJECTED = 'payment-rejected';

    protected const PROCESSING = 'processing';

    protected const REJECTED = 'rejected';

    protected const UNACKNOWLEDGED = 'unacknowledged';

    protected const ACKNOWLEDGED = 'acknowledged';

    protected const FULFILLED = 'fulfilled';

    protected const FRONT_UNKNOWN = 'Unknown';

    protected const FRONT_NEWORDER = 'New Order';

    protected const FRONT_PROCESSING = 'Processing';

    protected const FRONT_COMPLETED = 'Completed';

    protected const FRONT_REJECTED = 'Rejected';

    public const ORDER_TYPE_COLLECTION = 'collection';

    public const ORDER_TYPE_TABLE_SERVICE = 'table_service';

    public const ORDER_TYPE_DELIVERY = 'delivery';

    public const ORDER_TYPE_UNASSIGNED = 'unassigned';

    public array $frontEndStatusMap = [
        self::FRONT_NEWORDER => [
            self::PURCHASED
        ],
        self::FRONT_PROCESSING => [
            self::PROCESSING,
            self::ACKNOWLEDGED
        ],
        self::FRONT_COMPLETED => [
            self::FULFILLED
        ],
        self::FRONT_REJECTED => [
            self::UNACKNOWLEDGED,
            self::REJECTED
        ]
    ];

    /**
     * Map for available order types
     * @var array|string[]
     */
    public array $orderTypeFields = [
        'is_collection' => self::ORDER_TYPE_COLLECTION,
        'is_table_service' => self::ORDER_TYPE_TABLE_SERVICE,
        'is_delivery' => self::ORDER_TYPE_DELIVERY
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'url_slug',
        'merchant_id',
        'status',
        'is_delivery',
        'is_table_service',
        'is_collection',
        'customer_name',
        'customer_email',
        'customer_address',
        'customer_phone',
        'available_time',
        'rejection_reason',
        'rejection_reason',
        'total_cost',
        'final_cost',
        'delivery_cost',
        'merchant_note',
        'customer_requested_time',
        'order_submitted',
        'payment_id',
        'customer_note',
        'table_number'
    ];

    protected $dates = [
        'order_submitted'
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
        'status'
    ];

    /**
     * @return BelongsTo
     */
    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
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
     * a order has many items
     * @return HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * email reminders being sent out
     * @return HasMany
     */
    public function reminders(): HasMany
    {
        return $this->hasMany(OrderReminder::class);
    }

    /**
     * checks to see if the item is editable
     * @return bool
     */
    public function isEditable(): bool
    {
        return $this->status === self::INCOMPLETE || $this->status === self::READY_TO_BUY;
    }

    /**
     * Gives a payload of valid backend statuses by frontend name
     * Defaults to all if not in map
     *
     * @param $status
     * @return array|string[]
     */
    public function getBackendStatusesFromFrontendSearchStatus(string $status): array
    {
        if (isset($this->frontEndStatusMap[$status])) {
            return $this->frontEndStatusMap[$status];
        }

        return $this->getAllStatuses();
    }

    /**
     * Iterate though status map and return matching key
     * @return string
     */
    public function getNiceFrontendStatus(): string
    {
        foreach ($this->frontEndStatusMap as $frontEndStatus => $backendStatusArray) {
            if (in_array($this->status, $backendStatusArray)) {
                return $frontEndStatus;
            }
        }

        return $this->getUnknownStatus();
    }

    /**
     * this checks if an order is marked as complete
     * @return array|string[]
     */
    public function getOrderCompletedStatuses(): array
    {
        return [
            self::REJECTED,
            self::UNACKNOWLEDGED,
            self::ACKNOWLEDGED,
            self::FULFILLED,
        ];
    }

    public function getSubmittedStatuses(): array
    {
        return [
            self::PURCHASED,
            self::PAYMENT_REJECTED,
            self::PROCESSING,
            self::REJECTED,
            self::UNACKNOWLEDGED,
            self::ACKNOWLEDGED,
            self::FULFILLED,
        ];
    }

    public function getUnknownStatus(): string
    {
        return self::FRONT_UNKNOWN;
    }

    /**
     * returns an array of statuses
     * @return array
     */
    public function getProcessingStatuses(): array
    {
        return [
            self::PAYMENT_REJECTED,
            self::PROCESSING,
            self::REJECTED,
            self::UNACKNOWLEDGED,
            self::ACKNOWLEDGED,
            self::FULFILLED,
        ];
    }

    /**
     * returns the states that an accepted order cant be past
     * @return array|string[]
     */
    public function getAcceptedStatuses(): array
    {
        return  [
            self::REJECTED,
            self::UNACKNOWLEDGED,
            self::ACKNOWLEDGED,
        ];
    }

    /**
     * returns the states that a rejected order cant be past
     * @return array|string[]
     */
    public function getRejectedStatuses(): array
    {
        return  [
            self::REJECTED,
            self::UNACKNOWLEDGED,
            self::ACKNOWLEDGED,
        ];
    }

    /**
     * gets the statuses that a acknowledged order cant be past
     * @return array|string[]
     */
    public function getAcknowledgedStatuses(): array
    {
        return  [
            self::REJECTED,
            self::UNACKNOWLEDGED,
            self::ACKNOWLEDGED,
        ];
    }

    /**
     * @return array|string[]
     */
    public function getFulfilledStatuses(): array
    {
        return [
            self::REJECTED,
            self::FULFILLED,
        ];
    }

    public function getAllStatuses(): array
    {
        return [
            self::PURCHASED,
            self::INCOMPLETE,
            self::READY_TO_BUY,
            self::PAYMENT_REJECTED,
            self::PROCESSING,
            self::REJECTED,
            self::UNACKNOWLEDGED,
            self::ACKNOWLEDGED,
            self::FULFILLED
        ];
    }

    /**
     * @return array|string[]
     */
    public function getFrontendFilterFormattedStatuses(): array
    {
        $statuses = array_keys($this->getFrontendStatusMap());

        return array_combine($statuses, $statuses);
    }

    /**
     * Returns only the front end status keys from the map
     * @return array
     */
    public function getAllFrontendStatuses(): array
    {
        return array_keys($this->getFrontendStatusMap());
    }

    /**
     * Returns the whole status map
     * @return array|\string[][]
     */
    public function getFrontendStatusMap(): array
    {
        return $this->frontEndStatusMap;
    }

    /**
     * returns whether its delivery or collection
     * @return string
     */
    public function getIsDeliveryOrCollection(): string
    {
        return $this->is_delivery ? 'Delivery' : 'Collection';
    }

    /**
     * checks if the current order is in a complete state
     */
    public function isOrderCompleted()
    {
        return in_array($this->status, $this->getOrderCompletedStatuses());
    }

    /**
     * gets the time since the order was created and if its older than 20 minutes
     *
     * @return array
     */
    public function getTimeSinceCreatedAndIfTheOrderIsOlderThan20Minutes(): array
    {
        $orderSubmitted = $this->order_submitted;
        $orderSubmitted = Carbon::parse($orderSubmitted);
        $now = Carbon::now();
        $difference = $now->diff($orderSubmitted);

        $timeSinceCreated = [];

        if ((int)$difference->format('%I') >= 20) {
            $timeSinceCreated['old'] = true;
        } else {
            $timeSinceCreated['old'] = false;
        }

        $timeSinceCreated['time_to_display'] = $this->generateTimeToDisplay($difference);

        $timeSinceCreated['time'] = $difference->format('%I:%S');

        return $timeSinceCreated;
    }

    /**
     * sets the timezone on the attribute
     *
     * @param $value
     *
     * @return Carbon
     */
    public function getOrderSubmittedAttribute($value)
    {
        return Carbon::parse($value)->setTimezone('Europe/London');
    }

    /**
     * returns the type of order
     * @return string
     */
    public function orderType(): string
    {
        foreach ($this->orderTypeFields as $field => $handle) {
            if ($this->{$field} === 1) {
                return $handle;
            }
        }
        return self::ORDER_TYPE_UNASSIGNED;
    }

    /**
     * Scope a query to only include a merchants orders that are processing.
     *
     * @param Builder $query
     * @param string $status
     *
     * @return Builder
     */
    public function scopeStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * @return int
     */
    public function getOrderTotalForPaymentProvider(): int
    {
        if ($this->getIsDeliveryOrCollection() === 'Delivery') {
            return $this->total_cost + $this->delivery_cost;
        }

        return $this->total_cost;
    }

    /**
     * checks if a order has already had some payment details provided
     * @return bool
     */
    public function hasPaymentDetails(): bool
    {
        return $this->payment_id !== null;
    }

    /**
     * @return bool
     */
    public function isTableService(): bool
    {
        return (bool) $this->is_table_service;
    }

    /**
     * @return void
     */
    public function setTableService(): void
    {
        $this->is_table_service = 1;
    }

    /**
     * @param \DateInterval $dateInterval
     * @return string
     */
    protected function generateTimeToDisplay(\DateInterval $dateInterval): string
    {
        if ($dateInterval->h >= 1) {
            return $dateInterval->format('%h Hour %I');
        }
        return $dateInterval->format('%I');
    }
}
