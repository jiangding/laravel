<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Csrecord;

class CsController extends Controller
{

    /**
     * 客服列表首页
     */
    public function index(Request $request){

        // 搜索
        $nickname = $request->input('nickname');

        $csWhere = Csrecord::orderBy('id', 'desc');
        echo $nickname;
        if($nickname){
            $csRecord = $csWhere->with(['user'=>function ($q) use ($nickname) {
                $q->where('nickname', 'LIKE', '%'.$nickname.'%');
            }])->with('kf')->where('mode',0)->paginate(15);
        }else{
            $csRecord = $csWhere->with('user')->with('kf')->paginate(15);
        }
        //return $csRecord;
        return view('backend.cs-index', compact('csRecord'));
    }

    /**
     * 获取客服聊天记录
     * @param Request $request
     * @param $adminid
     * @return v
     */
    public function getLogs(Request $request, $adminid)
    {
        $appid = 'gh_d30a13af0bc7';
        $useropenid = $request->input('id');
        $page = $request->input('page');

        if ($request->ajax())
        {
            $chatlogs = Csrecord::with('user')
                ->with('kf')
                ->where('appid',$appid)
//                ->where('csopenid', $adminid)
                ->where('useropenid',$useropenid)
                ->orderBy('created_at','desc')
                ->paginate(20);
            $arr = array();
            foreach ($chatlogs->items() as $item)
            {
                array_push($arr,(object)[
                    'username' => $item->mode?$item->kf->name:$item->user->nickname,
                    'id' => $item->mode?$item->csopenid:$item->useropenid,
                    'avatar' => $item->mode?$item->kf->avator:$item->user->avatar,
                    'timestamp' => $item->created_at->format('U')*1000,
                    'content' => $item->record
                ]);
            }
            $response = [
                'total' => $chatlogs->total(),
                'data'    => $arr,
                'code'  => 0
            ];
            return response()->json($response);
        }
        else
        {
            return view("backend.cservice-index",compact('currentUser', 'appid','useropenid', 'adminid'));
        }
    }

}
