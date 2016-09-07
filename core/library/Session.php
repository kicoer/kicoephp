<?php

namespace kicoe\Core;

use \kicoe\Core\Exception;

/**
 * Session类，用于管理Session
 */
class Session
{
	/**
	 * session初始化
	 */
	public static function init()
	{
		session_start();
	}

	/**
	 * 获取session对象
	 * @param string $name 获取的session元素名
	 * @return mixed
	 */
	public static function get($name = '')
	{
		!isset($_SESSION) && self::init();
		if ('' == $name) {
			# 获取全部的session
			$value = $_SESSION;
		} else {
			$value = isset($_SESSION[$name]) ? $_SESSION[$name] : null;
		}
		return $value;
	}

	/**
	 * 设置session对象
	 * @param string $name 要设置的session元素名
	 * @param mixed $value 要设置的对应值
	 */
	public static function set($name, $value = '')
	{
		!isset($_SESSION) && self::init();
		$_SESSION[$name] = $value;
	}

	/**
	 * 删除session对象
	 * @param string $name 要删除的session元素名
	 */
	public static function delete($name)
	{
		!isset($_SESSION) && self::init();
		unset($_SESSION[$name]);
	}

	/**
     * 判断session数据
     * @param string        $name session名称
     * @return bool
     */
    public static function has($name)
    {
        !isset($_SESSION) && self::init();
        return isset($_SESSION[$name]);
    }

	/**
	 * 清空session对象
	 */
	public static function clear($name)
	{
		!isset($_SESSION) && self::init();
		$_SESSION = [];
	}

    /**
     * 启动session
     * @return void
     */
    public static function start()
    {
        session_start();
    }

    /**
     * 销毁session
     * @return void
     */
    public static function destroy()
    {
        if (!empty($_SESSION)) {
            $_SESSION = [];
        }
        session_unset();
        session_destroy();
    }

}