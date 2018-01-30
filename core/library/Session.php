<?php
// Session类

namespace kicoe\Core;

use \kicoe\Core\Exception;

class Session
{
    function __construct()
    {
        !isset($_SESSION) && session_start();
    }

    public function __set($key, $val)
    {
        $_SESSION[$key] = $val;
    }

    public function __get($key)
    {
        return $_SESSION[$key];
    }

    public function has($key)
    {
        return isset($_SESSION[$key]);
    }

    public function remove($key)
    {
        unset($_SESSION[$key]);
    }

    public function destroy()
    {
        session_unset();
        session_destroy();
    }

    public function clear()
    {
        $_SESSION = [];
    }
}