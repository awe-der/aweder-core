<?php

namespace App\Traits;

use Carbon\Carbon;

trait HelperTrait
{
    /**
     * @param int $length
     * @return string
     */
    public function generateSlugUrl(int $length): string
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ12345689_-';

        $my_string = '';

        for ($i = 0; $i < $length; $i++) {
            $pos = mt_rand(0, (strlen($chars) - 1));

            $my_string .= substr($chars, $pos, 1);
        }

        return $my_string;
    }

    /**
     * converts a price to pence
     * @param $price
     * @return int
     */
    public function convertToPence($price): int
    {
        return (int) ($price * 100);
    }

    /**
     * returns a formatted version of the date/time suitable for emails
     *
     * @param $time
     *
     * @return string
     */
    public function getFormattedDateForEmail($time): string
    {
        $date = Carbon::parse($time);

        return $date->format('g:i a');
    }

    /**
     * returns a formatted version of delivery time for order details form
     *
     * @param $time
     *
     * @return string
     */
    public function getFormattedDeliveryTime($time): string
    {
        $date = Carbon::parse($time);

        return $date->format('H:i');
    }

    /**
     * returns the formatted value of the price
     *
     * @param int $price
     * @param bool|int $deliveryCost
     *
     * @return string
     */
    public function getFormattedUKPriceAttribute($price, $deliveryCost = false): string
    {
        if ($deliveryCost !== false && $deliveryCost > 0) {
            $price += $deliveryCost;
        }

        return number_format(($price / 100), 2);
    }

    /**
     * Used to convert strings to 0 or 1 for database persistance
     *
     * @param string $string
     * @return bool
     */
    public function convertBooleanStringToInteger(string $string): int
    {
        $boolean = filter_var($string, FILTER_VALIDATE_BOOLEAN);
        return (int)$boolean;
    }

    /**
     * @param string $timePeriod
     * @return array
     */
    public function getTimePeriodForOrders(string $timePeriod): array
    {
        switch ($timePeriod) {
            case 'today':
                return [
                    Carbon::now()->startOfDay(),
                    Carbon::now()->endOfDay(),
                ];
            case 'this-week':
                return [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek(),
                ];
            case 'this-month':
                return [
                    Carbon::now()->startOfMonth(),
                    Carbon::now()->endOfMonth(),
                ];
        }
        return [];
    }
}
