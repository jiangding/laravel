<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use zgldh\UploadManager\UploadManager;

class UploadController extends Controller
{

    /**
     * 上传
     * @param Request $request
     * @return bool|\zgldh\UploadManager\Upload
     */
    public function upload(Request $request)
    {
        $manager = UploadManager::getInstance();
        $upload = $manager->withDisk('public')->upload($request->file('upload'));
        $upload->save();
        return $upload;
    }

    /**
     * 上传图片
     * @param Request $request
     * @return v
     */
    public function csupload(Request $request)
    {
        $manager = UploadManager::getInstance();
        $upload = $manager->withDisk('public')->upload($request->file('file'));
        $upload->save();

        return response()->json([
            'code' => 0,
            'msg' => '',
            'data' => ['src' => '/upload/'.$upload->path]
        ]);
    }
}
