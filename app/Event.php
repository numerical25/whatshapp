<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use DB;
class Event extends Model
{
    //Measures Locations Distance by Kilometers or Miles
    const KILOMETERS = 6371;
    const MILES = 3959;

    function getTrendingEvents($args = []) {
        if(isset($args['units']) && $args['units'] == 'km') {
            $distanceUnits = self::KILOMETERS;
        } else {
            $distanceUnits = self::MILES;
        }
        if(!isset($args['latitude']) ||  !isset($args['longitude'])) {
            throw new \Exception("latitude and longitude values are needed for this actions");
        }
        $latitude = $args['latitude'];
        $longitude = $args['longitude'];
        $distance = 25;
        $now = Carbon::now();
        $todaysDate = Carbon::today();
        $tomorrowsDate = Carbon::tomorrow();
        $events = self::whereDate('start_date',$todaysDate)
                        ->orWhereDate('end_date','=',$todaysDate)
                        ->orWhere(function($query) use ($todaysDate) {
                            $query->whereDate('start_date','<',$todaysDate)
                                    ->whereDate('end_date','>',$todaysDate);
                        })
                        ->whereHas('venue', function($query) 
                        use ($latitude, $longitude, $distance, $distanceUnits ) {
                            $selectQuery =  "(
                                $distanceUnits * 
                                acos(
                                    cos(radians($latitude)) * 
                                    cos(radians(latitude)) *
                                    cos(radians(longitude) - radians($longitude)
                                    ) +
                                    sin(radians($latitude)) *
                                    sin(radians(latitude))
                                )
                            ) `distance`";
                            $selectQuery = preg_replace('/\s+/', ' ', $selectQuery);
                            $query->select([
                                'id',
                                'name',
                                 DB::raw($selectQuery)
                            ])->having("distance","<", $distance);
                        })->with('venue')->get();      
        return new JsonResponse($events,200);
    }

    public function venue()
    {
        return $this->belongsTo('App\Venue');
    }
}
