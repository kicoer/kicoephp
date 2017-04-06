<!DOCTYPE html>
<html>
<head>
    <!-- view 示例 -->
    <meta charset="utf-8">
    <title>Kicoe - Blog</title>
    <meta name="keywords" content="kicoe,博客,blog,代码,code,游戏,game">
    <meta name="description" content="失眠症开始让我日渐遗忘">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <link rel="stylesheet" type="text/css" href="/static/css/main.css">
</head>
<body>
<div id="up" class="p_up"><a href="javascript:up()"><b>^</b></a></div>
<div id="header">
    <div class="nav">
        <ul>
            <li> <a href="/">博客</a> </li>
            <li> <a href="/page/game/">项目</a> </li>
            <li> <a href="/page/link/">链接</a> </li>
            <li> <a href="/page/about/">关于</a> </li>
        </ul>
    </div>
</div>
<div id="content">
<?php foreach($art_list as $art){ ?>
    <div class="article">
        <h1> <a href="/article/id/<?php echo $art['id'] ?>"><?php echo stripslashes($art['title']); ?></a> </h1>
        <div class="mark"> <?php echo date('Y/m/d',$art['up_time']); ?> @<a class="author_" href="javascript:void(0)"><?php echo $art['author'] ?></a> 
        <?php if($art['tags']){ ?> <b>#</b> <?php foreach(explode(',', $art['tags']) as $tag){ ?>
            <a class="tag" href="/tag/name/<?php echo $tag_list[$tag]['tag_name'] ?>"><?php echo $tag_list[$tag]['tag_name'] ?></a><b>,</b>
        <?php }} ?>
        </div>
        <div class="syn">
            <?php if($art['img']){ ?> <img src="<?php echo 'http://7xk6io.com1.z0.glb.clouddn.com'.$art['img'] ?>"> <?php } ?>
            <?php echo stripslashes($art['syn']); ?>
        </div>
    </div>
<?php } ?>
<div class="page">
<ul>
<?php
   if ($page-1 < $pages-$page) {
        $start_page = max($page-3, 1);
        $end_page = min($page+3, $pages);
        for ($i = $start_page; $i <= $end_page; $i++) {
            if ($i != $page) {
                echo '<li><a href="/article/page/'.$i.'">'.$i.'</a></li>';
            } else {
                echo '<li><span>'.$i.'</span></li>';
            }
        }
        if($page+3 < $pages){
            echo '<li><span>...</span></li>';
            echo '<li><a href="/article/page/'.$pages.'">'.$pages.'</a></li>';
        }
    } else {
        if($page-3 > 1){
            echo '<li><a href="/article/page/1">1</a></li>';
            echo '<li><span>...</span></li>';
        }
        $start_page = max($page-3, 1);
        $end_page = min($page+3, $pages);
        for ($i = $start_page; $i <= $end_page; $i++) {
            if ($i != $page) {
                echo '<li><a href="/article/page/'.$i.'">'.$i.'</a></li>';
            } else {
                echo '<li><span>'.$i.'</span></li>';
            }
        }
    }
?>
</ul>
</div>
</div>
<div id="footer">
    <span class="f_en">Powered by<a href="https://github.com/kicoer/kicoephp" target="_blank"> kicoephp </a></span>
</div>
<script type="text/javascript" src="/static/js/main.js"></script>
</body>
</html>