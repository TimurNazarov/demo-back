<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;
use App\Helpers\Helpers;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker) {
	static $i = 0;
	$i++;
    return [
        'name' => $faker->name,
        'email' => $i,
        'email_verified_at' => now(),
        'password' => bcrypt($i), // i
        'profile_picture_path' => 'profile/' . $i . '.jpg'
        // 'remember_token' => Str::random(10),
    ];
});
