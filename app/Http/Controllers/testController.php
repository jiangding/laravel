<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cache, Queue;
use App\Http\Requests;
use Carbon\Carbon;

class testController extends Controller
{
    //

    public function test()
    {

        return view('welcome');
    }
}
