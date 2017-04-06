<?php
//--框架加载文件

// 初始化常量
defined('CORE_PATH') or define('CORE_PATH', __DIR__.'/');   //框架核心目录
defined('APP_PATH') or define('APP_PATH', dirname($_SERVER['SCRIPT_FILENAME']).'/');    //应用目录

// 简单的自动加载
spl_autoload_register( function($class){
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

//注册错误和异常处理
\kicoe\Core\Error::register();

//加载应用功能    --link start--  
\kicoe\Core\Link::start();