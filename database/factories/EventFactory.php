<?php

use Faker\Generator as Faker;

$factory->define(App\Event::class, function (Faker $faker) {
    return [
        'name'=>$faker->words(3,true),
        'description'=>$faker->paragraph,
    ];
});
