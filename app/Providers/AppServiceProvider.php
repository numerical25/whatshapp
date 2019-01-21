<?php

namespace App\Providers;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Http\Resources\JsonApiErrorResource;

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
            return new \App\Article();
        });
        //Returns a Singleton Model for Venue
        $this->app->singleton('venue',function(){
            return new \App\Venue();
        });
        //Returns a Singleton Model for Venue
        $this->app->singleton('event',function(){
            return new \App\Event();
        });
    }

    public function run() {
        $result = null;
        $resource = Input::get('resource');
        $id = Input::get('id');
        $action = Input::get('action');
        $action = $this->camelize($action);
        $parameters = Input::all();
        $data = Input::all();
        $method = Input::method();
        $xdebug  = ini_get('xdebug.profiler_enable');
        try {
            throw new \Exception("Issue on the server");
            $model = null;
            if($resource) {
                $model = app($resource);
            }
            if($method == 'GET') {
                if($action && !method_exists($model,$action)) {
                    throw new \Exception("No such action availible.");
                }
                if($action) {
                    return $model->$action($parameters);
                } else {
                    if($id) {
                        $result = $model::find($id);
                        return $result;
                    }
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
            return new JsonApiErrorResource($e);
        }
        return $result;

    }

    function camelize($input, $separator = '-')
    {
        return lcfirst(str_replace($separator, '', ucwords($input, $separator)));
    }
}
