<?php
// 控制器示例

namespace app\controller;

use kicoe\Core\Controller;
use app\model\Article;
use kicoe\Core\Request;
use kicoe\Core\Session;

class Index extends Controller
{
    
    public function index($arg = 'kicoe')
    {
        $s = new Session;
        $s->name = 'kicoe';
        $this->assign('poi', $arg);
        $this->show();
    }

    public function article(Request $re)
    {
        $s = new Session;
        echo $s->name;
    }
    
}