<?php

namespace Tests\Feature\Auth\Registration\MultiStep\BusinessDetails;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class ContactDetailsPostControllerTest
 * @package Tests\Feature\Auth\Registration\MultiStep\ContactDetails
 * @coversDefaultClass \App\Http\Controllers\Auth\Registration\MultiStep\ContactDetails\DetailsPost
 * @group MultiStageRegistration
 */
class ContactDetailsPostControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * @test
     */
    public function user_can_post_contact_details(): void
    {
        $user = factory(User::class)->create();
        $merchant = $this->createAndReturnMerchant(['registration_stage' => 3]);
        $user->merchants()->attach($merchant->id);
        $this->actingAs($user);

        $contactDetails = [
            'mobile-number' => $this->faker->phoneNumber,
            'customer-phone-number' => $this->faker->phoneNumber
        ];

        $response = $this->from(route('register.contact-details'))
            ->post(route('register.contact-details.post'), $contactDetails);

        $response->assertRedirect(route('register.business-address'));
    }

    /**
     * @test
     */
    public function user_cannot_advance_without_contact_details(): void
    {
        $user = factory(User::class)->create();
        $merchant = $this->createAndReturnMerchant(['registration_stage' => 3]);
        $user->merchants()->attach($merchant->id);
        $this->actingAs($user);

        $contactDetails = [
            'mobile-number' => '',
            'customer-phone-number' => ''
        ];

        $response = $this->from(route('register.contact-details'))
            ->post(route('register.contact-details.post'), $contactDetails);

        $response->assertRedirect(route('register.contact-details'));
    }

    /**
     * @test
     */
    public function mobile_number_must_not_contain_alphas(): void
    {
        $user = factory(User::class)->create();
        $merchant = $this->createAndReturnMerchant(['registration_stage' => 3]);
        $user->merchants()->attach($merchant->id);
        $this->actingAs($user);

        $contactDetails = [
            'mobile-number' => '0778a2054654',
            'customer-phone-number' => $this->faker->phoneNumber
        ];

        $response = $this->from(route('register.contact-details'))
            ->post(route('register.contact-details.post'), $contactDetails);

        $response->assertRedirect(route('register.contact-details'));
    }

    /**
     * @test
     */
    public function contact_number_must_not_contain_alphas(): void
    {
        $user = factory(User::class)->create();
        $merchant = $this->createAndReturnMerchant(['registration_stage' => 3]);
        $user->merchants()->attach($merchant->id);
        $this->actingAs($user);

        $contactDetails = [
            'mobile-number' => $this->faker->phoneNumber,
            'customer-phone-number' => '0121 4A3 2135'
        ];

        $response = $this->from(route('register.contact-details'))
            ->post(route('register.contact-details.post'), $contactDetails);

        $response->assertRedirect(route('register.contact-details'));
    }
}
