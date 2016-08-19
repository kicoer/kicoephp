<?php
namespace kicoe\Core;

use \kicoe\Core\Config;
use \PDO;

/*
* 原来和Model还是得分开，一个用单例一个给继承
*/
class Db
{
	private static $instance;

	public static function connect()
	{
		//单例模式下只使用一个数据库连接
		if(is_null(self::$instance)){ 
			$db_config = Config::config_prpr('db');
			try {
	            $dsn = sprintf("mysql:host=%s;dbname=%s;charset=utf8", $db_config['hostname'], $db_config['database']);
	            self::$instance = new PDO($dsn, $db_config['username'], $db_config['password'], array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC));
	        } catch (PDOException $e) {
	            exit('PDO错误: ' . $e->getMessage());
	        }
	    }
	    return self::$instance;
	}
}