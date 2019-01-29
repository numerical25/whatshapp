<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use DB;
use App\Http\Resources\JsonApiCollectionResource;
use App\Http\Resources\JsonApiResource;

class Venue extends Model
{
    protected $fillable = array('name', 'address', 'city','state','zip','longitude','latitude');

    //Measures Locations Distance by Kilometers or Miles
    const KILOMETERS = 6371;
    const MILES = 3959;

    public function getTrendingEvents($args = null) {
        //DB::enableQueryLog();
        $results = $this->_getTrendingEvents($args);
        //dd(DB::getQueryLog());
        return $results;
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
        $venues = $this->select([
            'id',
            'name',
            'latitude',
            'longitude',
            DB::raw($selectQuery)
        ])->having("distance","<", $distance)
        ->whereHas('event',function($query) use ($todaysDate){
            $query->whereDate('start_date',$todaysDate)
                ->orWhereDate('end_date','=',$todaysDate)
                ->orWhere(function($query) use ($todaysDate) {
                    $query->whereDate('start_date','<',$todaysDate)
                            ->whereDate('end_date','>',$todaysDate);
                });
        })
        ->with('event')
        ->get();
        return new JsonApiCollectionResource($venues);
    }

    public function event() {
        return $this->hasMany('App\Event');
    }

}
