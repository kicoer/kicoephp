<?php

namespace kicoe\Core;

use \kicoe\Core\Exception;
use \kicoe\Core\Config;

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
        $controllerName = 'Index';
        $action = 'index';
        if (!empty($_GET['k'])) {
            $url = trim($_GET['k'],'/');
            $urlArray = explode('/', $url);
            $route_conf = Config::config_prpr('route');
            $route_action = isset($urlArray[1])?$urlArray[1]:'index';
            if( !empty($route_conf) && array_key_exists($urlArray[0].'/'.$route_action, $route_conf) ) {
                // 从匹配的路由表中匹配
                $sele_conf = explode('/', $route_conf[$urlArray[0].'/'.$route_action]);
                $controllerName = ucfirst($sele_conf[0]);
                $action = $sele_conf[1];
            } else {
                // 将控制器首字母转换大写
                $controllerName = ucfirst($urlArray[0]);
                // 获取动作名
                $action = empty($urlArray[1]) ? 'index' : $urlArray[1];
            }
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
     * 将上传文件拷贝到。。。
     * @param string $file_n 上传文件表单名
     * @param string $file 拷贝文件至服务器public的路径
     * @param string $arg 文件名或诸多限制 size(KB) type name
     */
    public function fileCp($file_n, $path, $arg = array())
    {
        $cp_path = PUB_PATH.$path;
        $file = $_FILES[$file_n];
        if (!$file) {
            throw new Exception("文件上传错误", $file_n . " 文件不存在");
        }
        $file_name = $file['name'];
        if (!empty($arg)) {
            if (isset($arg['size']) && $file['size'] > $arg['size']*1000 ) {
                throw new Exception("文件上传错误", '上传文件大于 '.$arg['size'].' KB');
            }
            if (isset($arg['type'])) {
                if(!in_array($file['type'], $arg['type'])){
                    throw new Exception("文件上传错误", '文件类型 '.$file['type'].' 不符合');
                }
            }
            if (is_set($arg['name'])) {
                $file_name = $arg['name'].$file['type'];
            }
        }
        if (file_exists($cp_path. $file_name)){
            throw new Exception("文件上传错误", $file_name." already exists. ");
        } else {
            move_uploaded_file($file["tmp_name"], $cp_path.$file_name);
        }
    }

}