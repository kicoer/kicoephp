<?php
namespace app\controller;

use \kicoe\Core\Controller;
use \app\model\User;

class Index extends Controller
{
	public function index()
	{
		$this->show();
	}
}