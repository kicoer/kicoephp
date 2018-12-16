<?php
// 简单的路由处理类

namespace kicoe\Core;

use \ReflectionClass;

class Route{

    // 中间件列表
    // 对了，用swoole载入内存后这一块全要改，当初写的什么蠢代码
    private static $middleware_list = [];

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
     * @throws
     */
    public static function init($url)
    {
        $route_conf = Config::prpr('route');
        $url = trim($url,'/');
        if ($route_conf === []) {
            // 配置为空则自动路由
            $k_arr = explode('/', $url);
            self::$controller = ucfirst($k_arr[0]);
            self::$action = $k_arr[1];
            if ( isset($k_arr[2]) ) {
                self::$query = array_slice($k_arr,2);
            }
        } else {
            // 完全按照配置来
            $key = 'route.cache.php';
            if (!Config::prpr('test') && Cache::has($key)) {
                $route_cache = Cache::read($key);
            } else {
                $route_cache = self::tree($route_conf);
                Cache::write($key, $route_cache);
            }
            // 解析路由
            $url_arr = explode('/', $url);
            foreach ($url_arr as $i => $key) {
                $route_cache = &$route_cache[$key];
                if ($i === count($url_arr)-1 || isset($route_cache['/']) && !isset($route_cache[$url_arr[$i+1]])) {
                    $route_cache = &$route_cache['/'];
                    break;
                }
            }
            if (!isset($route_cache[0])) {
                throw new Exception('route no find', $url, '..../app/config.php', '[route]');
            }
            if (isset($route_cache[2])) {
                self::$middleware_list = $route_cache[2];
            }
            self::$controller = ucfirst($route_cache[0]);
            self::$action = $route_cache[1];
            // 解析参数
            if ($qu_arr = array_slice($url_arr, $i+1)) {
                self::$query = $qu_arr;
            }
        }
    }

    /**
     * 将路由配置解析为树
     * @param array $conf 配置数组
     * @return array 转换树结构
     * @throws
     */
    public static function tree($conf)
    {
        $tree = [];
        $in_node = &$tree;
        foreach ($conf as $key => $value) {
            // 中间件列表
            $mi_list = explode('|', $value);
            $value = array_pop($mi_list);
            $ca_list = explode('@', $value);
            if (!isset($ca_list[1])) {
                $ca_list[1] = 'index';
            }
            if ($mi_list) {
                $ca_list[2] = $mi_list;
            }
            if (count($ca_list)<2) {
                throw new Exception('route config export error', $value, '..../app/config.php', '[route]');
            }
            $route_arr = explode('/', trim($key, '/'));
            foreach ($route_arr as $v) {
                if (isset($in_node[$v])) {
                    $in_node = &$in_node[$v];
                } else {
                    $in_node[$v] = [];
                    $in_node = &$in_node[$v];
                }
            }
            $in_node['/'] = $ca_list;
            $in_node = &$tree;
        }
        return $tree;
    }

    /**
     * 利用反射检查
     * @throws
     */
    public static function reflec()
    {

        $controller = 'app\controller\\'. self::$controller;
        $action = self::$action;

        // 执行中间件
        if (self::$middleware_list) {
            $middleware_class_name = Config::prpr('middleware');
            if (!class_exists($middleware_class_name)) {
                throw new Exception('middleware', "{$middleware_class_name} class is not exist", 'config.php');
            }
            $middleware = new $middleware_class_name();
            foreach (self::$middleware_list as $mw) {
                if (!method_exists($middleware, $mw)) {
                    return;
                }
                if (!$middleware->$mw()) {
                    throw new Exception('middleware', "{$mw} not pass", "{$controller}::{$action}");
                }
            }
        }
        // 获取控制器类的反射实例
        $controller_ref = new ReflectionClass($controller);
        if ($controller_ref->isSubclassOf('kicoe\Core\Controller')) {
            $action_ref = $controller_ref->getMethod(self::$action);
            $params = $action_ref->getParameters();
            $i = 0;
            $pas = [];
            // 注入依赖
            foreach ($params as $pa) {
                if ($class = $pa->getClass()) {
                    if ('kicoe\Core\Request' === $class->getName()) {
                        $pas[] = Request::getInstance();
                    } else if ('kicoe\Core\Response' === $class->getName()) {
                        $pas[] = Response::getInstance();
                    }
                } else {
                    if (isset(self::$query[$i])) {
                        $pas[] = self::$query[$i];
                    }
                    $i++;
                }
            }
            // 还是不用反射执行吧
            call_user_func_array([new $controller, $action], $pas);
        }
    }

}