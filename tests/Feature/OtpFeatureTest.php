<?php

use App\Otp;
use Carbon\Carbon;
use App\Traits\HasJsonResponse;
use Laravel\Lumen\Testing\DatabaseMigrations;

class OtpFeatureTest extends TestCase
{
    use DatabaseMigrations, HasJsonResponse;

    /** @test */
    public function it_can_generate_an_otp_code()
    {
        $attributes = [
            'initiator_id' => 12345,
        ];

        $this->json('POST', '/generate', $attributes)
             ->seeJsonStructure([
                 'status',
                'initiator_id',
                'code',
                'expires_in',
             ]);

        $this->seeInDatabase('otps', $attributes);
    }

    /** @test */
    public function it_returns_the_correct_response_for_an_invalid_otp_code()
    {
        $attributes = [
            'code' => 154637,
            'initiator' => 123456,
        ];

        $this->json('POST', route('validate.otp', $attributes))
             ->seeJson($this->invalidOtp());
    }

    /** @test */
    public function it_returns_the_correct_response_for_a_valid_otp_code()
    {
        $initiatorId = 123456;

        $otp = factory(Otp::class)->create(['initiator_id' => $initiatorId]);

        $attributes = [
            'code' => $otp->code,
            'initiator' => $initiatorId,
        ];

        $this->json('POST', route('validate.otp', $attributes))
             ->seeJson($this->validOtp());

        $this->assertTrue($otp->fresh()->hasBeenValidated());
    }

    /** @test */
    public function it_returns_the_correct_response_for_an_unauthorized_initiator()
    {
        $otp = factory(Otp::class)->create();

        $attributes = [
            'code' => $otp->code,
            'initiator' => 123456,
        ];

        $this->json('POST', route('validate.otp', $attributes))
             ->seeJson($this->unauthorizedInitiator());
    }

    /** @test */
    public function it_returns_the_correct_response_for_an_otp_code_that_has_been_validated()
    {
        $otp = factory(Otp::class)->create(['active' => true]);

        $attributes = [
            'code' => $otp->code,
            'initiator' => $otp->initiator_id,
        ];

        $this->json('POST', route('validate.otp', $attributes))
             ->seeJson($this->validatedOtp());
    }

    /** @test */
    public function it_returns_the_correct_response_for_an_otp_code_that_has_expired()
    {
        $otp = factory(Otp::class)->create();

        $attributes = [
            'code' => $otp->code,
            'initiator' => $otp->initiator_id,
        ];

        Carbon::setTestNow(Carbon::now()->addMinutes(11));

        $this->json('POST', route('validate.otp', $attributes))
             ->seeJson($this->expiredOtp());
    }
}
