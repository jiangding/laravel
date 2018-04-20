<?php

namespace App\Console\Commands;

use App\Models\Stock;
use Illuminate\Console\Command;
use Queue;
use Log;
use App\Jobs\UserRemindCompute;
use App\Models\User;
use App\Models\Record;
use Carbon\Carbon;
class CheckUserStockCompute extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CheckUserStockCompute';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate user stocks remaining';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

//        $users = User::all('id','openid');
//
//        foreach($users as $user){
//            // 查看当前用户是否有库存
//            $arrStock =Stock::where('userid', $user->id)->get();
//            if(count($arrStock) > 0){
//                Queue::push(new UserRemindCompute($user));
//            }
//        }

        // 今天
        $curDate = Carbon::today();
        // 查询开启并且提醒时间是今天的记录
        $arrRecords = Record::where('status', 1)->where('remind_at',$curDate)->get();
        if(count($arrRecords) > 0){
            foreach($arrRecords as $record){
                // 发送消息
                Queue::push(new UserRemindCompute($record));
            }
        }
    }
}
