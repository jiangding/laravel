<?php

namespace App\Jobs;

use Config;
use Cache;
use Carbon\Carbon;
use App\Jobs\Job;
use App\Http\Traits\UsersTrait;
use App\Http\Traits\CustomerServiceTrait;
use Illuminate\Queue\SerializesModels;
use App\Http\Traits\WefaceTrait;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Redis\Database;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;
use Storage;
use EasyWeChat\Message\Text;
use EasyWeChat\Message\Image;
use EasyWeChat\Message\News;
use App\Models\Csrecord;
use App\Models\User;
use GuzzleHttp\Client;

//use FFMpeg\FFMpeg;

class Message extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels, UsersTrait, CustomerServiceTrait, WefaceTrait;

    protected $temporary;
    protected $message;
    protected $staff;
    protected $wechat;
    protected $redis;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($message)
    {
        $this->onQueue('MESSAGER');
        $this->message = $message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // 实例化
        $this->wechat = app('wechat');
        // 临时素材
        $this->temporary = $this->wechat->material_temporary;
        // 客服分发消息
        $this->staff = $this->wechat->staff;
        // redis
        $this->redis = app(Database::class)->connection();
        // 队列尝试次数
        if ($this->attempts() > 1)
        {
            if(isset($this->message->touser))
            {
                // 10秒后在执行
                $this->delay(10)->_sendhandle($this->message);
            }
            else
            {
                $this->delay(10)->_recvhandle($this->message);
            }
        }
        // 队列次数大于3次就直接
        elseif ($this->attempts() > 3)
        {
            $this->failed();
        }
        else
        {
            // 客服发消息给用户
            if(isset($this->message->touser))
            {
                $this->_sendhandle($this->message);
            }
            else
            {   // 用户发给客服
                $this->_recvhandle($this->message);
            }
        }
    }

    public function failed()
    {
        // Called when the job is failing...
    }

    /**
     * 处理消息
     * @param $message
     */
    private function _sendhandle($message)
    {
        $csrecord = new Csrecord();

        // 如果有图片
        if(preg_match("/^img\[(.*?)\]/i",$message->message,$match))
        {
            //\Log::info(__DIR__.'/../../public'.$match[1]);
            $Media = $this->temporary->uploadImage(__DIR__.'/../../public'.$match[1]);
            $csrecord->type = 'image';
            $csrecord->record = $message->message;
            $send_message = new Image(['media_id' => $Media->media_id]);
        }
        else  // 只是文本
        {
            $csrecord->type = 'text';
            $csrecord->record = $message->message;
            $send_message = new Text(['content' => $message->message]);
        }

        $csrecord->appid = $message->appid;
        $csrecord->csopenid = $message->from;
        $csrecord->useropenid = $message->to;
        $csrecord->MsgId = $message->MsgId;
        $csrecord->mode = 1;
        $csrecord->read = 1;
        $csrecord->save();

        // 插入数据到redis
        $this->insertcs2user($message->appid,$message->from,$message->to);

        $result = $this->staff->message($send_message)->to($message->to)->send();
        \Log::info($result);
    }


    // push消息
    private function _pushhandle($recordid,$openid,$useropenid)
    {
        $client = new Client(['base_uri' => 'http://ganghao.i2mago.com/push/']);
        $response = $client->request('GET', 'newMessage', [
            'query' => [
                'openid' => $openid,
                'useropenid' => $useropenid,
                'record' => $recordid
            ]
        ]);
    }

    /**
     * 接收消息处理
     */
    private function _recvhandle($message)
    {
        // 公众号id
        $appid = $message->ToUserName;
        // 用户openid
        $useropenid = $message->FromUserName;
        $MsgId = $message->MsgId;
        try
        {
            Csrecord::where('appid',$appid)
                ->where('useropenid',$useropenid)
                ->where('Msgid',$MsgId)
                ->firstOrFail();
            return true;
        }
        catch (ModelNotFoundException $exception)
        {
            // 新消息处理
            \Log::info('new message');

            // 获取上次客服id
            $csopneid = $this->getCustomServiceByUser($appid,$useropenid);

            \Log::info('prev cs id ='.$csopneid);
            // 判断当前获取的客服是否在线
            $free = false;
            if($csopneid) {
                $free = self::iscsfree($appid, $csopneid);
            }
            // 如果在线就选用 否则就从新找一个客服
            $csopneid = ($free && $csopneid)?$csopneid:(self::findcs($appid, $message));
            \Log::info('current cs : '.$csopneid);
            //$csopneid = 1;
            // push
            if($csopneid){

                self::_pushhandle(self::insertCsrecord($message,$csopneid),$csopneid,$useropenid);
            }
        }
    }

    /**
     * 判断客服是否空闲
     * @param $appid
     * @param $openid
     * @return bool
     */
    private function iscsfree($appid,$openid)
    {
        if(!$openid) return false;
        \Log::info('iscsfree');
        if(Cache::has('PUSHSTATUS_'.$appid.'_'.$openid))
        {
            if(Cache::get('PUSHSTATUS_'.$appid.'_'.$openid) == 1)
            {
                \Log::info('is free');
                return true;
            }
            else
            {
                \Log::info('not free');
                return false;
            }
        }
        else
        {
            \Log::info('not free');
            return false;
        }
    }

    /**
     * 重新找个客服
     * @param $appid
     * @param $message
     * @return mixed
     */
    private function findcs($appid, $message)
    {
        // 获取当前所有客服
        $arrCS = $this->getCustomerService($appid);
        $arr = array();
        foreach ($arrCS as $value)
        {
            if(!Cache::has('PUSHSTATUS_'.$appid.'_'.$value['id']))
            {
                continue;
            }
            if(Cache::get('PUSHSTATUS_'.$appid.'_'.$value['id']) == 0)
            {
                continue;
            }
            // 找到当前在线的客服们
            array_push($arr,$value['id']);
        }
        \Log::info('online cs - '.json_encode($arr));

        //$csopenid = $this->CustomerService2User($appid,$arr,['csopenid','useropenid'],true);
        if(count($arr) == 0)
        {
//            // 客服都不在线
//            Cache::forever('USER_MESSAGE_STUFF'.$appid, $message);
//            \Log::info('已把消息存到了缓存中');
//            \Log::info($message);
//
//            return 0;
//          // 随机获取一个客服
            $csRow = $this->getCustomerServiceRandom($appid);
            \Log::info($csRow->id);
            return $csRow->id;
        }
        else
        {
           // 在线客服中就随机抽一个
            return $arr[array_rand($arr,1)];
        }
    }

    /**
     *  插入数据到redis 和 数据库中
     *
     * @param $message
     * @param $csopenid
     * @return bool
     */
    private function insertCsrecord($message,$csopenid)
    {
        // 插入数据到redis
        $this->insertcs2user($message->ToUserName,$csopenid,$message->FromUserName);

        $record = [
            'appid' => $message->ToUserName,
            'csopenid' => $csopenid,
            'useropenid' => $message->FromUserName,
            'mode' => 0,
            'Msgid' => $message->MsgId,
            'type' => $message->MsgType,
            'read' => 0,
            'push' => 0,
        ];
        if($message->MediaId)
        {
            $record['Mediaid'] = $message->MediaId;
        }
        switch ($message->MsgType)
        {
            case 'text':
                $record['record'] = $message->Content;
                break;
            case 'SYSTEM':
                $record['record'] = $message->Content;
                break;
            case 'image':
                $record['record'] = 'upload/image/'.(string) Uuid::uuid4().'.jpg';

                Storage::disk('public')->put(
                    $record['record'],
                    $this->temporary->getStream($message->MediaId)
                );
                break;
            case 'voice':
                $uuid = (string) Uuid::uuid4();
                $record['record'] = 'voice/'.$uuid.'.'.$message->Format;
//                $record['record'] = 'voice/'.$uuid.'.mp3';

                Storage::disk('public')->put(
                    $record['record'],
                    $this->temporary->getStream($message->MediaId)
                );
//                $uuid = (string) Uuid::uuid4();
//                $ffmpeg = FFMpeg::create();

//                $audio = $ffmpeg->open(storage_path().'voice/'.$uuid.'.'.$message->Format);
//                $audio_format = new FFMpeg\Format\Audio\Mp3();
//                $audio->save($audio_format,storage_path().'voice/'.$uuid.'.mp3');
//                Storage::disk('public')->delete('voice/'.$uuid.'.'.$message->Format);
//                $record['record'] = 'voice/'.$uuid.'.mp3';
                break;
            case 'video':
                $uuid = (string) Uuid::uuid4();
                $record['record'] = 'video/'.$uuid.'.mp4';

                Storage::disk('public')->put(
                    $record['record'],
                    $this->temporary->getStream($message->MediaId)
                );
                break;
        }
        return $this->insertRecord($record,$csopenid);
    }





}