<?php
//所有控制器类基类
namespace kicoe\Core;

use \kicoe\Core\View;

class Controller
{
    protected $view;    //视图类

    //需要传递给视图的值
    protected $variables = array();

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
        if($value == '' && is_array($name)){
            $this->variables = array_merge($this->variables,$name);
        } else {
            $this->variables[$name] = $value;
        }
    }

    public function show($path ='')
    {
        $this->view->show($path,$this->variables);
    }
}