<?php
//--框架加载文件

// 初始化常量
defined('CORE_PATH') or define('CORE_PATH', __DIR__.'/');	//框架核心目录
defined('APP_PATH') or define('APP_PATH', dirname($_SERVER['SCRIPT_FILENAME']).'/');	//应用目录

//注册自动加载
require CORE_PATH . 'AutoLoad.php';
$core = new \kicoe\AutoLoad;	//自动加载类实例
$core->register();	//注册自动加载

//注册错误和异常处理
\kicoe\Core\Error::register();

//加载应用功能	--link start--	
\kicoe\Core\Link::start();