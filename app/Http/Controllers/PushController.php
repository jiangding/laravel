<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Log;
use Cache;
use Queue;
use Carbon\Carbon;
use App\Jobs\Message;
use App\Models\Csrecord;
use App\Models\User;
use App\Http\Traits\CustomerServiceTrait;
use Laravoole\LaravooleFacade;
use Illuminate\Redis\Database;


class PushController extends CommonController
{

    use CustomerServiceTrait;

    protected $redis;

    public function __construct(Database $redis)
    {
        $this->redis = $redis->connection();
        return $this;
    }

    public function _getmessage($appid,$openid)
    {
        $csrecords = Csrecord::where([
            'appid'     =>  $appid,
            'csopenid'  =>  $openid,
            'read'      =>  0,
            'push'      =>  0
        ])->with('user')->get();

        $arr = array();
        $ids = array();
        $count = 0;

        foreach ($csrecords as $csrecord)
        {
            $tmp = [
                'username' => $csrecord->user->nickname,
                'avatar' => $csrecord->user->avatar,
                'id' => $csrecord->useropenid,
                'cid' => $csrecord->id,
                'type' => "friend",
                'content' => $csrecord->record,
                'mine' => false,
                'fromid' => $openid,
                'timestamp' => $csrecord->created_at->format('U')*1000,
            ];
            array_push($arr,json_encode($tmp));
            array_push($ids,$csrecord->id);
            $count += 1;
        }

        $ret = array(
            'unread_count' => $count,
            'unread_message' => $arr
        );
        // 更新为push
        Csrecord::whereIn('id',$ids)->update(['push' => 1]);
        return $ret;
    }


    /**
     * 获取当前客服聊天记录
     * @param $appid
     * @param $openid
     * @return array
     */
    public function _getList($appid,$openid)
    {
        $currentUser = Admin::find($openid);
        $list = array();
        // 获取状态
        if((int)Cache::get('PUSHSTATUS_'.$appid.'_'.$openid) == 1){
            $online = 'online';
        }else{
            $online = 'hide';
        }
        $list['mine'] = array(
            'username' => $currentUser->name,
            'id' => $currentUser->id,
            'status' => $online,
            'avatar' => $currentUser->avator,
        );
        $list['friend'] = array();
        $cslist = array();
        // 获取客服聊天记录
        $usersOpenids = $this->getCustomService2Users($appid,$openid);

        foreach ($usersOpenids as $userOpenid)
        {
            $_user = User::where('openid', $userOpenid)->first();
            array_push($cslist,array(
                'username' => $_user->nickname,
                'id' => $_user->openid,
                'avatar' => $_user->avatar,
            ));
        }

        $cs = array('groupname'=>'客户','id'=>'groupname1','online'=>'','list'=>$cslist);
        array_push($list['friend'],$cs);
        return $list;
    }

    /**
     * 注册到缓存中去
     * @param $appid
     * @param $openid
     * @param $fd
     */
    public function _register($appid,$openid,$fd)
    {
        // 1分钟
        $this->_cache('PUSHFD_'.$appid.'_'.$openid,$fd,Carbon::now()->addMinutes(1));
        if(Cache::has('PUSHSTATUS_'.$appid.'_'.$openid))
        {
            $this->_cache('PUSHSTATUS_'.$appid.'_'.$openid,Cache::get('PUSHSTATUS_'.$appid.'_'.$openid),Carbon::now()->addMinutes(1));
        }
        else
        {
            $this->_cache('PUSHSTATUS_'.$appid.'_'.$openid,1,Carbon::now()->addMinutes(1));
        }
    }


    public function _push($mesage,$openid,$appid)
    {
        $fd = Cache::get('PUSHFD_'.$appid.'_'.$openid);
        $ret = LaravooleFacade::push($fd, $mesage);
    }

    /**
     * 设置保存到缓存中
     * @param $key
     * @param $value
     * @param null $expiresAt
     */
    public function _cache($key,$value,$expiresAt = null)
    {
        // 先删除redis中的东西
        if(Cache::has($key)) Cache::forget($key);
        // 然后在添加
        return $expiresAt?Cache::put($key, $value, $expiresAt):Cache::forever($key,$value);
    }

    public function websocket(Request $request)
    {

    }

    public function ping(Request $request)
    {
        $appid = $request->input('appid');
        $csopenid = $request->input('openid');
        $fd = $request->getLaravooleInfo()->fd;
        $this->_register($appid,$csopenid,$fd);
        return response()->json([
            'message_type' => 'pong',
            'msg' => 'pong',
            'fd'=>$fd
        ]);
    }

    /**
     * 一开始就注册
     * @param Request $request
     * @return v
     */
    public function register(Request $request)
    {
        $appid = $request->input('appid');
        $csopenid = $request->input('openid');
        $inited = $request->input('init');
        $fd = $request->getLaravooleInfo()->fd;

        $this->_register($appid,$csopenid,$fd);
        $ret = array(
                'message_type' => 'register',
                'data' => $this->_getmessage($appid,$csopenid),
                'initdata' => $inited ? array() : $this->_getList($appid,$csopenid)
        );
        return response()->json($ret);
    }


    /**
     *  取消注册
     * @param Request $request
     * @return v
     */
    public function unregister(Request $request)
    {
        $appid = $request->input('appid');
        $openid = $request->input('openid');
        Cache::forget('PUSHFD_'.$appid.'_'.$openid);
        Cache::forget('PUSHSTATUS_'.$appid.'_'.$openid);
        return response()->json([]);
    }


