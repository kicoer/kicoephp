<?php
// -- 数据库的查询构造器
// -- 要优雅

namespace kicoe\Core;

use kicoe\Core\Secret\Moo;

class Query extends Moo
{
    // 单例
    private static $instance; 

    /**
     * 设置表名与数据库连接，和laravel一样啦
     */
    public static function table($tablename)
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
            //获取数据库连接实例
            self::$instance->db_instance = Db::connect();
        }
        self::$instance->resetV();
        self::$instance->table = $tablename;
        return self::$instance;
    }

    /**
     * 构造where
     * @param 
     * @return obj 自身实例
     */
    public function where($arg1, $arg2, $arg3 = false)
    {
        $this->wh($arg1, $arg2, $arg3, 'and');
        return $this;
    }

    /**
     * 构造orwhere
     * @return 同上
     */
    public function orWhere($arg1, $arg2, $arg3 = false)
    {
        $this->wh($arg1, $arg2, $arg3, 'or');
        return $this;
    }

}