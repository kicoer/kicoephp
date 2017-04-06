<?php
// --link start
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
// 定义应用入口
define('APP_PATH', __DIR__ . '/../app/');
// 定义静态 公开库(当前)
define('PUB_PATH',__DIR__);
// 定义扩展库
define('EXT_PATH', __DIR__ . '/../extend/');
// 加载框架引导文件
require __DIR__ . '/../core/load.php';