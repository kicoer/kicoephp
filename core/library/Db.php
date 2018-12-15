<?php
// Mysql连接单例

namespace kicoe\Core;

use kicoe\Core\Config;
use kicoe\Core\Exception;
use \PDO;

class Db
{
    // PDO连接对象
    private static $instance;

    /**
     * 返回Pdo连接对象函数
     * @return PDO PDO连接对象
     * @throws \kicoe\Core\Exception
     */
    public static function connect()
    {
        if(self::$instance === null){
            // PDO config
            $db_config = Config::prpr('db');
            try {
                $dsn = 'mysql:host='. $db_config['hostname']. ';dbname='. $db_config['database']. ';charset=utf8';
                self::$instance = new PDO($dsn, $db_config['username'], $db_config['password'], array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC));
            } catch (\PDOException $e) {
                
            }
        }
        return self::$instance;
    }
}

