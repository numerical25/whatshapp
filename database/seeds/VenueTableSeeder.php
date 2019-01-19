<?php

use Illuminate\Database\Seeder;

class VenueTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */


    public function run()
    {
        $count = 0;
        $coords = [
            [
                'name'=>'The Derby East',
                'state'=>'OH',
                'latitude'=>39.942440,
                'longitude'=>-82.828950
            ],
            [
                'name'=>'Shooters Sports Bar',
                'state'=>'OH',
                'latitude'=>39.952800,
                'longitude'=>-82.829400
            ],
            [
                'name'=>'Putter\'s Pub',
                'state'=>'OH',
                'latitude'=>39.942690,
                'longitude'=>-82.834220
            ],
        ];
        //Adds Fake Data but implements a few real locations for testing coordinates
        factory(App\Venue::class,3)->create()
        ->each(function ($venue) use ($coords,&$count) {
            if(isset($coords[$count])) {
                $coordsData = $coords[$count];
                $venue->name = $coordsData['name'];
                $venue->state = $coordsData['state'];
                $venue->latitude = $coordsData['latitude'];
                $venue->longitude = $coordsData['longitude'];
                $count++;
            }
            $venue->save();
        });
    }
}
