<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Models\components\cusResponse;
use App\Models\Admin;
use Validator;
use Illuminate\Support\Facades\Log;
use Cache;
use App\Http\Traits\CustomerServiceTrait;

class HomeController extends Controller
{
    use CustomerServiceTrait;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    public function login()
    {

        return view('backend.auth.login');
    }


    /**
     * 执行登录
     *
     * @param Request $request
     * @return string
     */
    public function doLogin(Request $request)
    {
        // 校验数据
        $validator = Validator::make($request->all(), [
            'username'	=>	'required',
            'password'	=>	'required',
        ]);

        $cusResponse = new cusResponse();
        // 验证失败
        if ($validator->fails()) {
            // 返回jason
            $cusResponse->status = 5;
            $cusResponse->message = $validator->errors()->first();
            return $cusResponse->toJson();
        }

        // 查找
        $username = $request->input("username");
        $pwd = md5($request->input("password"));

        $user = Admin::where(['name'=>$username,'password'=>$pwd])->first();
        if($user){
            $cusResponse->status = 200;

            // 设置session
            $request->session()->put('gh_admin', $user);
        }else{
            $cusResponse->status = 404;
            $cusResponse->message = "用户或密码错误!";
        }
        return $cusResponse->toJson();

    }

    /**
     *  退出
     */
    public function logout(Request $request){

        $request->session()->clear();

        return redirect()->guest('admin/login');
    }

    /**
     * Show the application dashboard.
     *
     */
    public function index(Request $request){

        $appid = env('WECHAT_ORIGINALID','gh_d30a13af0bc7');

        $arr = Admin::all()->take(5);
        foreach($arr as $k=>$v){
                $pArr[$v['id']] = array(
                    'name' => $v['name'],
                    'avatar' => $v['avator'],
                    'status' => (int)Cache::get('PUSHSTATUS_'.$appid.'_'.$v['id']),
                );
        }
        return view('backend.home', ['admins'=>$pArr]);
    }

}
