<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Otp extends Model
{
    protected $fillable = ['code', 'expiry_date', 'active', 'initiator_id'];

    protected $dates = ['expiry_date'];

    protected $dateFormat = 'Y-m-d H:i:s';

    protected $casts = [
        'initiator_id' => 'string',
        'code' => 'int'
    ];

    public function generate($initiatorId)
    {
        return $this->create([
            'initiator_id' => $initiatorId,
            'code' => mt_rand(100000, 300000),
            'expiry_date' => Carbon::now()->addMinutes(10),
        ]);
    }

    public function hasExpired() : bool
    {
        return $this->expiry_date->lt(Carbon::now());
    }

    public function hasBeenValidated() : bool
    {
        return $this->active == true;
    }

    public function invalidate()
    {
        return $this->update(['active' => true]);
    }

    public function isInitiatorAuthorized($initiator) : bool
    {
        return $this->initiator_id === $initiator;
    }
}