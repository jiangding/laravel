<?php

namespace App\Jobs;

use Carbon\Carbon;
use App\Jobs\Job;

use App\Models\User;
use Log;
use Cache;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use EasyWeChat\Foundation\Application;


class Template extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $message;
    protected $notice;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($message)
    {
        $this->onQueue('WECHATTEMPLATE');
        $this->message = $message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->notice = app('wechat')->notice;
        if ($this->attempts() > 1)
        {
            $this->delay(10)->_send($this->message);
        }
        elseif ($this->attempts() > 3)
        {
            $this->failed();
        }
        else
        {
            $this->_send($this->message);
        }
    }

    public function failed()
    {
        // Called when the job is failing...
    }

    private function _send($message)
    {
        switch ($message->MsgType)
        {
            case 'LOGISTICS':
                $templateId = 'c6EA2f9uGapCqkUx8AotwVtVMLY2yNm5O3AlPvij3Ps';
                break;
            case 'STOCKNOTENOUGH':
                $templateId = 'frHbTSuocCElKWkOP63bDbTfoRQQxwyVWQRwDR8Sjqc';
                break;
//            case 'voice':
//                $templateId = Config::get('ganghao.stocknotenoughTemplate');
//                break;
        }
        $to = $message->openid;
        $url = $message->url;
        $this->notice->withTemplate($templateId)
                               ->withUrl($url)
                               ->withData($message->data)
                               ->to($to)->send();
    }
}
