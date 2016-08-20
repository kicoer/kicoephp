<?php
namespace app\model;

use \kicoe\Core\Model;

/**
* user表的模型类
*/
class User extends Model
{

	//判断当前用户表是否为空
	public function isNull()
	{
		return $this->selectAll()? False : True;
	}
	
}
