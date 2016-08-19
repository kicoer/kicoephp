<?php

namespace kicoe\Core;

/**
* 解析配置文件
*/
class Config
{
	public static function config_prpr($type)
    {
    	$config = include APP_PATH.'config.php';
    	return $config[$type];
    }
}