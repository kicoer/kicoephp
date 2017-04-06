<?php

namespace kicoe\Core;

class Config
{
    /**
     * 解析配置文件
     * @param string  $type配置项
     * @param string $path 配置文件路径
     * @return array 配置信息
     */
    public static function config_prpr($type, $path = 'config.php')
    {
        $config = include APP_PATH.$path;
        return $config[$type];
    }
}