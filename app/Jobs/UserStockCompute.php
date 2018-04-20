<?php

namespace App\Jobs;

use App\Models\Product;
use Cache;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;

use App\Models\Stock;

use App\Http\Traits\TemplateTrait;

class UserStockCompute extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels,TemplateTrait;
    protected $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->onQueue('USERSTOCKCOMPUTE');
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->attempts() == 1)
        {
            $stocks = Stock::where('userid',$this->user->id)
                                ->get();
            $productIds = array();
            $productNull = array();
            foreach ( $stocks as $stock )
            {
                $productArr = $this->compute($stock);
                // 七天补货提醒
                if($productArr[0] > 0)
                {
                    array_push($productIds,$productArr[1]);
                }
                // 0天已用完
                elseif($productArr[0] == 0)
                {
                	array_push($productNull,$productArr[1]);
                }
            }

            // 发送七天补货提醒
            if(count($productIds) > 0)
            {
                $productname = Product::find($productIds[0])->name;
                $this->sendStockNotEnoughTemplate($productname.'等'.count($productIds).'件商品',$this->user->openid);

                // 更新库存状态
                foreach($productIds as $seven){
                    $s = Stock::where('userid',$this->user->id)->where('productid',$seven)->first();
                    $s->is_send = 1;
                    $s->save();
                }
            }

            // 发送已用完提醒
            if(count($productNull) > 0)
            {
                $productname = Product::find($productNull[0])->name;
                $this->sendStockNullTemplate($productname.'等'.count($productNull).'件商品',$this->user->openid);

                // 更新库存状态
                foreach($productNull as $null){
                    $s = Stock::where('userid',$this->user->id)->where('productid',$null)->first();
                    $s->is_send = 2;
                    $s->save();
                }
            }
            Log::info("User ".$this->user->id." Compute Done");
        }
        else
        {
            $this->failed();
        }
    }

    public function failed()
    {
        // Called when the job is failing...
    }


    /**
     * 计算用户补货
     * @param $userstock
     * @return int
     */
    private function compute($userstock)
    {
        // 返回值
        $array = [];
        if($userstock->lastday > 1)
        {
            // 剩余天 -1
            $userstock->lastday = bcsub($userstock->lastday,1,2);
            // 剩余天除以单间周期
            $num = bcdiv($userstock->lastday,$userstock->cycle,1);
            // 剩余数量
            $userstock->quantity = intval($num);
            // 单间剩余量
            $userstock->last = $num - intval($num);

            // update
            $userstock->save();

            // 如果并且状态 is_send = 0
            if($userstock->lastday <= 7 && $userstock->is_send == 0)
            {
                // 返回数据
                $array[0] = 7;
                $array[1] = $userstock->productid;
                return $array;
            }

        }
        elseif ($userstock->lastday >= 0 && $userstock->lastday <= 1)
        {

            $userstock->lastday = 0;
            $userstock->quantity = 0;
            $userstock->last = 0.0;
            // update
            $userstock->save();

            // 如果并且状态
            if($userstock->is_send != 2)
            {
                // 返回数据
                $array[0] = 0;
                $array[1] = $userstock->productid;
                return $array;
            }

        }

        // 返回数据
        $array[0] = -1;
        return $array;

    }
}
