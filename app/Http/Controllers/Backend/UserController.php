<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\http\Requests\UserRequest;
use App\Http\Requests;
use App\Models\User;
use App\Models\Stock;
use App\Models\Address;
use Illuminate\Support\Facades\Hash;
use Validator;
use App\Models\components\cusResponse;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 用户主页
     */

    public function index(Request $request){

        $nickname = $request->input('nickname');

        $userWhere = User::orderBy('id','desc');
        // 产品名
        if($nickname){
            $userWhere->where('nickname', 'like', "%".$nickname."%");
        }
        $data = $userWhere->paginate(500);

        // 分页追加参数
        $appends = [
            'nickname' => $nickname
        ];

        return view('backend.user', [
            'data' => $data ,
            'appends' => $appends
        ]);
    }


    /**
     * 用户edit 信息
     */

    public function edit($id){

        $row = User::find($id);
        //dd($row);
        return view('backend.user-edit', [ 'json' => $row ]);
    }

    /**
     *  用户执行更新
     */
    public function update(Request $request){
        // 校验数据
        $validator = Validator::make($request->all(), [
            'email'		=>	'email',
            'selfcode'	=>	'string',
            'name'		=>	'string|required',
            'nickname'	=>	'string',
            'country'	=>	'string',
            'constellation'	=>	'string',
            'city'		=>	'string',
            'avatar'	=>	'string',
            'age'		=>	'integer',
            'remind'	=>	'integer|in:5,7,10,30|',
        ]);

        $cusResponse = new cusResponse();
        // 验证失败
        if ($validator->fails()) {
            // 返回jason
            $cusResponse->status = 5;
            $cusResponse->message = $validator->errors()->first();
            return $cusResponse->toJson();
        }

        $id = $request->input('id');

        $affect = User::find($id)->update($request->input());

        if($affect){
            $cusResponse->status = 200;
            $cusResponse->message = "success";
        }else{
            $cusResponse->status = 500;
            $cusResponse->message = "未获取到服务器数据";
        }

        return $cusResponse->toJson();
    }

    /**
     *  用户 stock 列表
     */
    public function stockList($userid){

        // 用户
        $userRow = User::find($userid);

        // 库存数据
        $data =  Stock::where('userid', $userid)->with('product')->get();

//        dd($data);

        return view('backend.user-stock-list', [ 'data' => $data, 'userRow' => $userRow]);
    }


    /**
     *  用户 地址本
     */
    public function addressList($userid){

        // 用户
        $userRow = User::find($userid);

        // 地址本数据
        $data =  Address::where('userid', $userid)->get();

        return view('backend.user-address-list', [ 'data' => $data, 'userRow' => $userRow]);
    }

}
