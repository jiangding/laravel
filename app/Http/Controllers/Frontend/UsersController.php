<?php

namespace App\Http\Controllers\Frontend;

use Cache;
use Log;
use Illuminate\Http\Request;
use Validator;
use SmsManager;
use App\Models\User;
use Carbon\Carbon;



class UsersController extends Controller
{
    /**
     * 首页
     */
    public function index(Request $request)
    {
        $currentUser = $this->currentUser;
        $title = '个人设置';
        return view('frontend.Users.index', compact('currentUser','title'));
    }

    public function info(Request $request)
    {
        $currentUser = $this->currentUser;
        $title = '个人资料';
        return view('frontend.Users.info', compact('currentUser','title'));
    }

    /**
     * 注册手机号
     */
    public function register(Request $request)
    {
        $currentUser = $this->currentUser;
        if ($request->ajax())
        {
            try {
//                $validator = Validator::make($request->all(), [
//                    'mobile'     => 'required|confirm_mobile_not_change|confirm_rule:mobile_required',
//                    'verifyCode' => 'required|verify_code',
//                ]);
//                if ($validator->fails()) {
//                    SmsManager::forgetState();
//                    $messages = $validator->errors();
//                    $msgKeys = $messages->keys();
//                    foreach ($msgKeys as $msgKey) {
//                        $msgError[] =
//                            array(
//                                'name' => $msgKey,
//                                'status' => $messages->get($msgKey)
//                            );
//                    }
//                    $response = [
//                        'retcode' => 1,
//                        'fieldErrors' => $msgError
//                    ];
//                }
                // 获取验证码
                $vcode = Cache::get('CURUSERID_'.$currentUser['id']);
                $verifyCode = $request->input('verifyCode');
                $mobile = $request->input('mobile');
                if($vcode == $verifyCode) {
                    $id = $currentUser['id'];
                    $user = User::find($id);
                    $user->mobile = $mobile;
                    $user->save();
                    $response = [
                        'message' => 'Users updated.',
                        'fieldErrors'    => '',
                        'retcode' => 0
                    ];
                }
                else
                {
                    $response = [
                        'fieldErrors'    => 'code error',
                        'retcode' => 1
                    ];
                }

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
            $title = '个人资料';
            return view('frontend.Users.register', compact('currentUser','title'));
        }
    }

    /**
     * 发送短信
     */
    public function sms(Request $request)
    {
        $curUser = $this->currentUser;
        $varcode = rand(1111,9999);
        $url="http://service.winic.org:8009/sys_port/gateway/index.asp?";
        $data = "id=%s&pwd=%s&to=%s&content=%s&time=";
        $id = 'Jason贼神';
        $id = iconv("UTF-8","GB2312",$id);
        $pwd = 'ganghao2016';
        $to = $request->input('mobile');
        $content = iconv("UTF-8","GB2312","【刚好】您好，您的验证码是 ". $varcode);
        $rdata = sprintf($data, $id, $pwd, $to, $content);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$rdata);
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_exec($ch);
        curl_close($ch);

        // 保存到缓存中
        $extime = Carbon::now()->addMinutes(10);
        Cache::put('CURUSERID_'.$curUser['id'], $varcode, $extime);

    }

    public function profile(Request $request)
    {
        $currentUser = $this->currentUser;
        if ($request->ajax())
        {
            try {
                $id = $currentUser['id'];
                $profile = $this->toArray(json_decode($request->input('profile')));
                $this->UsersValidator->with($profile)->passesOrFail(ValidatorInterface::RULE_UPDATE);
                $user = $this->UsersRepository->update($profile,$id);
                $response = [
                    'message' => 'Users updated.',
                    'data'    => $user->toArray(),
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
            return $this->index();
        }
    }

    /**
     * 提交反馈
     */
    public function remind(Request $request)
    {
        $currentUser = $this->currentUser;
        if ($request->ajax())
        {
            try {
                $openid = $currentUser['openid'];
                $user = User::where('openid',$openid)->first();
                $remind = (int)$request->input('remind');
                $user->remind = $remind;
                $user->save();
                $response = [
                    'message' => 'Users updated.',
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
            return $this->index();
        }
    }

    public function feedback(Request $request)
    {
        $currentUser = $this->currentUser;
        if ($request->ajax())
        {
            try {
                $this->FeedbackValidator->with($request->all())->passesOrFail(ValidatorInterface::RULE_CREATE);
                $user = $this->FeedbackRepository->create($request->all());
                $response = [
                    'message' => 'Feedback updated.',
                    'data'    => $user->toArray(),
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
            return view('Frontend.Users.feedback', compact('title','currentUser'));
        }
    }
}
