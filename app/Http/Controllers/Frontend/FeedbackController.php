<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Models\Feedback;

class FeedbackController extends Controller
{
    /**
     * 提交反馈首页
     */
    public function index(Request $request)
    {
        $currentUser = $this->currentUser;
        if ($request->ajax())
        {
            try {
                Feedback::create($request->all());
                $response = [
                    'message' => 'Feedback updated.',
                    'data'    => '',
                    'retcode' => 0
                ];
            } catch (ValidatorException $e) {
                return response()->json([
                    'retcode'   => 1,
                    'message' => $e->getMessageBag()
                ]);
            }
            return response()->json($response);
        }
        else
        {
            $title = '提交反馈';
            return view('frontend.Feedback.index', compact('title','currentUser'));
        }
    }
}
