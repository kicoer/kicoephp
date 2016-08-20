<?php

namespace kicoe\Core;

use \kicoe\Core\Resource;
use \kicoe\Core\Exception;

class Link
{
	//开始应用
	public static function start()
    {
        //注册路由
		self::route();
	}
	// 路由处理，将所有链接都直接指向了index.php?K=
    protected static function route()
    {
    	$controllerName = 'Index';
        $action = 'index';
        if (!empty($_GET['k'])) {
            $url = trim($_GET['k'],'/');
            $urlArray = explode('/', $url);
            // 将控制器首字母转换大写
            $controllerName = ucfirst($urlArray[0]);       
            // 获取动作名
            $action = empty($urlArray[1]) ? 'index' : $urlArray[1];       
            //获取URL参数
            $queryString = empty($urlArray[2]) ? array() : array_slice($urlArray,2);
        }
        $queryString = empty($queryString) ? array() : $queryString;
        $controller = 'app\controller\\'.$controllerName;
		if(class_exists($controller)){
            //将控制器名存入资源备用
            Resource::getInstance()->controller = $controllerName;
			// 实例化控制器,传入控制器和操作
			$controllerPoi = new $controller();
		} else {
			throw new Exception("路由错误",$controllerName . " 控制器不存在");
		}
        // 如果控制器和动作存在，这调用并传入URL参数
        if ((int)method_exists($controllerPoi, $action)) {
            //存储操作名到资源
            Resource::getInstance()->action = $action;
        	call_user_func_array(array($controllerPoi,$action),$queryString);
        } else {
            throw new Exception("路由错误",$action . " 操作不存在");
        }
    }
} 