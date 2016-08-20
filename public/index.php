<?php
// --link start
$start_time = microtime();
header("Content-type: text/html; charset=utf-8");
// 定义应用入口
define('APP_PATH', __DIR__ . '/../app/');
// 加载框架引导文件
require __DIR__ . '/../core/load.php';

echo '<br><br><br><span style="font-family:Microsoft Yahei;font-size:11px;"> 当前页面执行时间：'.(microtime()-$start_time).'秒</span>';