<?php

namespace App\Models\components;

/**
 * Class cusResponse
 * 统一返回接口
 */
class cusResponse
{
    public $status;
    public $message;

    /**
     * 返回json格式
     */
    public function toJson()
    {
        return json_encode($this, JSON_UNESCAPED_UNICODE);
    }

}
