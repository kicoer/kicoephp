<?php
namespace kicoe\Core;

use \kicoe\Core\Db;
use \kicoe\Core\Exception;

/**
* 数据库的查询构造器
* 要优雅，不要污
*/
class Query
{
    // 单例
    private static $instance; 
    // 数据库连接实例
    private $db_instance;
    // 构造的查询语句
    private $statement;
    // 保存的表名
    private $table;
    // 保存的where数据
    private $where;
    // 保存的pdo参数绑定数据
    private $Pdo_bind_data = array();
    // 保存的pdo参数绑定计数
    private $Pdo_bind_count;
    // 保存的order by语句
    private $Order_by;
    // 保存的LIMIT语句
    private $limit;

    private function __construct(){}
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
        self::$instance->statement = '';
        self::$instance->table = $tablename;
        self::$instance->where = '';
        self::$instance->Order_by = '';
        self::$instance->limit = '';
        self::$instance->Pdo_bind_data = array();
        self::$instance->Pdo_bind_count = 0;
        return self::$instance;
    }

    /**
     * 构造where
     * @param 可惜没有重载，那就自己定义吧
     * @return 返回自身实例
     */
    public function where($arg1, $arg2, $arg3 = False)
    {
        if ($this->where == '') {
            $start_where = 'where ';
        } else {
            $start_where = ' and ';
        }
        if ($arg3 == False && !in_array($arg2, array('=','!=','<>','<','>','<=','>=','BETWEEN','LIKE','IN'))) {
                $this->Pdo_bind_count++;
                $creat_where = '`'.$arg1.'` = :wh'.$this->Pdo_bind_count;
                $this->Pdo_bind_data[':wh'.$this->Pdo_bind_count] = $arg2;
        } else {
                $this->Pdo_bind_count++;
                $creat_where = '`'.$arg1.'` '.$arg2.' :wh'.$this->Pdo_bind_count;
                $this->Pdo_bind_data[':wh'.$this->Pdo_bind_count] = $arg3;
        }
        $this->where .= ($start_where.$creat_where);
        return $this;
    }

    /**
     * 构造orwhere
     * 同上,改or
     */
    public function orwhere($arg1, $arg2, $arg3 = False)
    {
        if ($this->where == '') {
            $start_where = 'where ';
        } else {
            $start_where = ' or ';
        }
        if ($arg3 == False && !in_array($arg2, array('=','!=','<>','<','>','<=','>=','BETWEEN','LIKE','IN'))) {
                $this->Pdo_bind_count++;
                $creat_where = '`'.$arg1.'` = :wh'.$this->Pdo_bind_count;
                $this->Pdo_bind_data[':wh'.$this->Pdo_bind_count] = $arg2;
        } else {
                $this->Pdo_bind_count++;
                $creat_where = '`'.$arg1.'` '.$arg2.' :wh'.$this->Pdo_bind_count;
                $this->Pdo_bind_data[':wh'.$this->Pdo_bind_count] = $arg3;
        }
        $this->where .= ($start_where.$creat_where);
        return $this;
    }

    /**
     * 构造order by语句
     * @param string $by 要排序的列
     * @param string $type asc/desc 排序手段
     */
    public function order($by, $type = 'asc')
    {
        $type = strtolower($type);
        if ($type == 'asc' || $type == 'desc') {
            if ($this->Order_by == '') {
                $this->Order_by = 'order by '.$by.' '.$type;
            } else {
                $this->Order_by .= (', '.$by.' '.$type);
            }
        }
        return $this;
    }

    /**
     * 构造limit语句
     * @param string $index 要开始的位置
     * @param string $number 要获取的数量
     */
    public function limit($index, $number)
    {
        $this->Pdo_bind_count++;
        $this->limit = ' limit :li'.$this->Pdo_bind_count;
        $this->Pdo_bind_data[':li'.$this->Pdo_bind_count] = $index;
        $this->Pdo_bind_count++;
        $this->limit .= (',:li'.$this->Pdo_bind_count);
        $this->Pdo_bind_data[':li'.$this->Pdo_bind_count] = $number;
        return $this;
    }

    /**
     * 从当前条件查询语句
     * @param string $data 要查询的条目。不是数组的话查询这一个
     * @return 数组
     */
    public function select($data = '*')
    {
        //构造查询变量
        if (is_array($data)) {
            $select = "select ".implode(',', $data);
        } else {
            $select = "select ".$data;
        }
        $this->statement = sprintf("%s from `%s` ", $select, $this->table).$this->where.$this->Order_by.$this->limit;
        return $this->bind_prpr()->fetchAll();
    }

    /**
     * 从当前条件删除语句
     * @return 删除行数
     */
    public function delete()
    {
        //没有执行where就delete,则删除全部表。。。天啊真可怕
        $this->statement = sprintf("delete from `%s` ", $this->table).$this->where.$this->Order_by.$this->limit;
        return $this->bind_prpr()->rowCount();
    }

    /**
     * 增加数据
     * @param array $data 要插入的键值对数组/或者键数组
     * @param array $v_data 要插入的值数组
     */
    public function insert($data, $v_data = NULL)
    {
        if (is_null($v_data)) {
            $begin = array();
            $end = array();
            foreach ($data as $k => $v) {
                $begin[] = '`'.$k.'`';
                $this->Pdo_bind_count++;
                $end[] = ':in'.$this->Pdo_bind_count;
                $this->Pdo_bind_data[':in'.$this->Pdo_bind_count] = $v;
            }
            $insert = sprintf("(%s) values (%s)", implode(',', $begin), implode(',', $end));
            $this->statement = sprintf("insert into %s %s", $this->table, $insert);
        } else {
            $begin = $data;
            $end = array();
            foreach ($v_data as $value) {
                $end_l = array();
                foreach ($value as $v) {
                    $this->Pdo_bind_count++;
                    $end_l[] = ':in'.$this->Pdo_bind_count;
                    $this->Pdo_bind_data[':in'.$this->Pdo_bind_count] = $v;
                }
                $end[] = '('.implode(',', $end_l).')';

            }
            $insert = sprintf("(%s) values %s", implode(',', $begin), implode(',', $end));
            $this->statement = sprintf("insert into %s %s", $this->table, $insert);
        }
        return $this->bind_prpr()->rowCount();
    }

    /**
     * 更新数据
     * @param array $data 要修改的键值对数组
     * @return 影响行数
     */
    public function update($data)
    {
        $fields = array();
        foreach ($data as $key => $value) {
            $this->Pdo_bind_count++;
            $fields[] = sprintf("`%s` = :up%s", $key, $this->Pdo_bind_count);
            $this->Pdo_bind_data[':up'.$this->Pdo_bind_count] = $value;
        }
        $update = implode(',', $fields);
        # 没有where的话,修改所有数据
        $this->statement = sprintf("update `%s` set %s ",$this->table,$update).$this->where.$this->Order_by.$this->limit;
        return $this->bind_prpr()->rowCount();
    }

    /**
     * 自定义查询，支持参数绑定
     * @param string $my_statement 自定义查询语句
     * @param array $bind_arg 参数绑定数组
     * @return 返回查询结果数组
     */
    public static function query($my_statement,$bind_arg = NULL)
    {
        //获取数据库连接实例
        $db_instance = Db::connect();
        $sta = $db_instance->prepare($my_statement);
        if (!is_null($bind_arg)) {
            foreach ($bind_arg as $key => $value) {
                if (is_numeric($key)) {
                    $key++;
                }
                $sta->bindValue($key,$value);
            }
        }
        $sta->execute();
        if ($sta->errorCode() != '00000') {
            # 进行错误处理
            throw new Exception("数据库执行错误", implode('<br>',$sta->errorInfo()));
        }
        return $sta->fetchAll();
    }

    /**
     * 自定义查询，支持参数绑定
     * @param string $my_statement 自定义查询语句
     * @param array $bind_arg 参数绑定数组
     * @return 返回影响行数
     */
    public static function execute($my_statement,$bind_arg = NULL)
    {
        //获取数据库连接实例
        $db_instance = Db::connect();
        $sta = $db_instance->prepare($my_statement);
        if (!is_null($bind_arg)) {
            foreach ($bind_arg as $key => $value) {
                if (is_numeric($key)) {
                    $key++;
                }
                $sta->bindValue($key,$value);
            }
        }
        $sta->execute();
        if ($sta->errorCode() != '00000') {
            # 进行错误处理
            throw new Exception("数据库执行错误", implode('<br>',$sta->errorInfo()));
        }
        return $sta->rowCount();
    }

    /**
     * 执行参数绑定
     * @return PDO 查询结果后对象实例
     */
    private function bind_prpr()
    {
        $sta = $this->db_instance->prepare($this->statement);
        if (count($this->Pdo_bind_data)) {
            foreach ($this->Pdo_bind_data as $key => $value) {
                if (is_numeric($value)) {
                    $sta->bindValue($key,$value,\PDO::PARAM_INT);
                } else {
                    //原来bindParam会绑定变量而不是值，害的我差点以为要用到闭包
                    $sta->bindValue($key,$value);
                }
            }
        }
        $sta->execute();
        if ($sta->errorCode() != '00000') {
            # 进行错误处理
            throw new Exception("数据库执行错误", implode('<br>',$sta->errorInfo()));
        }
        return $sta;
    }

}