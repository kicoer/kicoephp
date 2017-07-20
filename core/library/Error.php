<?php
namespace kicoe\Core;

use \kicoe\Core\Exception;

// 错误处理类
class Error
{
    /**
     * 注册错误处理
     */
    public static function register()
    {
        //开启所有错误报告
        error_reporting(E_ALL);
        // 注册自定义错误处理
        set_error_handler([__CLASS__,'onlymy_error_handler']);
        // 注册脚本结束后的处理
        register_shutdown_function([__CLASS__, 'onlymy_shutdown_handler']);
        // 注册自定义异常处理
        set_exception_handler([__CLASS__, 'onlymy_exception_handler']);
    }

    /**
     * 错误处理
     * @param type $error_level     错误级别
     * @param type $error_message   错误信息
     * @param type $error_file      可选 错误文件
     * @param type $error_line      可选 错误行
     * @param type $error_context   可选。规定一个数组，包含了当错误发生时在用的每个变量以及它们的值。
     * @throws 自定义异常
     */
    public static function onlymy_error_handler($error_level, $error_message, $error_file, $error_line, $error_context)
    {
        throw new Exception('系统错误 : ',$error_message,$error_file,$error_line);
    }

    /**
     * 通过error_get_last()判断是否为致命错误
     * @throws 自定义异常
     */
    public static function onlymy_shutdown_handler()
    {
        if (!is_null($error = error_get_last()) && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE])) {
            $Exc_instance = new Exception('致命错误 : '.$error['type'], $error['message'], $error['file'],$error['line']);
            // 翻了下tp源码，总算知道原来这里的异常得交给别的函数抛出啊
            self::onlymy_exception_handler($Exc_instance);
        }
    }

    /**
     * 自定义异常处理
     * 将 所有错误的抛出都当异常处理了 
     * @param $e 可抛出的异常
     */
    public static function onlymy_exception_handler($e)
    {
        if ($e instanceof Exception) {
            $e->show(); 
        } else {
            $My_exception = new Exception("原生错误 : ".get_class($e), $e->getMessage(), $e->getFile(), $e->getLine());
            $My_exception->show();
        }
    }

}