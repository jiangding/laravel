<?php

namespace App\Http\Traits;
use Log;

use Illuminate\Redis\Database;
//use App\Repositories\CsrecordRepository;
//use App\Validators\CsrecordValidator;
//use Prettus\Validator\Contracts\ValidatorInterface;
//use Prettus\Validator\Exceptions\ValidatorException;
use App\Models\Csrecord;

trait CustomerServiceTrait
{
    public function CustomerService2User($appid,array $cslist,array $get,$online = true)
    {
        $ret = array();
        $cs2user = $this->getCustomerService($appid);
        foreach ($cs2user as $key => $_cs2user)
        {
            if(count($cslist) && (!in_array($_cs2user['openid'],$cslist))) continue;

            if(count($get))
            {
                $_tmps = self::getCustomService2Users($appid,$_cs2user['openid']);
                Log::info($_tmps);
                if(count($_tmps) == 0 && $online)
                {
                    array_push($ret,[$get[0] => $_cs2user['openid'],$get[1] => '']);
                }
                else
                {
                    foreach ($_tmps as $_tmp)
                    {
                        array_push($ret,[$get[0] => $_cs2user['openid'],$get[1] => $_tmp]);
                    }
                }
            }
            else
            {
                $ret[$_cs2user['openid']] = self::getCustomService2Users($appid,$_cs2user['openid']);
            }

        }
        Log::info($ret);
        return $ret;
    }


    /**
     * 接受消息插入到csRecord表中
     * @param $record
     * @param $csopenid
     * @return bool
     */
    public function insertRecord($record,$csopenid)
    {
//        $CsrecordRepository = App(CsrecordRepository::class);
//        $CsrecordValidator = App(CsrecordValidator::class);
        try {
//            $csrecord = new Csrecord();
//            $CsrecordValidator->with($record)->passesOrFail(ValidatorInterface::RULE_CREATE);
//            $record = $CsrecordRepository->create($record);
            $record = Csrecord::create($record);
            return $record->id;

        } catch (ValidatorException $e) {
            Log::info($e->getMessageBag());
            return false;
        }
    }

    /**
     * 插入数据到缓存中
     */
    public function insertcs2user($appid,$csopenid,$useropenid)
    {
        self::cleanCustomService2Users($appid,$useropenid);
        self::addCustomService2Users($appid,$csopenid,$useropenid);
        return true;
    }

    /**
     * 添加客服 to 用户的 记录
     */
    public function addCustomService2Users($appid,$csopenid,$useropenid)
    {
        $redis = app(Database::class)->connection();
        // redis 添加 用户openid 作为值, 添加重复的也只为一个!
        $redis->sadd('CustomService2Users_'.$appid.'_'.$csopenid,[$useropenid]);
        //$redis->lpush('CustomService2Users_'.$appid.'_'.$csopenid,[$useropenid]);
        // 设置超时， 如果 key 已经存在， SETEX 命令将会替换旧的值
        $redis->setex('User2CustomService_'.$appid.'_'.$useropenid,10*60,$csopenid);
    }

    /**
     * 清除客服 to 用户
     */
    public function cleanCustomService2Users($appid,$useropenid)
    {
        $redis = app(Database::class)->connection();
        // 获取用户发给客服的 客服id
        $csopenid = $this->redis->get('User2CustomService_'.$appid.'_'.$useropenid);
        // 删除这条记录
        $redis->del('User2CustomService_'.$appid.'_'.$useropenid);
        // 移除改集合中的一个元素, 移除 值 = 用户openid 的项
        $redis->srem('CustomService2Users_'.$appid.'_'.$csopenid,[$useropenid]);
    }

    /**
     * 获取发给客服的用户list
     */
    public function getCustomService2Users($appid,$csopenid)
    {
        $redis = app(Database::class)->connection();
        // 获取没有重复的集合的值
        return $redis->sinter(['CustomService2Users_'.$appid.'_'.$csopenid]);
    }

    /**
     * 获取用户发给客服
     */
    public function getCustomServiceByUser($appid,$useropenid)
    {
        $redis = app(Database::class)->connection();
        // 获取用户 to 客服的 客服 id
        return $redis->get('User2CustomService_'.$appid.'_'.$useropenid);
    }
}