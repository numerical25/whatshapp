<?php

use Illuminate\Database\Seeder;

class EventTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $numOfEvents = 3;
        $data = [
            'venue_id'=>1,
            'start_date'=>now(),
            'end_date'=>now()
        ];
        factory(App\Event::class,$numOfEvents)->create($data)
        ->each(function ($event) use ($numOfEvents) {
            $event->venue_id = rand(1,$numOfEvents);
            $event->save();
        });
    }
}
