<?php

namespace App\Jobs;

use App\Models\Product;
use App\Models\User;
use Cache;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;

use App\Models\Stock;

use App\Http\Traits\TemplateTrait;

class UserRemindCompute extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels,TemplateTrait;
    protected $record;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($record)
    {
        $this->onQueue('USERREMINDCOMPUTE');
        $this->record = $record;
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
            $productname = Product::find($this->record->productid)->name;
            $openid = User::find($this->record->userid)->openid;
            $this->sendStockNullTemplate($productname,$openid,$this->record->id);
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

}
