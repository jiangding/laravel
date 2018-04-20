<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;
use EasyWeChat\Foundation\Application;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;


class Controller extends BaseController
{
    use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;
    public function __construct()
    {

    }
    /**
     * 获取uuid
     * @return bool|mixed
     */
    public function uuid()
    {
        try {
            return str_replace('-','',(string) Uuid::uuid4());
        } catch (UnsatisfiedDependencyException $e) {
            return false;
        }
    }
}
