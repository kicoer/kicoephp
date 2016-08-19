<?php

namespace kicoe\Core;

/**
* 突发奇想，使用一个单利模式的类来收集、保存所需资源
* 
*/
class Resource
{
	//开启单例模式
	protected static $_instance;

	private function __construct(){}

	//获得类的实例
    public static function getInstance(){
        if(!isset(self::$_instance)){
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    //控制器
    public $controller;
    //操作
    public $action;

}