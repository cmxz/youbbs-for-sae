<?php 
if (!defined('IN_SAESPOT')) exit('error: 403 Access Denied'); 

echo '
<div class="title">
    &raquo; ',$title,'/',$taltol_page,'
</div>

<div class="main-box home-box-list">';

foreach($articledb as $article){
echo '
<div class="post-list">'
if($article['uavatar'])
    echo'<div class="item-avatar"><a href="/member/',$article['uid'],'"><img src="',$options['base_avatar_url'],'/',$article['uavatar'],'.mini.jpg" alt="',$article['author'],'" /></a></div>';
else
    echo'<div class="item-avatar"><a href="/member/',$article['uid'],'"><img src="/avatar/0.normal.jpg" alt="',$article['author'],'" /></a></div>';
echo'
    <div class="item-content count',$article['comments'],'">
        <h1><a href="/t/',$article['id'],'">',$article['title'],'</a></h1>
        <span class="item-date"><a href="/n/',$article['cid'],'">',$article['cname'],'</a>';
if($article['comments']){
    echo ' • <a href="/member/',$article['ruid'],'">',$article['rauthor'],'</a> ',$article['edittime'],'回复';
}else{
    echo ' • <a href="/member/',$article['uid'],'">',$article['author'],'</a> ',$article['addtime'],'发表';
}
echo '</span>
    </div>';
if($article['comments']){
    $gotopage = ceil($article['comments']/$options['commentlist_num']);
    if($gotopage == 1){
        $c_page = '';
    }else{
        $c_page = '/'.$gotopage;
    }
    echo '<div class="item-count"><a href="/t/',$article['id'],'">',$article['comments'],'</a></div>';
}
echo '    <div class="c"></div>
</div>';

}


if($tag_obj['articles'] > $options['list_shownum']){ 
echo '<div class="pagination">';
if($page>1){
echo '<a href="/tag/',$tag,'/',$page-1,'" class="float-left">&laquo; 上一页</a>';
}
if($page<$taltol_page){
echo '<a href="/tag/',$tag,'/',$page+1,'" class="float-right">下一页 &raquo;</a>';
}
echo '<div class="c"></div>
</div>';
}


echo '</div>';

?>