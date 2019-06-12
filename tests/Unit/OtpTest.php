<?php

use App\Otp;
use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;

class OtpTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_can_create_an_otp()
    {
        factory(Otp::class)->create(['code' => 12345]);

        $otp = Otp::first();

        $this->assertEquals(12345, $otp->code);

        $this->seeInDatabase('otps', ['code' => 12345]);
    }

    /** @test */
    public function it_can_determine_when_an_otp_has_expired()
    {
        $otp = factory(Otp::class)->create();

        $this->assertFalse($otp->hasExpired());

        Carbon::setTestNow($this->addMinutes());

        $this->assertTrue($otp->hasExpired());
    }

    /** @test */
    public function it_can_determine_whether_an_otp_has_been_used_or_not()
    {
        $otp = factory(Otp::class)->create();

        $this->assertFalse($otp->hasBeenValidated());

        $otp->update(['active' => true]);

        $this->assertTrue($otp->hasBeenValidated());
    }

    /** @test */
    public function it_can_invalidate_an_otp_code()
    {
        $otp = factory(Otp::class)->create();

        $otp->invalidate();

        $this->assertTrue($otp->hasBeenValidated());
    }

    /** @test */
    public function it_can_determine_the_initiator_for_an_otp_code()
    {
        $randomCode = str_random();

        $otp = factory(Otp::class)->create(['initiator_id' => $randomCode]);

        $this->assertTrue($otp->isInitiatorAuthorized($randomCode));

        $this->assertFalse($otp->isInitiatorAuthorized(str_random()));
    }

    private function addMinutes()
    {
        return Carbon::now()->addMinutes(11);
    }
}
