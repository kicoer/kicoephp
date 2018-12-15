<?php
// -- 超级简单的文件缓存 
// -- 正确的扩展做法是使用一个工厂生产文件或Redis类型的缓存对象

namespace kicoe\Core;

use kicoe\Core\Config;

class Cache
{

    /**
     * 获取文件全名
     * @param string $key 文件名
     * @return string 文件全路径
     * @throws
     */
    public static function getFile($key)
    {
        $file = APP_PATH.Config::prpr('cc').'/'.$key;
        if (!is_file($file)) {
            touch($file, 0755, true);
        }
        return $file;
    }

    public static function has($key)
    {
        return is_file(self::getFile($key));
    }

    /**
     * 写入缓存.cache
     * @param string $key 缓存key
     * @param array $data 要序列化的数据
     */
    public static function write($key, $data)
    {
        file_put_contents(self::getFile($key), serialize($data), LOCK_EX);
    }

    /**
     * 读取缓存.cache
     * @param string $key 缓存key
     * @return array 缓存数据
     */
    public static function read($key)
    {
        return unserialize(file_get_contents(self::getFile($key)));
    }

}
