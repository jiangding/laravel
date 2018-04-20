<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;

class SpiderController extends Controller
{
    public function spider(Request $request)
    {
        set_time_limit(0);
        date_default_timezone_set("Asia/shanghai");
        header("Content-type:text/html;charset=UTF-8");

        if($request->ajax()){


        }

    }


}
