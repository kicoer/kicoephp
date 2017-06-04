<?php
namespace kicoe\Core\Secret;

use \kicoe\Core\Exception;
use \kicoe\Core\Db;

/**
* 数据查询的父类
*/
class Moo
{
    protected $table = '';
    // 数据库链接实例
    protected $db_instance = NULL;
    // 构造的查询语句
    protected $statement = '';
    // where语句
    protected $where = '';
    // 保存的order by语句
    protected $Order_by = '';
    // 保存的LIMIT语句
    protected $limit = '';
    // 保存的pdo参数绑定数据
    protected $Pdo_bind_data = array();
    // 保存的pdo参数绑定计数
    protected $Pdo_bind_count = 0;

    /**
     * 重置初始变量
     */
    protected function resetV()
    {
        $this->statement = '';
        $this->where = '';
        $this->Order_by = '';
        $this->limit = '';
        $this->Pdo_bind_data = array();
        $this->Pdo_bind_count = 0;
    } 

    /**
     * 获得上一条执行语句的id
     */
    public function lastInsertId(){
        return $this->db_instance->lastInsertId();
    }

    /**
     * pdo绑定数据
     * @param string $pre 绑定命名前缀
     * @param  mixed $data 绑定数据
     * @return string 当前绑定名
     */
    protected function pdoBind($pre, $data)
    {
        $pre_name = ':'. $pre;
        $this->Pdo_bind_count++;
        $this->Pdo_bind_data[ $pre_name. $this->Pdo_bind_count] = $data;
        return $pre_name. $this->Pdo_bind_count;
    }

    /**
     * 构造where
     * @param string $in 'or' | 'and'
     */
    protected function wh($arg1, $arg2, $arg3, $in)
    {
        if ($this->where == '') {
            $start_where = 'where ';
        } else {
            $start_where = ' '.$in.' ';
        }
        if ($arg3 == False) {
            $creat_where = '`'.$arg1.'` = '. $this->pdoBind('wh', $arg2);
        } else {
            $sign = strtolower($arg2);
            if (in_array($sign, ['=','!=','<>','<','>','<=','>=','like','not like'])) {
                $creat_where = '`'.$arg1.'` '.$arg2.' '. $this->pdoBind('wh', $arg3);
            } elseif ($sign == 'in' || $sign =='not in' ) {
                // IN 与 NOT IN 处理
                $in_arr = [];
                foreach ($arg3 as $in_key => $in_val) {
                    $in_arr[] = ' '. $this->pdoBind('wh', $in_val);
                }
                $creat_where = '`'.$arg1.'` '.$arg2. ' ('. implode(',', $in_arr). ')';
            } elseif ($sign == 'between' || $sign=='not between' ) {
                // BETWEEN 处理
                $creat_where = '`'.$arg1.'` '.$arg2.' '.$this->pdoBind('wh', $arg3[0]);
                $creat_where .= ' and '. $this->pdoBind('wh', $arg3[1]);
            } else {
                throw new Exception("不允许的sql符号", "<b>$arg2</b>");
            }
        }
        $this->where .= ($start_where.$creat_where);
    }
      
    /**
     * 构造order by语句
     * @param string $by 要排序的列
     * @param string $type asc/desc 排序手段
     * @param obj 自身实例
     */
    public function order($by, $type = 'asc')
    {
        $type = strtolower($type);
        if ($type == 'asc' || $type == 'desc') {
            if ($this->Order_by == '') {
                $this->Order_by = ' order by '.$by.' '.$type;
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
     * @return obj 自身实例
     */
    public function limit($index, $number)
    {
        $this->limit = ' limit '. $this->pdoBind('li', $index);
        $this->limit .= (','. $this->pdoBind('li', $number));
        return $this;
    }

    /**
     * 从当前条件查询语句
     * @param string $data 要查询的条目。不是数组的话查询这一个
     * @return array 查询结果
     */
    public function select($data = '*', $key = '')
    {
        if ($key && '*' != $data) {
                $data = $key . ',' . $data;
        }
        //构造查询变量
        if (is_array($data)) {
            $select = "select ".implode(',', $data);
        } else {
            $select = "select ".$data;
        }
        $this->statement = "$select from $this->table ". $this->where. $this->Order_by. $this->limit;
        if ($key) {
            return $this->bind_prpr()->fetchAll(\PDO::FETCH_UNIQUE | \PDO::FETCH_ASSOC);
        } else {
            return $this->bind_prpr()->fetchAll();
        }
    }

    /**
     * 从当前条件删除语句
     * @return int 删除行数
     */
    public function delete()
    {
        //没有执行where就delete,则删除全部表
        $this->statement = "delete from `$this->table` ". $this->where. $this->Order_by. $this->limit;
        return $this->bind_prpr()->rowCount();
    }

    /**
     * 增加数据
     * @param array $data 要插入的键值对数组/或者键数组
     * @param array $v_data 要插入的值数组
     * @return NULL | int 受影响行数
     */
    public function insert($data, $v_data = NULL)
    {
        if (is_null($v_data)) {
            $begin = array();
            $end = array();
            foreach ($data as $k => $v) {
                $begin[] = '`'.$k.'`';
                $end[] .= $this->pdoBind('in', $v);
            }
            $insert = "(". implode(',', $begin).") values (". implode(',', $end).")";
        } else {
            $begin = $data;
            $end = array();
            foreach ($v_data as $value) {
                $end_l = array();
                foreach ($value as $v) {
                    $end_l[] .=  $this->pdoBind('in', $v);
                }
                $end[] = '('.implode(',', $end_l).')';

            }
            $insert = "(". implode(',', $begin).") values ". implode(',', $end);
        }
        $this->statement = "insert into $this->table $insert";
        return $this->bind_prpr()->rowCount();
    }

    /**
     * 更新数据
     * @param array $data 要修改的键值对数组
     * @return int 影响行数
     */
    public function update($data)
    {
        $fields = array();
        foreach ($data as $key => $value) {
            $fields[] = "`$key` = ". $this->pdoBind('up', $value);
        }
        $update = implode(',', $fields);
        # 没有where的话,修改所有数据
        $this->statement = "update `$this->table` set $update ". $this->where. $this->Order_by. $this->limit;
        return $this->bind_prpr()->rowCount();
    }

    /**
     * 执行参数绑定
     * @return PDO 查询结果后对象实例
     */
    protected function bind_prpr()
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

    /**
     * 自定义查询
     */
    private static function ex($stat, $data){
        //获取数据库连接实例
        $db_instance = Db::connect();
        $sta = $db_instance->prepare($stat);
        if (!is_null($data)) {
            foreach ($data as $key => $value) {
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
        } else {
            return $sta;
        }
    }

    /**
     * 自定义查询，支持参数绑定
     * @param string $my_statement 自定义查询语句
     * @param array $bind_arg 参数绑定数组
     * @return array 返回查询结果数组
     */
    public static function query($my_statement,$bind_arg = NULL)
    {
        $sta = self::ex($my_statement, $bind_arg);
        return $sta->fetchAll();
    }
    /**
     * 自定义查询，支持参数绑定
     * @param string $my_statement 自定义查询语句
     * @param array $bind_arg 参数绑定数组
     * @return int 返回影响行数
     */
    public static function execute($my_statement,$bind_arg = NULL)
    {
        $sta = self::ex($my_statement, $bind_arg);
        return $sta->rowCount();
    }      

}