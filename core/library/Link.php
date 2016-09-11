<?php

namespace kicoe\Core;

use \kicoe\Core\Request;
use \kicoe\Core\Exception;

class Link
{
    //开始应用
    public static function start()
    {
        //注册路由
        Request::getInstance()->route();
    }
} 