<?php

use Faker\Generator as Faker;

$factory->define(App\Comment::class, function (Faker $faker) {
    return [
        //
        'user_id'=>1,
        'event_id'=>1,
        'message'=>$faker->paragraph(4,true)
    ];
});
