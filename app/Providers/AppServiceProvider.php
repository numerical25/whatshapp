<?php

namespace App\Providers;

use App\Article;
use App\Venue;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        Schema::defaultStringLength(191);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

        //Returns The API App
        $this->app->singleton('queryBuilderApi',function(){
            return $this;
        });


        //Returns a Singleton Model for Article
        $this->app->singleton('article',function(){
            return new Article();
        });
        //Returns a Singleton Model for Venue
        $this->app->singleton('venue',function(){
            return new Venue();
        });
    }

    public function run() {
        $result = null;
        $resource = Input::get('resource');
        $id = Input::get('id');
        $data = Input::all();
        $method = Input::method();
        $xdebug  = ini_get('xdebug.profiler_enable');
        try {
            $model = null;
            if($resource) {
                $model = app($resource);
            }
            if($method == 'GET') {
                if($id) {
                    $result = $model::find($id);
                    return $result;
                }
                $result = $model::orderBy('updated_at', 'DESC')->get();
            }
            if($method == 'PUT') {
                $instance = $model::find($id);
                if($instance->update($data)) {
                    $result = $instance;
                }
            }
            if($method == 'POST') {
                $instance = new $model($data);
                if($instance->save()) {
                    $result = $instance;
                }
            }
        } catch (\Exception $e) {
            $error = $e;
        }
        return $result;

    }
}
