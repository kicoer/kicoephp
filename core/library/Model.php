<?php

namespace kicoe\Core;

use \kicoe\Core\Db;

/**
 * 模型类，简单的ORM
 * 凌晨三点无聊爬起来写代码orz
 */
class Model
{
    // 类名与表名
    protected $class;

    protected $table;
    // 数据库链接实例
    protected $db_instance;
    // 查询与操作数据
    protected $_data = array();
    // where语句
    protected $where = '';
    // 保存的order by语句
    private $Order_by = '';
    // 保存的LIMIT语句
    private $limit = '';
    // 保存的pdo参数绑定数据
    private $Pdo_bind_data = array();
    // 保存的pdo参数绑定计数
    private $Pdo_bind_count = 0;

    /**
     * 执行初始化操作
     */
    protected function _init()
    {
        $this->db_instance = Db::connect();
        if (is_null($this->table)) {
            // 获取模型名称
            $this->class = explode('\\',get_class($this));
            $this->class = end($this->class);
            // 数据库表名与类名一致
            $this->table = strtolower($this->class);
        }
    }

    /**
     * 获得上一条执行语句的id
     */
    public function lastInsertId(){
        return $this->db_instance->lastInsertId();
    }

    /**
     * 获取当前查询结果的实例,一般都只查询一条,且立即执行查询
     * @param array $data 如果为空的话,则不执行，否则查询数据，注意这里使用时务必查询主键且一条数据
     */
    public function get($data = NULL)
    {
        if (is_null($this->db_instance)) {
            $this->_init();
        }
        $this->Order_by = '';
        $this->limit = '';
        $this->statement = 'select * from '.$this->table;
        $this->Pdo_bind_data = array();
        $this->Pdo_bind_count = 0;
        $creat_where = '';
        $con_token = '';
        foreach ($data as $key => $value) {
            $this->Pdo_bind_count++;
            $creat_where.=($con_token.$key.' = :wh'.$this->Pdo_bind_count);
            if ($con_token == '') {
                $con_token = ' and ';
            }
            $this->Pdo_bind_data[':wh'.$this->Pdo_bind_count] = $value;
        }
        $this->where = ($creat_where == '') ? '' : ' where '.$creat_where;
        $this->statement.=$this->where;
        $this->_data = $this->bind_prpr()->fetch();
        return $this;
    }

    /**
     * 和get获取不同，这里主要是构造查询语句where
     * @param array $data where中的键值对数组
     */
    public function set($data = NULL)
    {
        if (is_null($this->db_instance)) {
            $this->_init();
        }
        $this->Order_by = '';
        $this->limit = '';
        $this->where = '';
        $this->Pdo_bind_data = array();
        $this->Pdo_bind_count = 0;
        $start_where = ' where ';
        $creat_where = '';
        $con_token = '';
        if (!is_null($data)) {
            foreach ($data as $value) {
                switch (count($value)) {
                    case 1:
                        # 为1时，判断是否为and 或 or
                        if ($value == 'or') {
                            $con_token = ' or ';
                        } elseif ($value == 'and') {
                            $con_token = ' and ';
                        }
                        break;
                    case 2:
                        # 为2是，还是默认=的数组
                        $this->Pdo_bind_count++;
                        $creat_where .= ($con_token.'`'.$value[0].'` = :wh'.$this->Pdo_bind_count);
                        $con_token = ' and ';
                        $this->Pdo_bind_data[':wh'.$this->Pdo_bind_count] = $value[1];
                        break;
                    case 3:
                        # 为3时，数组第二个元素为判断符
                        if (in_array($value[1], array('=','!=','<>','<','>','<=','>=','BETWEEN','LIKE','IN'))) {
                        $this->Pdo_bind_count++;
                        $creat_where .= ($con_token.'`'.$value[0].'` '.$value[1].' :wh'.$this->Pdo_bind_count);
                        $con_token = ' and ';
                        $this->Pdo_bind_data[':wh'.$this->Pdo_bind_count] = $value[2];
                        } else {
                            // 这里抛出异常比较好吗
                        }
                        break;
                }

            }
            $this->where .= ($start_where.$creat_where);
        }
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
     * 插入新数据
     * @param array $c_data 要插入的列，为空时请将_data赋值好
     * @param array $data 要插入的数据
     */
    public function insert($c_data = NULL, $data = NULL)
    {
        $this->Pdo_bind_data = array();
        $this->Pdo_bind_count = 0;
        if (is_null($this->table)) {
            $this->_init();
        }
        if (is_null($c_data)) {
            $begin = array();
            $end = array();
            foreach ($this->_data as $k => $v) {
                $begin[] = '`'.$k.'`';
                $this->Pdo_bind_count++;
                $end[] = ':in'.$this->Pdo_bind_count;
                $this->Pdo_bind_data[':in'.$this->Pdo_bind_count] = $v;
            }
            $insert = sprintf("(%s) values (%s)", implode(',', $begin), implode(',', $end));
            $this->statement = sprintf("insert into %s %s", $this->table, $insert);
        } else {
            $begin = $c_data;
            $end = array();
            foreach ($data as $value) {
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
     * @param array $data 要修改的键值对数组,为空的话用_data
     * @return 影响行数
     */
    public function update($data = NULL)
    {
        if (is_null($data)) {
            $data = $this->_data;
        }
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
     * 从当前条件查询语句
     * @param string $data 要查询的条目。不是数组的话查询这一个
     * @param string $key 返回关联数组的键
     * @return 数组
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
        //没有执行where就delete,则删除全部表。。。真可怕
        $this->statement = sprintf("delete from `%s` ", $this->table).$this->where.$this->Order_by.$this->limit;
        return $this->bind_prpr()->rowCount();
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

    /**
     * DAO
     */
    public function __set($name, $value)
    {
        $this->_data[$name] = $value;
    }

    public function __get($name)
    {
        if (isset($this->_data[$name])) {
            return $this->_data[$name];
        } else {
            return NULL;
        }
    }

    public function __isset($name)
    {
        return isset($this->_data[$name]);
    }

    public function __unset($name)
    {
        unset($this->_data[$name]);
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

}