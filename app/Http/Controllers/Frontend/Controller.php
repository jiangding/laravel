<?php

namespace App\Http\Controllers\Frontend;

use Log;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;
use App\Models\User;
use EasyWeChat\Foundation\Application;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

class Controller extends BaseController
{
    use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;
    protected $currentUser;
    protected $wechat;
    public function __construct(Application $wechat)
    {
        $this->wechat = $wechat;
        // oauth验证后的东西
        $openid = session('wechat.oauth_user')['id'];
        $this->currentUser = User::where('openid',$openid)->first();
    }


    /**
     * 对象转数组
     * @param $obj
     * @return array
     */
    public function toArray($obj)
    {
        $new = array();
        foreach ($obj as $key => $val) {
            $new[$key] = $val;
        }
        return $new;
    }

    /**
     * 获取uuid
     * @return bool|mixed
     */
    public function uuid()
    {
        try {
            return str_replace('-','',(string) Uuid::uuid4());
        } catch (UnsatisfiedDependencyException $e) {
            return false;
        }
    }
}
