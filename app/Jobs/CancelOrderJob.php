<?php

namespace App\Jobs;

use Carbon\Carbon;

use Log;
use Cache;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Order;


class CancelOrderJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->onQueue('CANCELORDER');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // 20分钟后的东西
        $now = Carbon::now()->subMinutes(20);

        $orders = Order::where('status', 0)
            ->where('created_at','<',$now)
            ->get();
        if(count($orders) != 0){
            $arrOrders = array();
            // 获取order id
            foreach($orders as $k=>$v){
                $arrOrders[] = $v->id;
                // 记录日志
                $l = new \App\Models\Log();
                $l->type = 1;
                $l->pkey = $v->orderid;
                $l->pval = "系统20分钟自动取消订单";
                $l->save();
            }
            Log::info('取消成功');
            // 更新
            Order::whereIn('id',$arrOrders)->update(['status'=>2]);
        }else{
            Log::info('miss');
        }

    }

    public function failed()
    {
        // Called when the job is failing...
    }

}
