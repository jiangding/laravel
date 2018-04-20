<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;
use Ramsey\Uuid\Uuid;


class Controller extends BaseController
{
    use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {

    }

    /**
     * 对象转数组
     * @param $obj
     * @return array
     */
    public function toArray($obj)
    {
        $new = array();
        foreach ($obj as $key => $val) {
            $new[$key] = $val;
        }
        return $new;
    }

    public function uuid()
    {
        try {
            return str_replace('-','',(string) Uuid::uuid4());
        } catch (UnsatisfiedDependencyException $e) {
            return false;
        }
    }
}
