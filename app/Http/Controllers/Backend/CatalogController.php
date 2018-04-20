<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\Catalog;
use App\Http\Controllers\Backend;
use App\Models\components\cusResponse;
use Excel;
class CatalogController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 品类list
     */

    public function index(Request $request){

        // 获取数据
//        $shortcode = $request->input('shortcode');
//        $name = $request->input('name');
//
//        $catalogWhere = Catalog::orderBy('id', 'desc');
//        // barcode
//        if($shortcode){
//            $catalogWhere->where('shortcode', $shortcode);
//        }
//
//        // 产品名
//        if($name){
//            $catalogWhere->where('name', 'like', "%".$name."%");
//        }

        // 查询
        $data = Catalog::orderBy('id', 'desc')->paginate(15);


        return view('backend.catalog', [ 'data' => $data ]);
    }

    /**
     * 品类add
     */

    public function add(){

        return view('backend.catalog-add');

    }

    /**
     * 品类edit
     */

    public function edit($id){

        $row = Catalog::find($id);
//        dd($row);
        return view('backend.catalog-edit', [ 'json' => $row ]);

    }


    /**
     *  品类执行更新
     */
    public function update(Request $request){

        $id = $request->input('id');
        $keyword = $request->input('keyword');

        $cata = Catalog::find($id);
        $cata->keyword = $keyword;
        $affect = $cata->save();
        $cusResponse = new cusResponse();
        if($affect){
            $cusResponse->status = 200;
            $cusResponse->message = "success";
        }else{
            $cusResponse->status = 500;
            $cusResponse->message = "未获取到服务器数据";
        }

        return $cusResponse->toJson();

    }

    /**
     * 导出
     */
    public function export()
    {
        $data = Catalog::orderBy('id', 'desc')->paginate(15);
        $arr = [];
        $cellData = array(
            array('id','品类代码','品类名','关键词')
        );
        foreach($data as $k=>$v){

            $arr[] = $v->id;
            $arr[] = $v->shortcode;
            $arr[] = $v->name;
            $arr[] = $v->keyword;


            $cellData[] = $arr;
            unset($arr);
        }

        Excel::create('分类列表',function($excel) use ($cellData){
            $excel->sheet('sheet', function($sheet) use ($cellData){
                $sheet->rows($cellData);
            });
        })->export('xls');
    }

}

