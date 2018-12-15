<?php
//所有控制器类基类

namespace kicoe\Core;

use kicoe\Core\View;
use kicoe\Core\Request;

class Controller
{
    //视图类
    protected $view;

    //需要传递给视图的值
    protected $var = array();

    public function __construct()
    {
        $this->view = View::getInstance();
    }

    /**
     * 给页面赋值
     * @param string|array $name  赋值给页面的变量名或键值对数组
     * @param string $value     赋值给该变量名的值
     */
    public function assign($name='', $value='')
    {
        if($value === ''){
            $this->var = array_merge($this->var, $name);
        } else {
            $this->var[$name] = $value;
        }
    }

    /**
     * 加载页面
     * @param string $path  自定义路径或空
     */
    public function show($path = '')
    {
        if ($path === '') {
            // 获取控制器和操作名
            $re = Request::getInstance();
            $this->view->show($re->getController().'/'.$re->getAction(), $this->var);
        } else {
            // 加载自定义页面
            $this->view->show($path, $this->var);
        }
    }

    /**
     * die
     */
    protected function shutdown($str)
    {
        die($str);
    }

}
