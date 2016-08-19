<?php

namespace kicoe\Core;

use \kicoe\Core\Db;

/**
* 模型类，直接PDO链接mysql吧
*/
class Model
{
	//类名与表名
	protected $class;
	protected $table;

	protected $dbHandle;
	
	function __construct()
	{
        //获取数据库链接的单例
        $this->dbHandle =  Db::connect();

        // 获取模型名称
        $this->class = explode('\\',get_class($this));
        $this->class = end($this->class);
        
        // 数据库表名与类名一致
         $this->table = strtolower($this->class);
	}

    // 查询所有
    public function selectAll()
    {
        $sql = sprintf("select * from `%s`", $this->table);
        $sth = $this->dbHandle->prepare($sql);
        $sth->execute();

        return $sth->fetchAll();
    }

    // 根据条件 (id) 查询
    public function select($id)
    {
        $sql = sprintf("select * from `%s` where `id` = '%s'", $this->table, $id);
        $sth = $this->dbHandle->prepare($sql);
        $sth->execute();
        
        return $sth->fetch();
    }

    // 根据条件 (id) 删除
    public function delete($id)
    {
        $sql = sprintf("delete from `%s` where `id` = '%s'", $this->table, $id);
        $sth = $this->dbHandle->prepare($sql);
        $sth->execute();

        return $sth->rowCount();
    }

    // 自定义SQL查询，返回影响的行数
    public function query($sql)
    {
        $sth = $this->dbHandle->prepare($sql);
        $sth->execute();

        return $sth->rowCount();
    }

    // 新增数据
    public function add($data)
    {
        $sql = sprintf("insert into `%s` %s", $this->table, $this->formatInsert($data));

        return $this->query($sql);
    }

    // 修改数据
    public function update($id, $data)
    {
        $sql = sprintf("update `%s` set %s where `id` = '%s'", $this->table, $this->formatUpdate($data), $id);

        return $this->query($sql);
    }

    // 将数组转换成插入格式的sql语句
    private function formatInsert($data)
    {
        $fields = array();
        $values = array();
        foreach ($data as $key => $value) {
            $fields[] = sprintf("`%s`", $key);
            $values[] = sprintf("'%s'", $value);
        }

        $field = implode(',', $fields);
        $value = implode(',', $values);

        return sprintf("(%s) values (%s)", $field, $value);
    }

    // 将数组转换成更新格式的sql语句
    private function formatUpdate($data)
    {
        $fields = array();
        foreach ($data as $key => $value) {
            $fields[] = sprintf("`%s` = '%s'", $key, $value);
        }

        return implode(',', $fields);
    }
}