    /**
     * 客服更改状态
     * @param Request $request
     * @return v
     */
    public function status(Request $request)
    {
        try {
            $appid = $request->input('appid');
            $csopenid = $request->input('openid');
            $status = ($request->input('status') == 'online')?1:0;
//            $this->_cache('PUSHSTATUS_'.$appid.'_'.$csopenid,$status,Carbon::now()->addMinutes(1));
            $this->_cache('PUSHSTATUS_'.$appid.'_'.$csopenid,$status);
            $ret = array(
                'message_type' => 'status',
                'msg' => 'OK'
            );
        } catch (ValidatorException $e) {
            $ret = array(
                'message_type' => 'status',
                'msg' => 'ERROR'
            );
        }
        return response()->json($ret);
    }

    /**
     * 用户发给客服消息
     * @param Request $request
     * @return v
     */
    public function sendMessage(Request $request)
    {
        $message = array('touser' => 1);
        $message['appid'] = $request->input('appid');
        $message['from'] = $request->input('csopenid');
        $message['to'] = $request->input('useropenid');
        $message['MsgId'] = $this->uuid();
        $message['message'] = $request->input('message');
        Queue::push(new Message((object)$message));
        return response()->json([
            'message_type' => 'sendMessage',
            'msg' => 'OK'
        ]);

    }

    /**
     * 推送新消息
     * @param Request $request
     */
    public function newMessage(Request $request)
    {
        $csopenid = $request->input('openid');
        $recordid = $request->input('record');
        $appid = env('WECHAT_ORIGINALID','gh_d30a13af0bc7');

        // 获取数据
        $csrecord = Csrecord::where([
            'appid'     =>  $appid,
            'csopenid'  =>  $csopenid,
            'read'      =>  0,
            'push'      =>  0,
            'id'        =>  $recordid
        ])->with('user')->first();

        // 当前时间
        $now = Carbon::now();
        // 客服值班时间
        $start = Carbon::today()->addHours(9);
        $end = Carbon::today()->addHours(22);

        // 判断当前客服都不在线


        if(!($now > $start && $now < $end) && ($this->isallcsfree() == 0)){
        //if($now > $start && $now < $end){
            if(!Cache::has('KFMESSAGERETURN'.$appid.'_'.$csrecord->useropenid))
            {
                // 发送客服
                $m = array('touser' => 1);
                $m['appid'] = 'gh_d30a13af0bc7';
                $m['from'] = 2;
                $m['to'] = $csrecord->useropenid;
                $m['MsgId'] = $this->uuid();
                $m['message'] = $csrecord->user->nickname.'，我们的值班时间是9:00-22:00，非值班时间的响应速度比较慢，我们上线后会尽快回复你。';
                Queue::push(new Message((object)$m));

                $kfArr = ['oy7KgwHNxrXiaBH0CwbSk-5S2ri4','oy7KgwBdT7xr5Q7CFi1DhLrP-zF8','oy7KgwHO3s5wUVZMaBfQa7tbJWqQ','oy7KgwA9_r0O_jJ-LhKEwIbfQWWU','oy7KgwKi7A74CWhhjQkrdgjTB538'];
                foreach($kfArr as $v){
                    $m = array('touser' => 1);
                    $m['appid'] = 'gh_d30a13af0bc7';
                    $m['from'] = 1;
                    $m['to'] = $v;
                    $m['MsgId'] = $this->uuid();
                    $m['message'] = '有客人来了，快去看看吧';
                    Queue::push(new Message((object)$m));
                }
                // 设置缓存时间
                $extime = Carbon::now()->addMinutes(20);
                Cache::put('KFMESSAGERETURN'.$appid.'_'.$csrecord->useropenid, 1, $extime);
            }

        }


        if($csrecord->type =='SYSTEM')
        {
            $result = [
                'message_type' => 'SystemMessage',
                'user' => [
                    'username' => $csrecord->user->nickname,
                    'avatar' => $csrecord->user->avatar,
                ],
                'data' => [
                	'username' => '系统消息',
                    'avatar' => '/images/logo.jpeg',
                    'id' => $csrecord->useropenid,
                    'type' => "friend",
                    'content' => $csrecord->record,
                ]
            ];
        }
        else
        {
            $result = [
                'message_type' => 'newMessage',
                'data' => [
                    'username' => $csrecord->user->nickname,
                    'avatar' => $csrecord->user->avatar,
                    'id' => $csrecord->useropenid,
                    'cid' => $csrecord->id,
                    'type' => "friend",
                    'mine' => false,
                    'fromid' => $csrecord->useropenid,
                    'timestamp' => $csrecord->created_at->format('U')*1000,
                ]
            ];
            switch ($csrecord->type)
            {
                case 'text':
                    $result['data']['content'] = $csrecord->record;
                    break;
                case 'image':
                    $result['data']['content'] = 'img['.env('APP_URL').'/upload/'.$csrecord->record.']';
                    break;
                case 'voice':
                    $result['data']['content'] = 'audio['.env('APP_URL').'/upload/'.$csrecord->record.']';
                    break;
            }
        }
        $result = json_encode($result);
        // 更新推送id识别
        Csrecord::whereIn('id',array($csrecord->id))->update(['push' => 1]);

        // _push
        $this->_push(json_encode(array('result'=>$result)),$csopenid,$appid);
    }


    public function isallcsfree()
    {
        $appid = env('WECHAT_ORIGINALID','gh_d30a13af0bc7');

        $arr = [1,2,3,4,5];
        $t = 0;
        foreach($arr as $v){
            if(Cache::has('PUSHSTATUS_'.$appid.'_'.$v)){
                if(Cache::get('PUSHSTATUS_'.$appid.'_'.$v) == 1)
                {
                    Log::info('当前客服在线-'.$v);
                    $t++;
                }
            }
        }
        Log::info($t);
        return $t;
    }



}
