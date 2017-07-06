<?php

namespace kicoe\Core;

/**
 * 缓存类，序列化
 * 测试扩展类
 */
class Cache
{
    // 缓存文件路径
    $path = APP_PATH.'/cache//';
    // 缓存文件后缀
    $ex = '.cache';

    /**
     * 写入缓存.cache
     * @param string $name 缓存文件名
     * @param array|obj $data 要序列化的数据
     */
    public static function write($name, $data){
        file_put_contents($this->path.$name.$this->ex, serialize($data));
    }
    /**
     * 读取缓存.cache
     * @param string $name 缓存文件名
     * @return data 缓存数据
     */
    public static function read($name){
        $file_name = $this->path.$name.$this->ex;
        return unserialize(fread(fopen($file_name, 'r'), filesize($file_name)) );
    }
}