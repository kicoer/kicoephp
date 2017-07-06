<?php
namespace kicoe\Core;

use \kicoe\Core\Config;
use \kicoe\Core\Exception;
use \PDO;

/**
 * Db数据库链接的单例
 * 
 */
class Db
{
    //自身实例
    private static $instance;

    public static function connect()
    {
        //单例模式下只使用一个数据库连接
        if(is_null(self::$instance)){ 
            $db_config = Config::prpr('db');
            try {
                $dsn = "mysql:host=". $db_config['hostname']. ";dbname=". $db_config['database']. ";charset=utf8";
                self::$instance = new PDO($dsn, $db_config['username'], $db_config['password'], array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC));
            } catch (PDOException $e) {
                
            }
        }
        return self::$instance;
    }

}