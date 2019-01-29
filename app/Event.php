<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use DB;
use App\Http\Resources\JsonApiCollectionResource;
use App\Http\Resources\JsonApiResource;
use App\User;

class Event extends Model
{
    //Measures Locations Distance by Kilometers or Miles
    const KILOMETERS = 6371;
    const MILES = 3959;

    const CHECK_IN_DISTANCE = .25;

    function getTrendingEvents($args = null) {
        try {
            return $this->_getTrendingEvents($args);
        } catch(\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    function _getTrendingEvents($args = null) {
        if(isset($args->units) && $args->units == 'km') {
            $distanceUnits = self::KILOMETERS;
        } else {
            $distanceUnits = self::MILES;
        }
        if(!isset($args->latitude) ||  !isset($args->longitude)) {
            throw new \Exception("latitude and longitude values are needed for this actions");
        }
        $latitude = $args->latitude;
        $longitude = $args->longitude;
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
        
        return new JsonApiCollectionResource($events);
    }


    public function checkIn($args = null) {
        if(!(isset($args) && isset($args->from_lat) && isset($args->from_long) && 
            isset($args->to_lat) && isset($args->to_long) && isset($args->user_id) && 
            isset($args->event_id))) {
            throw new \Exception("Please Provide all data for checking in.");
        }

        $distance = round($this->distance($args->from_lat, $args->from_long, 
                                    $args->to_lat, $args->to_long, self::MILES),2);
        if($distance <= self::CHECK_IN_DISTANCE) {
            $user = new User();
            $user->where('id',$args->user_id)->update([
                'event_check_in_id'=>$args->event_id
            ]);
            $users = $user->get();
            $attributes = [
                'id'=>"200",
                'message'=>'You are now checked in.'
            ];
        } else {
            $attributes = [
                'id'=>"200",
                'message'=>'You are not close enough to the event to check in.'
            ];
        }
        return new JsonApiResource(new CheckIn($attributes));
    }

    /**
     * Calculates the great-circle distance between two points, with
     * the Haversine formula.
     * @param float $latitudeFrom Latitude of start point in [deg decimal]
     * @param float $longitudeFrom Longitude of start point in [deg decimal]
     * @param float $latitudeTo Latitude of target point in [deg decimal]
     * @param float $longitudeTo Longitude of target point in [deg decimal]
     * @param float $earthRadius Mean earth radius in [m]
     * @return float Distance between points in [m] (same as earthRadius)
     */
    function distance(
    $latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371)
    {
        // convert from degrees to radians
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * $earthRadius;
    }

    public function venue()
    {
        return $this->belongsTo('App\Venue');
    }
}
