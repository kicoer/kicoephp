<?php
//视图类
namespace kicoe\Core;

use \kicoe\Core\Request;
use \kicoe\Core\Exception;

class View
{

    protected static $instance;    //视图实例

    //获取视图实例
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 展示页面
     * @param string $path 自定义加载路径
     */
    public function show($path,$variables)
    {
        extract($variables);
        // 获取控制器和操作名
        $controller = Request::getInstance()->getController();
        $action = Request::getInstance()->getAction();
        if ($path!='') {
            if (file_exists(APP_PATH.'view/'.$path.'.php')) {
                // 加载用户自定义视图路径
                include APP_PATH.'view/'.$path.'.php';
            } else {
                throw new Exception('视图路径错误',$path.' 视图路径未找到');
            }

        } else {
            if (file_exists(APP_PATH.'view/'.$controller.'/'.$action.'.php')) {
                // 加载视图文件 /app/view/控制器名/操作名.php
                include APP_PATH.'view/'.$controller.'/'.$action.'.php';
            } else {
                throw new Exception('视图路径错误',$controller.'/'.$action.' 默认视图路径未找到');
            }
        }

    }

}