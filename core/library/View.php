<?php
//视图类

namespace kicoe\Core;

use \kicoe\Core\Exception;

class View
{
    //视图实例
    protected static $instance;

    /**
     * 单例
     * @return $this
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 展示页面
     * @param string $path 自定义加载路径
     * @param array $variables 传递的值数组指针
     * @throws
     */
    public function show($path, &$variables)
    {
        extract($variables, EXTR_SKIP);
        if (file_exists(APP_PATH.'view/'.$path.'.php')) {
            // 加载用户自定义视图路径
            include APP_PATH.'view/'.$path.'.php';
        } else {
            throw new Exception('view file not find', $path);
        }
    }

    /**
     * 发送json格式数据
     * @param $date
     */
    public function json(&$date) {
        Response::getInstance()->json($date);
    }

}
