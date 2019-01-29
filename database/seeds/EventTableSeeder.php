<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
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
        $i = 0;
        $start_date = Carbon::now();
        $end_date = Carbon::now();
        $data = [
            [
                'venue_id'=>1,
                'start_date'=>$start_date,
                'end_date'=>$end_date->addDay()
            ]
        ];
        factory(App\Event::class,$numOfEvents)->create($data[$i])
        ->each(function ($event) use ($numOfEvents, $data, $i) {
            if(isset($data[$i])) {
                $event->start_date = $data[$i]['start_date'];
                $event->end_date = $data[$i]['end_date'];
            }
            $event->venue_id = rand(1,$numOfEvents);
            $event->save();
            $i++;
        });
    }
}
