<?php
// 简单的路由处理类

namespace kicoe\Core;

use kicoe\Core\Request;
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
        $route_conf = Config::prpr('route');
        $url = trim($url,'/');
        if ($route_conf === []) {
            // 配置为空则自动路由
            $k_arr = explode('/', $url);
            self::$controller = ucfirst($k_arr[0]);
            self::$action = isset($k_arr[1])?$k_arr[1]:'index';
            if ( isset($k_arr[2]) ) {
                self::$query = array_slice($k_arr,2);
            }
        } else {
            // 完全按照配置来
            $key = 'route.cache.php';
            if (Cache::has($key) && !Config::prpr('test')) {
                $route_cache = Cache::read($key);
            } else {
                $route_cache = self::tree($route_conf);
                Cache::write($key, $route_cache);
            }
            // 解析路由
            for ($i=0; $i < strlen($url); $i++) { 
                $route_cache = $route_cache[$url[$i]];
                if (is_string($route_cache)) {
                    $ac = explode('@', $route_cache);
                    break;
                }
            }
            self::$controller = ucfirst($ac[0]);
            self::$action = $ac[1];
            // 解析参数
            if ($qu_str = substr($url, $i+2)) {
                if ($qu_arr = explode('/', $qu_str)) {
                   self::$query = $qu_arr;
                }
            }
        }
    }

    /**
     * 将路由配置缓存为树
     */
    public static function tree($conf)
    {
        $tree = [];
        $len = 0;
        foreach (array_keys($conf) as $key => $value) { 
            if (strlen($value) > $len) {
                $len = strlen($value);
            }
        }
        // route_index => tree_index
        $index_list = [];
        for ($i=0; $i<$len; $i++) {
            $ki = 0;
            foreach ($conf as $key => $value) {
                if (isset($key[$i])) {
                    if (!isset($index_list[$ki])) {
                        $index_list[$ki] = &$tree;
                    }
                    if (!isset($index_list[$ki][$key[$i]])) {
                        $index_list[$ki][$key[$i]] = [];
                    }
                    $index_list[$ki] = &$index_list[$ki][$key[$i]];
                    if (strlen($key) === $i+1) {
                        $index_list[$ki] = $value;
                    }
                }
                $ki++;
            }
        }
        return $tree;
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