<?php
// -- 框架加载文件 --

// 框架核心目录
defined('CORE_PATH') or define('CORE_PATH', __DIR__.'/');

// 简单的自动加载
// app -> app kicoe/core ->core
spl_autoload_register( function ($class) {
    $prefix = substr($class, 0, 3);
    if ($prefix == 'app') {
        $file = APP_PATH. str_replace('\\', '/', substr($class, 4)). '.php';
    } elseif ($prefix == 'kic') {
        $file = CORE_PATH. 'library/'. str_replace('\\', '/', substr($class, 11)). '.php';
    } else {
        return;
    }
    if (file_exists($file)) {
        require $file;
    }
} );

use kicoe\Core\Config;
use kicoe\Core\Error;
use kicoe\Core\Request;

// 加载配置文件
Config::load(APP_PATH.'config.php');

// 是否开启报错
Config::prpr('test') && Error::register();

// --link start--  
Request::getInstance()->route();
