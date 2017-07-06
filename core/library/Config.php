<?php

namespace kicoe\Core;

class Config
{

    private static $config = [];

    /**
     * 解析配置文件
     * @param string  $type配置项
     * @param string $path 配置文件路径
     * @return array 配置信息
     */
    public static function prpr($type, $path = 'config.php')
    {
        if ( !isset(self::$config[$path]) ) {
            self::$config[$path] = include APP_PATH.$path;
        }
        return self::$config[$path][$type];
    }
}