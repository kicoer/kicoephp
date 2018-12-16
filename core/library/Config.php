<?php
// 加载配置类

namespace kicoe\Core;

class Config
{

    private static $config = [];

    /**
     * 加载配置文件初始化
     * @param $path string 配置文件路径
     * @throws \kicoe\Core\Exception
     */
    public static function load($path)
    {
        if (is_file($path)) {
            self::$config = include $path;
        } else {
            throw new Exception('no such directory：', $path);
        }
    }

    /**
     * 解析配置文件
     * @param string $type 配置项
     * @return array 配置信息
     * @throws \kicoe\Core\Exception
     */
    public static function prpr($type)
    {
        if (isset(self::$config[$type])) {
            return self::$config[$type]; 
        }
        throw new Exception("config item not set：", $type);
    }
}