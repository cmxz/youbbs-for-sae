<?php 
if (!defined('IN_SAESPOT')) exit('error: 403 Access Denied'); 

echo '
<div class="title">
    <div class="float-left fs14">
        <a href="/">',$options['name'],'</a> &raquo; 第',$page,'页 / 共',$taltol_page,'页</div>';
if($cur_user && $cur_user['flag']>4){
    echo '<div class="float-right"><a href="/newpost/1" rel="nofollow" class="newpostbtn">发 新 帖</a></div>';
}
echo '    <div class="c"></div>
</div>

<div class="main-box home-box-list">';

foreach($articledb as $article){
echo '
<div class="post-list">
    <div class="item-avatar"><a href="/member/',$article['uid'],'">';
if(!$is_spider){
    if($article['uavatar'])
        echo '<img src="',$options['base_avatar_url'],'/',$article['uavatar'],'.normal.jpg" alt="',$article['author'],'" />';
    else
        echo '<img src="/avatar/0.normal.jpg" alt="',$article['author'],'" />';
}else{
    if($article['uavatar'])
        echo '<img src="/static/grey.gif" data-original="',$options['base_avatar_url'],'/',$article['uavatar'],'.normal.jpg" alt="',$article['author'],'" />';
    else
        echo '<img src="/static/grey.gif" data-original="/avatar/0.normal.jpg" alt="',$article['author'],'" />';
}
echo '    </a></div>
    <div class="item-content">
        <h1><a href="/t/',$article['id'],'">',$article['title'],'</a></h1>
        <span class="item-date"><a href="/n/',$article['cid'],'">',$article['cname'],'</a>  •  <a href="/member/',$article['uid'],'">',$article['author'],'</a>';
if($article['comments']){
    echo ' •  ',$article['edittime'],' •  最后回复来自 <a href="/member/',$article['ruid'],'">',$article['rauthor'],'</a>';
}else{
    echo ' •  ',$article['addtime'];
}
echo '        </span>
    </div>';
if($article['comments']){
    $gotopage = ceil($article['comments']/$options['commentlist_num']);
    if($gotopage == 1){
        $c_page = '';
    }else{
        $c_page = '/'.$gotopage;
    }
    echo '<div class="item-count"><a href="/t/',$article['id'],$c_page,'#reply',$article['comments'],'">',$article['comments'],'</a></div>';
}
echo '    <div class="c"></div>
</div>';

}


if($taltol_article > $options['list_shownum']){ 
echo '<div class="pagination">';
if($page>1){
echo '<a href="/page/',$page-1,'" class="float-left">&laquo; 上一页</a>';
}
if($page<$taltol_page){
echo '<a href="/page/',$page+1,'" class="float-right">下一页 &raquo;</a>';
}
echo '<div class="c"></div>
</div>';
}


echo '</div>';


?>