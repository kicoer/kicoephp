<?php
namespace app\controller;

use \kicoe\Core\Controller;
use \kicoe\Core\Query;
use \kicoe\Core\Cache;
use \app\model\Article;
use \app\model\Tags;

class Index extends Controller
{
    /**
     * 示例页面
     * @param $arg 前台参数
     */
    public function index($arg = 'kicoe')
    {
        $this->assign('poi', $arg);
        $this->show();
    }

    /**
     * 博客示例
     * @param $page int 页数
     */
    public function article($page = 1)
    {
        $art_m = new Article();
        $this->assign(['art_list'=>$art_m->get_article_list($page, 7),
            'tag_list'=>Query::table('tags')->select(),
            'page'=>$page,
            'pages'=>$art_m->get_pages(7)]);
        $this->show();
    }
}