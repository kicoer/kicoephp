<?php
// --link start

//开启调试模式
ini_set('display_errors','On');
error_reporting(E_ALL);

//输出测试避免乱码
header("Content-Type: text/html; charset=UTF-8");
// 定义应用入口
define('APP_PATH', __DIR__ . '/../app/');
// 加载框架引导文件
require __DIR__ . '/../core/load.php';