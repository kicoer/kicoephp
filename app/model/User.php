<?php
namespace app\model;

use \kicoe\Core\Model;

/**
* user表的模型类
*/
class User extends Model
{
	public function __construct()
	{
		// 添加表前缀
		$this->table = "ex_user";
	}
}
