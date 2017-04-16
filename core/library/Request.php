<?php

namespace kicoe\Core;

use \kicoe\Core\Route;
use \kicoe\Core\Exception;
use \kicoe\Core\Config;
use \kicoe\Core\File;
use \ReflectionClass;

/*
 * 请求类，用于保存客户端请求数据
 * 也可以设置get/post的过滤器等
 */
class Request
{
    protected static $_instance;

    private $_controller;

    private $_action;

    private $get;

    private $post;

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
        if ($k = $this->get('k')) {
            Route::init($k);
        }
        $this->_controller = Route::getController();
        $this->_action = Route::getAction();
        Route::reflec();
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

    /**
     * 获取_GET中数据
     * @param string $index _GET数组下标
     * @return _GET中对应数据
     */
    public function get($index)
    {
        if (!isset($this->get[$index])) {
            if (!isset($_GET[$index])) {
                return false;
            }
            if (!get_magic_quotes_gpc()) {
                $this->get[$index] = addslashes($_GET[$index]);
            } else {
                $this->get[$index] = $_GET[$index];
            }
        }
        return $this->get[$index];
    }

    /**
     * 获取_POST中数据
     * @param string $index _POST数组下标
     * @return _POST中对应数据
     */
    public function post($index)
    {
        if (!isset($this->post[$index])) {
            if (!isset($_POST[$index])) {
                return false;
            }
            if (!get_magic_quotes_gpc()) {
                $this->post[$index] = addslashes($_POST[$index]);
            } else {
                $this->post[$index] = $_POST[$index];
            }
        }
        return $this->post[$index];
    }

    /**
     * 表单验证函数 只验证post数据
     * @param string $index _POST数组下标
     * @param array $vali_arr 要验证的规则
     * @param NULL / _POST数据
     * @return 成功验证的数据 / false
     */
    public function validate($index, $vali_arr = NULL)
    {
        if (!isset($this->post[$index])) {
            if (!isset($_POST[$index])) {
                return false;
            }
            if (!get_magic_quotes_gpc()) {
                $this->post[$index] = addslashes($_POST[$index]);
            } else {
                $this->post[$index] = $_POST[$index];
            }
        }
        if ($vali_arr !== NULL) {
            if (isset($vali_arr['len'])) {
                if(strlen(strval($this->post[$index])) > $vali_arr['len']){
                    return false;
                }
            }
            if (isset($vali_arr['reg'])) {
                if(!preg_match($vali_arr['reg'], $this->post[$index])){
                    return false;
                }
            }
        }
        return $this->post[$index];
    }

    /**
     * 获取用户上传文件
     * @param string $name 文件name
     * @return File 继承自splFileInfo的文件处理类
     */
    public function file($name)
    {
        return (new File($_FILES[$name]["tmp_name"], $_FILES[$name]));
    }

}