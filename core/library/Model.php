<?php

namespace kicoe\Core;

use \kicoe\Core\Db;
use \kicoe\Core\Secret\Moo;

/**
 * 模型类，简单的ORM
 */
class Model extends Moo
{
    // 类名
    protected $class;
    // 查询与操作数据
    protected $_data = array();

    protected function resetV(){
        parent::resetV();
        $this->_data = []; 
    }
    /**
     * 执行初始化操作
     */
    public function __construct()
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
     * @param mixed $val 主键的值
     * @param string $pk 主键名
     * @return 当前对象
     */
    public function get($val, $pk = 'id')
    {
        $this->resetV();
        $this->Pdo_bind_count++;
        $this->Pdo_bind_data[':wh'.$this->Pdo_bind_count] = $val;
        $this->where = ' where '. $pk. ' = :wh'. $this->Pdo_bind_count;
        $this->statement = 'select * from '. $this->table. $this->where;
        $this->_data = $this->bind_prpr()->fetch();
        return $this;
    }

    /**
     * 和get获取不同，这里主要是构造查询语句where
     * @param array $data where中的键值对数组
     * @return 当前对象
     */
    public function set($data)
    {
        $this->resetV();
        // and | or
        $con_token = '';
        foreach ($data as $value) {
            switch (count($value)) {
                case 1:
                    # 为1时，为and 或 or
                    $con_token = $value;
                    break;
                case 2:
                    # 为2是，还是默认=的数组
                    $this->wh($value[0], $value[1], False, $con_token);
                    break;
                case 3:
                    # 为3时，数组第二个元素为判断符
                    $this->wh($value[0], $value[1], $value[2], $con_token);
                    break;
            }
        }
        return $this;
    }

    /**
     * 插入新数据
     * @param array $c_data 要插入的列，为空时请将_data赋值好
     * @param array $data 要插入的数据
     */
    public function insert($c_data = NULL, $data = NULL)
    {
        if (is_null($c_data)) {
            $c_data = $this->_data;
        }
        parent::insert($c_data, $data);
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
        parent::update($data);
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

}