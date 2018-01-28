<?php

namespace kicoe\Core;

use kicoe\Core\Route;
use kicoe\Core\Exception;
use kicoe\Core\File;

/*
 * 请求类，用于保存客户端请求数据
 * 也可以设置get/post的过滤器等
 */
class Request
{
    protected static $_instance;

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
        Route::reflec();
    }

    /** 
     * 获取控制器名
     */
    public function getController()
    {
        return Route::getController();
    }
    /**
     * 获取动作名
     */
    public function getAction()
    {
        return Route::getAction();
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
            $this->get[$index] = $_GET[$index];
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
            $this->post[$index] = $_POST[$index];
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
        $post_val = $this->post($index);
        if ($post_val !== false && $vali_arr !== NULL) {
            if(isset($vali_arr['len']) && strlen(strval($post_val)) > $vali_arr['len']){
                throw new Exception("字符长度超出 ", 'str overflow : '.$vali_arr['len']);
            }
            if(isset($vali_arr['reg']) && !preg_match($vali_arr['reg'], $post_val)){
                throw new Exception("正则匹配失败 ", $vali_arr['reg']);
            }
        }
        return $post_val;
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