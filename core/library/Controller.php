<?php
//所有控制器类基类
namespace kicoe\Core;

use \kicoe\Core\View;

class Controller
{
	protected $view;	//视图类

	public function __construct()
	{
		$this->view = new View();
	}

	public function assign($name, $value)
	{
		$this->view->assign($name, $value);
	}

	public function fetch()
	{
		$this->view->fetch();
	}
}