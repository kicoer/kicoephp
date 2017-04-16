<?php

/*
 * 简单的路由处理类
 */

namespace kicoe\Core;

use \kicoe\Core\Request;
use \ReflectionClass;

class Route{

    // 控制器
    private static $controller = 'Index';
    // 操作
    private static $action = 'index';
    // 参数
    private static $query = [];

    /**
     * 获取控制器名
     * @return string 控制器名
     */
    public static function getController()
    {
        return self::$controller;
    }

    /**
     * 获取操作名
     * @return string 操作
     */
    public static function getAction()
    {
        return self::$action;
    }

    /**
     * 初始化路由
     * @param string $url ?k=
     */
    public static function init($url)
    {
        $url = trim($url,'/');
        $urlArray = explode('/', $url);
        $route_conf = Config::config_prpr('route');
        $route_action = isset($urlArray[1])?$urlArray[1]:'index';
        if( isset($route_conf) && array_key_exists($urlArray[0].'/'.$route_action, $route_conf) ) {
            // 从匹配的路由表中匹配
            $sele_conf = explode('/', $route_conf[$urlArray[0].'/'.$route_action]);
            self::$controller = ucfirst($sele_conf[0]);
            self::$action = $sele_conf[1];
        } else {
            // 将控制器首字母转换大写
            self::$controller = ucfirst($urlArray[0]);
            // 获取动作名
            self::$action = isset($urlArray[1]) ? $urlArray[1] : 'index';
        }
        //获取URL参数
        if ( isset($urlArray[2]) ) {
            self::$query = array_slice($urlArray,2);
        }        
    }

    /**
     * 利用反射执行
     */
    public static function reflec()
    {
        $controller = 'app\controller\\'. self::$controller;
        // 获取控制器类的反射实例
        $controllerReflec = new ReflectionClass($controller);
        if ($controllerReflec->isSubclassOf('kicoe\Core\Controller')) {
            $actionReflec = $controllerReflec->getMethod(self::$action);
            $params = $actionReflec->getParameters();
            $i = 0;
            $pas = [];
            // 注入依赖
            foreach ($params as $pa) {
                if ($class = $pa->getClass()) {
                    if('kicoe\Core\Request' === $class->getName())
                        $pas[] = Request::getInstance();
                } else {
                    if (isset(self::$query[$i])) {
                        $pas[] = self::$query[$i];
                    }
                    $i++;
                }
            }
            $actionReflec->invokeArgs($controllerReflec->newInstance(), $pas);
        }
    } 

}