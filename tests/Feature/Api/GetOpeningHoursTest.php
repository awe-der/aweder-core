<?php

namespace Tests\Feature\Api;

use App\Contract\Repositories\NormalOpeningHoursContract;
use App\NormalOpeningHour;
use Illuminate\Http\Response;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Class GetOpeningHoursTest
 * @package Tests\Feature\Api
 * @group Order
 */
class GetOpeningHoursTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @test
     */
    public function can_view_opening_hours(): void
    {
        $merchant = $this->createAndReturnMerchant();

        factory(NormalOpeningHour::class)->create(
            [
                'merchant_id' => $merchant->id,
                'day_of_week' => 2,
                'open_time' => '10:00',
                'close_time' => '21:00',
                'is_delivery_hours' => 1
            ]
        );

        factory(NormalOpeningHour::class)->create(
            [
                'merchant_id' => $merchant->id,
                'day_of_week' => 3,
                'open_time' => '11:00',
                'close_time' => '21:00',
                'is_delivery_hours' => 1
            ]
        );

        factory(NormalOpeningHour::class)->create(
            [
                'merchant_id' => $merchant->id,
                'day_of_week' => 4,
                'open_time' => '12:00',
                'close_time' => '21:00',
                'is_delivery_hours' => 0
            ]
        );

        $response = $this->json(
            'GET',
            'api/v1/merchant/' . $merchant->url_slug . '/openinghours?type=business_hours'
        );

        $response->assertStatus(Response::HTTP_OK);

        $response->assertJsonFragment([
            'day_of_week' => 2,
            'open_time' => '10:00',
            'is_delivery_hours' => 1
        ]);

        $response->assertJsonFragment([
            'day_of_week' => 3,
            'open_time' => '11:00',
            'is_delivery_hours' => 1
        ]);

        $response->assertJsonMissing([
            'is_delivery_hours' => 0
        ]);
    }

    /**
     * @test
     */
    public function can_get_default_opening_hours(): void
    {
        $merchant = $this->createAndReturnMerchant();

        $normalOpeningHoursRepository = $this->app->make(NormalOpeningHoursContract::class);
        $normalOpeningHoursRepository->createDefaultOpeningHoursForMerchant($merchant->id);

        $response = $this->json(
            'GET',
            'api/v1/merchant/' . $merchant->url_slug . '/openinghours'
        );

        $response->assertStatus(Response::HTTP_OK);

        for ($i = 1; $i > 7; $i++) {
            $response->assertJsonFragment([
                'day_of_week' => $i,
                'open_time' => '9:00',
                'close_time' => '17:00',
                'is_delivery_hours' => 1
            ]);
        }
    }

    /**
     * @test
     */
    public function can_view_table_service_hours(): void
    {
        $merchant = $this->createAndReturnMerchant();

        factory(NormalOpeningHour::class)->create(
            [
                'merchant_id' => $merchant->id,
                'day_of_week' => 2,
                'open_time' => '10:00',
                'close_time' => '21:00',
                'is_delivery_hours' => 1
            ]
        );

        factory(NormalOpeningHour::class)->create(
            [
                'merchant_id' => $merchant->id,
                'day_of_week' => 3,
                'open_time' => '10:00',
                'close_time' => '21:00',
                'is_delivery_hours' => 1
            ]
        );

        factory(NormalOpeningHour::class)->create(
            [
                'merchant_id' => $merchant->id,
                'day_of_week' => 4,
                'open_time' => '10:00',
                'close_time' => '21:00',
                'is_delivery_hours' => 0
            ]
        );

        $response = $this->json(
            'GET',
            'api/v1/merchant/' . $merchant->url_slug . '/openinghours?type=table_service'
        );

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment(['day_of_week' => 4]);
        $response->assertJsonMissing(['day_of_week' => 3]);
        $response->assertJsonMissing(['day_of_week' => 2]);
    }
}
