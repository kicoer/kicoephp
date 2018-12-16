<?php
// 错误处理类

namespace kicoe\Core;

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
        set_error_handler([__CLASS__,'errorHandler']);
        // 注册脚本结束后的处理
        register_shutdown_function([__CLASS__, 'shutdownHandler']);
        // 注册自定义异常处理
        set_exception_handler([__CLASS__, 'exceptionHandler']);
    }

    /**
     * 错误处理
     * @param string $error_level     错误级别
     * @param string $error_message   错误信息
     * @param string $error_file      可选 错误文件
     * @param string $error_line      可选 错误行
     * @param string $error_context   可选。规定一个数组，包含了当错误发生时在用的每个变量以及它们的值。
     * @throws
     */
    public static function errorHandler($error_level, $error_message, $error_file, $error_line, $error_context)
    {
        throw new Exception('system: ', $error_message, $error_file, $error_line);
    }

    /**
     * 通过error_get_last()判断是否为致命错误
     * @throws
     */
    public static function shutdownHandler()
    {
        if (($error = error_get_last() !== null) &&
            in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE])) {
            $Exc_instance = new Exception('shutdown: '.$error['type'], $error['message'], $error['file'], $error['line']);
            // 翻了下tp源码，总算知道原来这里的异常得交给别的函数抛出啊
            self::exceptionHandler($Exc_instance);
        }
    }

    /**
     * 自定义异常处理
     * 将 所有错误的抛出都当异常处理了 
     * @param $e Exception 可抛出的异常
     */
    public static function exceptionHandler($e)
    {
        if ($e instanceof Exception) {
            $e->show(); 
        } else {
            (new Exception(
                'origin: '.get_class($e),
                $e->getMessage(), 
                $e->getFile(), 
                $e->getLine()
            ))->show();
        }
    }

}
