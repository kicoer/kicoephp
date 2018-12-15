<?php
// Session类

namespace kicoe\Core;

use \kicoe\Core\Exception;

class Session
{
    public static function init()
    {
        !isset($_SESSION) && session_start();
    }

    public static function set($key, $val)
    {
        self::init();
        $_SESSION[$key] = $val;
    }

    public static function get($key)
    {
        self::init();
        return $_SESSION[$key];
    }

    public static function has($key)
    {
        self::init();
        return isset($_SESSION[$key]);
    }

    public static function remove($key)
    {
        self::init();
        unset($_SESSION[$key]);
    }

    public function clear()
    {
        self::init();
        $_SESSION = [];
    }

    public static function destroy()
    {
        session_unset();
        session_destroy();
    }

}