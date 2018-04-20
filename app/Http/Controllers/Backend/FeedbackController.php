<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\Feedback;

class FeedbackController extends Controller
{

    protected $request;
    /**
     * Create a new controller instance.
     *
    */
    public function __construct(Request $request)
    {
        $this->request = $request;

        parent::__construct();

    }

    public function index(){

        $data = Feedback::orderBy('id','desc')->with(['user'])->paginate(15);

        return view('backend.feedback', [ 'data' => $data ]);
    }

}
