<?php

namespace kicoe\Core;

/**
 * 缓存类，序列化
 */
class Cache
{
    /**
     * 写入缓存.cache
     * @param string $name 缓存文件名
     * @param array|obj $data 要序列化的数据
     */
    public static function write($name, $data){
        file_put_contents(APP_PATH.'/cache//'.$name.'.cache', serialize($data));
    }
    /**
     * 读取缓存.cache
     * @param string $name 缓存文件名
     * @return data 缓存数据
     */
    public static function read($name){
        $file_name = APP_PATH.'/cache//'.$name.'.cache';
        return unserialize(fread(fopen($file_name, 'r'), filesize($file_name)) );
    }
}