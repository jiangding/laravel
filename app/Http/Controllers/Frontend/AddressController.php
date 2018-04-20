<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Detail;
use Illuminate\Http\Request;
use App\Models\Address;

class AddressController extends Controller
{
    /**
     * 地址管理首页
     */
    public function index(Request $request)
    {
        $currentUser = $this->currentUser;
        $title = '地址管理';
        $addresses = Address::where('userid',$currentUser['id'])->get();
        return view('frontend.Address.index', compact('addresses','title'));
    }


    /**
     * 添加新地址
     */
    public function create(Request $request)
    {
        $currentUser = $this->currentUser;
        if ($request->ajax())
        {
            try {

                $address = new Address();
                $address->userid = $request->input('userid');
                $address->name = $request->input('name');
                $address->phone = $request->input('phone');
                $address->zip = $request->input('zip');
                $address->address = $request->input('address');
                $address->label = $request->input('label');
                $address->area = $request->input('area');
                $address->areaid = $request->input('areaid');
                $address->save();

                // 如果有detailid就更新
                $did = $request->input('detailid');
                if($did){
                    $detial = Detail::find($did);
                    $detial->addressid = $address->id;
                    $detial->save();
                }

                $response = [
                    'message' => 'Address updated.',
                    'data'    => '',
                    'retcode' => 0
                ];
            } catch (ValidatorException $e) {
                return response()->json([
                    'retcode'   => 1,
                    'message' => $e->getMessageBag()
                ]);
            }
            return response()->json($response);
        }
        else
        {
            $title = '添加新地址';
            return view('frontend.Address.add', compact('title','currentUser'));
        }

    }

    /**
     * 编辑地址
     */
    public function update(Request $request,$id)
    {
        $currentUser = $this->currentUser;
        if ($request->ajax())
        {
            try {
                $address = Address::find($id);
                $address->userid = $request->input('userid');
                $address->name = $request->input('name');
                $address->phone = $request->input('phone');
                $address->zip = $request->input('zip');
                $address->address = $request->input('address');
                $address->label = $request->input('label');
                $address->area = $request->input('area');
                $address->areaid = $request->input('areaid');
                $address->save();

                // 如果有detailid就更新
                $did = $request->input('detailid');
                if($did){
                    $detial = Detail::find($did);
                    $detial->addressid = $address->id;
                    $detial->save();
                }

                $response = [
                    'message' => 'Address updated.',
                    'data'    => '',
                    'retcode' => 0
                ];
            } catch (ValidatorException $e) {
                return response()->json([
                    'retcode'   => 1,
                    'message' => $e->getMessageBag()
                ]);
            }
            return response()->json($response);
        }
        else
        {
            $title = '编辑地址';
            $address = Address::find($id);
            return view('frontend.Address.edit', compact('title','currentUser','address','id'));
        }

    }

    /**
     * 删除当前地址
     */
    public function delete(Request $request)
    {
        $currentUser = $this->currentUser;
        $id = $request->input('id');
        $addresses = Address::where(['id'=>$id, 'userid'=>$currentUser['id']])->first();
        if ($addresses)
        {
            $addresses->delete();
            $response = [
                    'retcode' => 0,
                    'fieldErrors' => ''
            ];

        }
        else
        {
            $response = [
                    'retcode' => 1,
                    'fieldErrors' => ''
            ];
        }
        return response()->json($response);
    }

}
