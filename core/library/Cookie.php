<?php

namespace kicoe\Core;

class Cookie{

    public static function get($name = '', $default = '') {
        if ($name === '') {
            return $_COOKIE;
        }
        if (isset($_COOKIE[$name])) {
            return $_COOKIE[$name];
        }
        return $default;
    }

    public static function set($name, $value, $expire = 0, $path = '/', $domain = null, $secure = false, $httponly = false) {
        $expire = $expire > 0 ? $expire + time() : 0;
        return setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
    }

    public static function del($name, $path = '/', $domain = null, $secure = false, $httponly = false) {
        unset($_COOKIE[$name]);
        return self::set($name, null, -86400, $path, $domain, $secure, $httponly);
    }

}