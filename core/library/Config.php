<?php
// 加载配置类

namespace kicoe\Core;

use kicoe\Core\Exception;

class Config
{

    private static $config = [];

    /**
     * 加载配置文件初始化
     * @param $path 配置文件路径
     */
    public static function load($path)
    {
        if (is_file($path)) {
            self::$config = include $path;
        } else {
            throw new Exception("找不到配置文件呢：", $path);
        }
    }

    /**
     * 解析配置文件
     * @param string  $type配置项
     * @return array 配置信息
     */
    public static function prpr($type)
    {
        if (isset(self::$config[$type])) {
            return self::$config[$type]; 
        }
        throw new Exception("配置项不存在呀：", $type);
    }
}