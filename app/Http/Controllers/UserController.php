<?php
/**
 * Created by PhpStorm.
 * User: anthonygordon
 * Date: 3/28/18
 * Time: 5:23 PM
 */

namespace App\Http\Controllers;

use App\User;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * Show the profile for the given user.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        echo "test";
        //return view('user.profile', ['user' => User::findOrFail($id)]);
    }
}