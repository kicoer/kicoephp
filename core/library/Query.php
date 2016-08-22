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
	//保存的pdo参数绑定数据
	private $Pdo_bind_data = array();
	//保存的pdo参数绑定计数
	private $Pdo_bind_count;

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
        self::$instance->where = null;
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
		if (is_null($this->where)) {
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
		if (is_null($this->where)) {
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
		if (is_null($this->where)) {
			$this->statement = sprintf("%s from `%s` ", $select, $this->table);
		} else {
			$this->statement = sprintf("%s from `%s` ", $select, $this->table).$this->where;
		}
		return $this->bind_prpr()->fetch();
	}

	/**
	 * 从当前条件删除语句
	 * @return 删除行数
	 */
	public function delete()
	{
		if (is_null($this->where)) {
			//没有执行where就delete,则删除全部表。。。天啊真可怕
			$this->statement = sprintf("delete from `%s` ", $this->table);
		} else {
			$this->statement = sprintf("delete from `%s` ", $this->table).$this->where;
		}
        return $this->bind_prpr()->rowCount();
	}

	/**
	 * 增加数据
	 * @param array $data 要插入的键值对数组
	 */
	public function insert($data)
	{
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
        return $this->bind_prpr()->rowCount();
	}

	/**
	 * 更新数据
	 * @param array $data 要修改的键值对数组
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
        if (is_null($this->where)) {
        	# 没有where的话,修改所有数据
        	$this->statement = sprintf("update `%s` set %s ",$this->table,$update);
        } else {
        	$this->statement = sprintf("update `%s` set %s ",$this->table,$update).$this->where;
        }

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
				//原来bindParam会绑定变量而不是值，害的我差点以为要用到闭包
				$sta->bindValue($key,$value);
			}
		}
		print_r($this->Pdo_bind_data);
		echo "<br>";
		print_r($this->statement);
				echo "<br>";
        $sta->execute();
        if ($sta->errorCode() != '00000') {
        	# 进行错误处理
        	throw new Exception("数据库执行错误", implode('<br>',$sta->errorInfo()));
        }
        return $sta;
	}

}