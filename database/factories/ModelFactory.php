<?php

use Carbon\Carbon;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->email,
    ];
});

$factory->define(App\Otp::class, function (Faker\Generator $faker) {
    return [
        'initiator_id' => str_random(),
        'code' => mt_rand(100000, 300000),
        'expiry_date' => Carbon::now()->addMinutes(10),
    ];
});
