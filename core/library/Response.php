<?php

namespace kicoe\Core;


class Response
{
    protected static $_instance;

    private function __construct(){}

    /**
     * 获取单例
     * @return Response
     */
    public static function getInstance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * 设置头信息
     * @param string $key
     * @param string $value 键值对
     * @return $this
     */
    public function header($key, $value)
    {
        \header($key.': '.$value);
        return $this;
    }

    /**
     * 设置响应状态码
     * @param int $code 状态码
     * @return $this
     */
    public function status($code)
    {
        \header('HTTP/1.1 '.$code);
        return $this;
    }

    /**
     * @param $data
     * @return $this
     */
    public function json(&$data)
    {
        $this->header('Content-type', 'text/json')->send(json_encode($data));
        return $this;
    }

    /**
     * 所有的输出都将代理到这
     * @param string 显示内容
     */
    public function send($str)
    {
        echo $str;
    }

    public function redirect($url)
    {
        $this->header('Location', $url);
    }

    /**
     * 宕机
     * @param $code
     * @param $info
     */
    public function shutdown($code, $info = NULL)
    {
        if (is_string($info)) {
            $this->status($code)->send($info);
        } else {
            $this->status($code)->json($info);
        }
        die();
    }

}