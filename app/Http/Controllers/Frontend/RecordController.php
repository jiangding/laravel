<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Product;
use Config;
use Illuminate\Http\Request;
use App\Models\Stock;
use App\Models\Catalog;
use App\Http\Traits\ProductsTrait;
use League\Flysystem\Exception;
use Predis\Protocol\Text\RequestSerializer;
use Queue;
use Log;
use App\Jobs\Message;
use App\Jobs\Template;
use App\Models\Record;

class RecordController extends Controller
{
    use ProductsTrait;
    public function index(Request $request, $id)
    {
        // 获取当前用户
        $currentUser = $this->currentUser;
        $title = '扫码记录';

        $records = Record::where([
            'userid'=>$currentUser['id'],
            'deleted_at'=>null
        ])->orderBy('updated_at','desc')->with('product')->get();
        foreach($records as $k=>$v){
            // 获取分类
            $v->catalog = Catalog::where('id', $v->product->catid)->first();
            //计算是否需要提示补货
            if ($v->remind_at != ''){
                $now = time();
                $record_time = strtotime($v->remind_at) - 5*24*60*60;
                if ($now >= $record_time){
                    $v->remind = '1';
                    $v->time = date('Y-m-d',strtotime($v->remind_at) );
                }
            }

    }

        return view('frontend.Record.index', compact('currentUser','title','records', 'id'));
    }

    /**
     * 详情
     */
    public function update(Request $request, $record_id){
        $currentUser = $this->currentUser;
        // 获取当前数据
        $records = Record::where(['id' => $record_id ])->with('product')->first();
        $records->catalog = Catalog::where('id', $records->product->catid)->first();
        if (!empty($records->remind_at)){
            $records->remind_at = date('Y-m-d',  strtotime($records->remind_at));
        }
        return view('frontend.Record.update', compact(['currentUser','title','records']));
    }

    public function updateRemind(Request $request, $value, $id){
        try{
            $records = Record::where(['id' => $id ])->with('product')->first();
            if ($value == 0){
                //如果是关闭提醒
                $records->status = 0;
            } else{
                //如果是打开提醒，先判断是否设置有时间，如果没有设置时间，则不开启提醒
                if (!empty($records->remind_at)){
                    $records->status = 1;
                }
            }

            $records->save();

            $response = [
                'message' => 'Record update remind_at.',
                'data'    => '',
                'retcode' => 0
            ];
        }catch (Exception $e){
            return response()->json([
                'retcode'   => 1,
                'message' => $e->getMessageBag()
            ]);
        }

        return response()->json($response);
    }



    /**
     * 删除扫码足迹记录
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function delete($id){
        $record = Record::where('id', $id)->first();
        $record->deleted_at = date('Y-m-d');
        $record->save();

        return redirect('/record/0');
    }

    /**
     * 提醒時間
     */
    public function setRemindDate(Request $request, $id,$date){
        $currentUser = $this->currentUser;
        if ($request->ajax()){
            try{
                //更新提醒時間
                $record = Record::where('id', $id)->first();
                $record->remind_at = $date;
                $record->status = 1;
                $record->save();

                $response = [
                    'message' => 'Record update remind_at.',
                    'data'    => '',
                    'retcode' => 0
                ];
            }catch (Exception $e){
                return response()->json([
                    'retcode'   => 1,
                    'message' => $e->getMessageBag()
                ]);
            }
        }
        return response()->json($response);
    }

}
