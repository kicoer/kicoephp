<?php
namespace kicoe\Core\Secret;

use \kicoe\Core\Exception;
use \kicoe\Core\Db;

/**
* 数据查询的父类
*/
class Moo
{
    protected $table;
    // 数据库链接实例
    protected $db_instance;
    // 构造的查询语句
    protected $statement;
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
    protected function lastInsertId(){
        return $this->db_instance->lastInsertId();
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
        
        if ($arg3 == False && !in_array($arg2, array('=','!=','<>','<','>','<=','>=','BETWEEN','LIKE','IN','NOT IN','NOT LINK','NOT BETWEEN'))) {
            $this->Pdo_bind_count++;          
            $creat_where = '`'.$arg1.'` = :wh'.$this->Pdo_bind_count;
            $this->Pdo_bind_data[':wh'.$this->Pdo_bind_count] = $arg2;
        } else {
            $sign = strtolower($arg2);            
            if ($sign == 'in' || $sign =='not in' ) {
                // IN 与 NOT IN 处理
                $in_arr = [];
                foreach ($arg3 as $in_key => $in_val) {
                    $this->Pdo_bind_count++;
                    $this->Pdo_bind_data[':wh'.$this->Pdo_bind_count] = $in_val;
                    $in_arr[] = ' :wh'. $this->Pdo_bind_count;
                }
                $creat_where = '`'.$arg1.'` '.$arg2. ' ('. implode(',', $in_arr). ')';
            } elseif ($sign == 'between' || $sign=='not between' ) {
                // BETWEEN 处理
                $this->Pdo_bind_count++;
                $this->Pdo_bind_data[':wh'.$this->Pdo_bind_count] = $arg3[0];
                $creat_where = '`'.$arg1.'` '.$arg2.' :wh'. $this->Pdo_bind_count. ' and :wh';
                $this->Pdo_bind_count++;
                $this->Pdo_bind_data[':wh'.$this->Pdo_bind_count] = $arg3[1];
                $creat_where .= $this->Pdo_bind_count;             
            } else {
                $this->Pdo_bind_count++;            
                $creat_where = '`'.$arg1.'` '.$arg2.' :wh'.$this->Pdo_bind_count;
                $this->Pdo_bind_data[':wh'.$this->Pdo_bind_count] = $arg3;
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
     * @param obj 自身实例
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
        $this->statement = sprintf("%s from `%s` ", $select, $this->table).$this->where.$this->Order_by.$this->limit;
        if ($key) {
            return $this->bind_prpr()->fetchAll(\PDO::FETCH_UNIQUE | \PDO::FETCH_ASSOC);
        } else {
            return $this->bind_prpr()->fetchAll();
        }
    }

    /**
     * 从当前条件删除语句
     * @return 删除行数
     */
    public function delete()
    {
        //没有执行where就delete,则删除全部表
        $this->statement = sprintf("delete from `%s` ", $this->table).$this->where.$this->Order_by.$this->limit;
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
     * @return 返回查询结果数组
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
     * @return 返回影响行数
     */
    public static function execute($my_statement,$bind_arg = NULL)
    {
        $sta = self::ex($my_statement, $bind_arg);
        return $sta->rowCount();
    }      

}