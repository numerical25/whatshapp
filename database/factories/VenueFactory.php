<?php

use Faker\Generator as Faker;

$factory->define(App\Venue::class, function (Faker $faker) {
    return [
        'name'=>$faker->title,
        'address'=>$faker->streetAddress,
        'city'=>$faker->city,
        'state'=>$faker->stateAbbr,
        'zip'=>$faker->postcode,
        'latitude'=>39.942440,
        'longitude'=>-82.828950
    ];
});
