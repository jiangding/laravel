<?php

namespace App\Http\Traits;
use Log;
use App\Models\Detail;

trait DetailTrait
{
    /** 插入清单数据
     * @param $detail
     * @return bool
     */
    public function insertDetail($detail)
    {

        try {
            $detal = Detail::create($detail);

            if($detal->id){
                Log::info("清单插入成功, ".$detal->id);
            }else{
                Log::info("清单插入失败 ");
            }
            return $detal->id;

        } catch (ValidatorException $e) {
            Log::info($e->getMessageBag());
            return false;
        }
    }

}