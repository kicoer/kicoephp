<?php

namespace kicoe\Core;

use \kicoe\Core\Exception;

/*
 * 请求类，用于保存客户端请求数据
 * 也可以设置get/post的过滤器等
 */
class Request
{
	protected static $_instance;

	private $_controller;

	private $_action;

	private function __construct(){}

	/**
	 * 获取单例
	 * @return Request
	 */
	public static function getInstance()
	{
		if (!isset(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * 注册路由处理
	 */
	public function route()
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
            $this->_controller = $controllerName;
			// 实例化控制器,传入控制器和操作
			$controllerPoi = new $controller();
		} else {
			throw new Exception("路由错误",$controllerName . " 控制器不存在");
		}
        // 如果控制器和动作存在，这调用并传入URL参数
        if ((int)method_exists($controllerPoi, $action)) {
            $this->_action = $action;
            //主要函数
        	call_user_func_array(array($controllerPoi,$action),$queryString);
        } else {
            throw new Exception("路由错误",$action . " 操作不存在");
        }
	}

	/** 
	 * 获取控制器名
	 */
	public function getController()
	{
		return $this->_controller;
	}
	/**
	 * 获取动作名
	 */
	public function getAction()
	{
		return $this->_action;
	}
}