<?php

namespace App\Http\Controllers;

use App\Otp;
use Illuminate\Http\Request;
use App\Traits\HasJsonResponse;

class OtpController extends Controller
{
    use HasJsonResponse;

    public function generate(Request $request, Otp $otp)
    {
        $this->validate($request, [
            'initiator_id' => 'required|alpha_num|max:200'
        ]);

        $initiatorId = $request->input('initiator_id');

        $otp = $otp->generate($initiatorId);

        $result = [
            'status' => true,
            'initiator_id' => $otp->initiator_id,
            'code' => $otp->code,
            'expires_in' => $otp->expiry_date
        ];

        return response()->json($result, 201);
    }

    public function validateOtp($code, $initiator)
    {
        $otp = Otp::whereCode($code)->first();

        if(! $otp){
            return response()->json($this->invalidOtp(), 200);
        }

        if(! $otp->isInitiatorAuthorized($initiator)){
            return response()->json($this->unauthorizedInitiator(), 200);
        }

        if($otp->hasBeenValidated()){
            return response()->json($this->validatedOtp(), 200);
        }

        if($otp->hasExpired()){
            return response()->json($this->expiredOtp(), 200);
        }

        $otp->invalidate();

        return response()->json($this->validOtp(), 200);
    }

}