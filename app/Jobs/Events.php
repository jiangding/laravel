<?php

namespace App\Jobs;

use Carbon\Carbon;
use App\Jobs\Job;

use App\Models\User;
use App\Models\Coordinate;
use Log;
use Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use EasyWeChat\Foundation\Application;
use EasyWeChat\Message\News;
use EasyWeChat\Message\Image;

class Events extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $message;
    protected $wechat;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($message)
    {
        $this->onQueue('EVENT');
        $this->message = $message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info($this->message);
        $this->wechat = app('wechat');
        switch ($this->message->Event) {
            case 'subscribe':
                $this->insertUser($this->message);

                // 发送图文
                $news = new News();
                $news->description = '开启你的购物管家';
                $news->url = env('APP_URL').'/subscribe/subscribe.html';
                $news->image = env('APP_URL').'/images/news.gif';
                $this->wechat->staff->message($news)->to($this->message->FromUserName)->send();
//                sleep(1);
//                $Media = $this->wechat->material_temporary->uploadImage(__DIR__.'/../../public/images/couponsss.jpg');
//                $img = new Image(['media_id' => $Media->media_id]);
//                $this->wechat->staff->message($img)->to($this->message->FromUserName)->send();
                break;
            case 'unsubscribe':
                $this->removeCache($this->message);
                break;
            case 'LOCATION':
                $this->insertcoordinate($this->message);
                break;
            default:
                // NULL
                break;
        }
    }

    public function failed()
    {
        // Called when the job is failing...
    }

    // 获取用户经纬度
    private function insertcoordinate($message)
    {
        try
        {
            $user = Users::where('openid', $message->FromUserName)->firstOrFail();
            $coordinate = new Coordinate;
            $coordinate->userid = $user->id;
            $coordinate->Latitude = $message->Latitude;
            $coordinate->Longitude = $message->Longitude;
            $coordinate->Precision = $message->Precision;
            $coordinate->CreateTime = $message->CreateTime;
            $coordinate->save();
            return '';
        }
        catch(ModelNotFoundException $e)
        {
            return '';
        }
    }

    /**
     * 用户关注
     * @param $message
     */
    private function insertUser($message)
    {
        $appid = $message->ToUserName;
        $openid = $message->FromUserName;
        // 获取线上的数据
        $wechat_user = $this->wechat->user->get($openid);

        // 并且在数据库中有记录才更新
        $userInfo = User::where('openid', $openid)->first();

        if($openid == $wechat_user->openid && $wechat_user->subscribe == 1 && $userInfo)
        {
            try
            {
                // 更新主要的数据
                $user = User::where('openid', $openid)->first();
                $user->nickname = $wechat_user->nickname;
                $user->sex = isset($wechat_user->sex)?$wechat_user->sex:'1';
                $user->email = isset($wechat_user->email)?$wechat_user->email:'';
                $user->country = isset($wechat_user->country)?$wechat_user->country:'';
                $user->province = isset($wechat_user->province)?$wechat_user->province:'';
                $user->city = isset($wechat_user->city)?$wechat_user->city:'';
                $user->avatar = isset($wechat_user->headimgurl)?$wechat_user->headimgurl:'';
                $user->status = 1;
                $user->save();

                // 写入到缓存中去
//                Cache::rememberForever($appid.'_'.$openid,function () use ($appid,$wechat_user) {
//                    return User::where('appid', $appid)
//                        ->where('openid', $wechat_user->openid)
//                        ->firstOrFail();
//                });
            }
            catch(ModelNotFoundException $e)
            {
                $this->addGuest($appid,$wechat_user);
            }
        }else{
                $this->addUser($appid,$wechat_user);
        }
    }

    /**
     * 用户入库
     * @param $appid
     * @param $wechat_user
     * @return mixed
     */
    private function addUser($appid,$wechat_user)
    {
        Log::info('Add User['.$wechat_user->openid.'] into db');
        $user = new User;
        $user->openid = $wechat_user->openid;
        $user->unionid = isset($wechat_user->unionid)?$wechat_user->unionid:'0';
        $user->nickname = $wechat_user->nickname;
        $user->sex = isset($wechat_user->sex)?$wechat_user->sex:'1';
        $user->email = isset($wechat_user->email)?$wechat_user->email:'';
        $user->country = isset($wechat_user->country)?$wechat_user->country:'';
        $user->province = isset($wechat_user->province)?$wechat_user->province:'';
        $user->city = isset($wechat_user->city)?$wechat_user->city:'';
        $user->avatar = isset($wechat_user->headimgurl)?$wechat_user->headimgurl:'';
        $user->remind = 7;
        $user->save();
        Log::info('Add User['.$wechat_user->openid.'] into db SUCCESS');
        return $user->id;
    }

    private function addGuest($appid,$wechat_user)
    {
        $user = User::where('id', $this->addUser($appid,$wechat_user))->first();
//        $r_guest = Role::where('name', 'r_user')->get()->first();
//        $user->attachRole($r_guest);
//        Cache::rememberForever($appid.'_'.$wechat_user->openid,function () use ($appid,$wechat_user) {
//            return $user;
//        });
//        Log::info('AttachRole User['.$wechat_user->openid.'] into db SUCCESS');
//        return $user->id;
    }

    /**
     * 缓存中删除
     * @param $message
     */
    private function removeCache($message)
    {
        $openid = $message->FromUserName;
        // 更新主要的数据
        $user = User::where('openid', $openid)->first();
        if($user){
            $user->status = 0;
            $user->save();
        }

        //Log::info('User['.$message->ToUserName.'_',$message->FromUserName.'] unsubscribe!');
        Cache::forget($message->ToUserName.'_',$message->FromUserName);
    }
}
