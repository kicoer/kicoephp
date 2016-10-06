<?php
namespace app\controller;

use \kicoe\Core\Controller;
use \kicoe\Core\Query;
use \app\model\Article;
use \app\model\Tags;

class Index extends Controller
{
    public function index($page = 1)
    {
        $art_m = new Article();
        $tags_m = new Tags();
        $art_list = $art_m->set()->limit(($page-1)*10, 10)->order('up_time', 'desc')->select('id,title,tags,img,syn,author,up_time', 'id');
        $tag_list = $tags_m->set()->select('tag_name', 'id');
        $pages = $art_m->set()->select('count(id)')[0]["count(id)"];
        $pages = ceil($pages/10.0);
        $this->assign(['art_list'=>$art_list,
            'tag_list'=>$tag_list,
            'page'=>$page,
            'pages'=>$pages]);
        $this->show();
    }
    public function article($id = 1)
    {
        $art_m = new Article();
        $tags_m = new Tags();
        $art_m->get(['id'=>$id]);
        $tag_list = $tags_m->set()->select('tag_name', 'id');
        $this->assign(['title'=>$art_m->title,
            'tags'=>$art_m->tags,
            'author'=>$art_m->author,
            'up_time'=>$art_m->up_time,
            'content'=>$art_m->content,
            'tag_list'=>$tag_list]);
        $this->show();
    }
    public function link(){
        $link_list = Query::table('links')->select(['url','name']);
        $this->assign('link_list',$link_list);
        $this->show();
    }
}