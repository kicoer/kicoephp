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
	//自身实例
	private static $instance;
	//数据库连接实例
	private $db_instance;
	//构造的查询语句
	private $statement;
	//保存的表名
	private $table;
	//保存的where数据
	private $where;

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
        self::$instance->where = 'where ';
        return self::$instance;
	}

	/**
	 * 构造where
	 * @param 可惜没有重载，那就自己定义吧
	 * @return 返回自身实例
	 */
	public function where($arg1, $arg2, $arg3 = False)
	{
		if ($arg3 == False && !in_array($arg2, array('=','!=','<>','<','>','<=','>=','BETWEEN','LIKE','IN'))) {
			if ($this->where!='where ') {
				//要再构造and
				$creat_where = sprintf(" and `%s` = '%s'", $arg1, $arg2);
			} else {
				$creat_where = sprintf("`%s` = '%s'", $arg1, $arg2);
			}
		} else {
			if ($this->where!='where ') {
				//要再构造and
				$creat_where = sprintf(" and `%s` %s '%s'", $arg1, $arg2, $arg3);
			} else {
				$creat_where = sprintf("`%s` %s '%s'", $arg1, $arg2, $arg3);
			}
		}
		$this->where .= $creat_where;
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
		if ($this->where == 'where ') {
			$this->statement = sprintf("%s from `%s` ",$select,$this->table);
		} else {
			$this->statement = sprintf("%s from `%s` ",$select,$this->table).$this->where;
		}
		return $this->query('array');
	}

	/**
	 * 从当前条件删除语句
	 * @return 删除行数
	 */
	public function delete()
	{
		if ($this->where == 'where ') {
			//没有执行where就delete,则删除全部表。。。天啊真可怕
			$this->statement = sprintf("delete from `%s` ",$this->table);
		} else {
			$this->statement = sprintf("delete from `%s` ",$this->table).$this->where;
		}
		return $this->query('line');
	}

	/**
	 * 增加数据
	 * @param array $data 要插入的键值对数组,必须是二维数组哦
	 */
	public function insert($data)
	{
		$begin = array();
		$end = array();
		foreach ($data as $value) {
			foreach ($value as $k => $v) {
				$begin[] = sprintf("`%s`", $k);
            	$end[] = sprintf("'%s'", $v);
			}
		}
		$insert = sprintf("(%s) values (%s)", implode(',', $begin), implode(',', $end));
		$this->statement = sprintf("insert into %s %s", $this->table, $insert);
		return $this->query('line');
	}

	/**
	 * 更新数据
	 * @param array $data 要修改的键值对数组
	 */
	public function update($data)
	{
		$fields = array();
        foreach ($data as $key => $value) {
            $fields[] = sprintf("`%s` = '%s'", $key, $value);
        }
        $update = implode(',', $fields);
        if ($this->where == 'where ') {
        	# 没有where的话,修改所有数据
        	$this->statement = sprintf("update `%s` set %s ",$this->table,$update);
        } else {
        	$this->statement = sprintf("update `%s` set %s ",$this->table,$update).$this->where;
        }
        return $this->query('line');
	}

	/**
	 * 执行查询语句,返回查询结果数组
	 * @param string $type 需要返回的结果类型
	 */
	public function query($type)
	{
		$sta = $this->db_instance->prepare($this->statement);
        $sta->execute();
        if ($sta->errorCode() != '00000') {
        	# 进行错误处理
        	throw new Exception("数据库执行错误", implode('<br>',$sta->errorInfo()));
        }
        switch ($type) {
        	case 'array':
        		return $sta->fetch();
        		break;
        	case 'line':
        		return $sta->rowCount();
        		break;
        }
	}

}