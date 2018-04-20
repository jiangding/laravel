<?php

namespace App\Jobs;

use Carbon\Carbon;
use App\Jobs\Job;

use App\Models\Product;
use Log;
use Cache;
use Excel;
use Illuminate\Support\Facades\Config;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class ProductImport extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $file;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($file)
    {
        $this->onQueue('PRODUCTIMPORT');
        $this->file = $file;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //if(Cache::has('PRODUCTIMPORT')) return false;
        //Cache::put('PRODUCTIMPORT', 1, Carbon::now()->addMinutes(5));

        Log::info('当前路径');
        //Log::info(\Laravoole\storage_path());
        Log::info($this->file->path);

        Excel::load('storage/app/public/'.$this->file->path, function($reader) {
            $datas = $reader->all();
            Log::info('AAAAAAAAAAAA');
            Log::info($datas);
            foreach ($datas as $data)
            {
                $barcode = $data['条形码'];
                $name = $data['产品名'];
                $manufacturer = $data['厂商'];
                $spec = $data['规格'];
                $trademark = $data['商标'];
                $TMALL = $data['天猫爬取地址']?$data['天猫爬取地址']:'';
                $JD = $data['京东爬取地址']?$data['京东爬取地址']:'';
                $YHD = $data['一号店爬取地址']?$data['一号店爬取地址']:'';
//                $s_TMALL_title = $data['天猫商品名']?$data['天猫商品名']:$name;
//                $s_TMALL_stock = $data['天猫库存']?$data['天猫库存']:'';
//                $s_TMALL_price = $data['天猫价格']?$data['天猫价格']:'';
//                $s_JD_title = $data['京东商品名']?$data['京东商品名']:$name;
//                $s_JD_stock = $data['京东库存']?$data['京东库存']:'';
//                $s_JD_price = $data['京东价格']?$data['京东价格']:'';
//                $s_YHD_title = $data['一号店商品名']?$data['一号店商品名']:$name;
//                $s_YHD_stock = $data['一号店库存']?$data['一号店库存']:'';
//                $s_YHD_price = $data['一号店价格']?$data['一号店价格']:'';

                Product::updateOrCreate([
                    'barcode' => $barcode
                ],[
                    'name' => $name,
                    'manufacturer' => $manufacturer,
                    'spec' => $spec,
                    'trademark' => $trademark,
                    'url' => json_encode(['TMALL'=> $TMALL,'JD'=> $JD,'YHD'=> $YHD]),
//                    'spider' => json_encode([
//                        'TMALL' => ['title' => $s_TMALL_title,'stock' => $s_TMALL_stock,'price' => $s_TMALL_price],
//                        'JD' => ['title' => $s_JD_title,'stock' => $s_JD_stock,'price' => $s_JD_price],
//                        'YHD' => ['title' => $s_YHD_title,'stock' => $s_YHD_stock,'price' => $s_YHD_price]
//                    ])
                ]);
            }
        });

    }

    public function failed()
    {
        // Called when the job is failing...
    }



}
