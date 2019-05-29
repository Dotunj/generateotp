<?php

namespace App\Traits;

trait HasJsonResponse
{
    private function invalidOtp()
    {
        return [
            'status' => false,
            'message' => 'Invalid Otp'
        ];
    }

    private function expiredOtp()
    {
        return [
            'status' => false,
            'message' => 'Otp has expired'
        ];
    }

    private function validatedOtp()
    {
        return [
            'status' => false,
            'message' => 'Otp has been used already'
        ];
    }

    private function validOtp()
    {
        return [
            'status' => true,
            'message' => 'Otp has been verified'
        ];
    }

    private function unauthorizedInitiator()
    {
        return [
            'status' => false,
            'message' => 'Not Authorized to use this otp'
        ];
    }
}