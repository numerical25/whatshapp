<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class QueryController extends Controller
{
    //

    /**
     * Entry Point for Query Building
     *
     * @param  array  $params
     * @return Response
     */
    public function index()
    {
        $data = app('queryBuilderApi')->run();
        return $data;
    }
}
