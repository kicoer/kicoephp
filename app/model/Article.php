<?php
namespace app\model;

use \kicoe\Core\Model;

/**
* 示例
* user表的模型类
*/
class Article extends Model
{
    /**
     * 获取前台文章列表
     * @param int $page 要获取的页数
     * @param int $num 每页数量
     * @return array 该页的数据集
     */
    public function get_article_list($page, $num)
    {
        return $this->set([['author', 'in', ['kicoe','moonprism']]])->limit(($page-1)*$num, $num)->order('up_time', 'desc')->select('id,title,tags,img,syn,author,up_time', 'id');
    }

    /**
     * 获取文章总页数
     * @param int $num 每页数量
     * @return int 页数
     */
    public function get_pages($num)
    {
        $pages = $this->set([['author', 'in', ['kicoe','moonprism']]])->select('count(id)')[0]["count(id)"];
        return ceil($pages/number_format($num, 1));
    }    
}