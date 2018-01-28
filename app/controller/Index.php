<?php
// 控制器示例

namespace app\controller;

use kicoe\Core\Controller;
use app\model\Article;
use kicoe\Core\Request;

class Index extends Controller
{
    
    public function index($arg = 'kicoe')
    {
        $this->assign('poi', $arg);
        $this->show();
    }
    
}