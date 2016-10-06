<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>kicoer</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="stylesheet" type="text/css" href="/static/css/main.css">
    <link rel="stylesheet" type="text/css" href="/static/css/style.css">
</head>
<body>
<div id="up"><a href="javascript:up()"><b>^</b></a></div>
<div id="header">
    <div class="nav">
        <ul>
            <li> <a href="/">Blog</a> </li>
            <li> <a href="javascript:void(0)">Game</a> </li>
            <li> <a href="/link">Link</a> </li>
            <li> <a href="/page/about.html">About</a> </li>
        </ul>
    </div>
</div>
<div id="content">
<?php foreach($art_list as $art){ ?>
    <div class="article">
        <h1> <a href="/article/id/<?php echo $art['id'] ?>"><?php echo $art['title'];?></a> </h1>
        <div class="mark"> <?php echo date('Y/m/d',$art['up_time']); ?> @<a href="#"><?php echo $art['author'] ?></a> <b>#</b> 
        <?php if($art['tags']){foreach(explode(',', $art['tags']) as $tag){ ?>
            <a class="tag" href="#"><?php echo $tag_list[$tag]['tag_name'] ?></a><b>,</b>
        <?php }} ?>
        </div>
        <div class="syn">
            <?php if($art['img']){ ?> <img src="<?php echo $art['img'] ?>"> <?php } ?>
            <?php echo $art['syn'] ?>
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
            echo '<li><a href="/article/page/"'.$pages.'>'.$pages.'</a></li>';
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
    <span>本博客由<a href="https://github.com/kicoer/kicoephp" target="_blank"> kicoephp </a>驱动--</span>
</div>
<script type="text/javascript" src="/static/js/main.js"></script>
</body>
</html>