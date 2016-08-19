<?php
//视图类
namespace kicoe\Core;

use \kicoe\Core\Resource;

class View
{
	protected $variables = array();

	/** 分配变量 **/
    function assign($name, $value)
    {
        $this->variables[$name] = $value;
    }

    function fetch()
    {
    	//将数组直接解析成变量。。。
    	extract($this->variables);

    	//获取控制器和操作名
    	$controller = Resource::getInstance()->controller;
    	$action = Resource::getInstance()->action;
    	if (file_exists(APP_PATH.'view//'.$controller.'/'.$action.'.php')) {
    		//加载视图文件 /app/view/控制器名/操作名.php
    		include APP_PATH.'view//'.$controller.'/'.$action.'.php';
    	} else {
    		exit($controller.'/'.$action.'.php 视图不存在');
    	}

    }

}