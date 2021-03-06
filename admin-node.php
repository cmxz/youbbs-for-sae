<?php
define('IN_SAESPOT', 1);
define('ROOT' , pathinfo(__FILE__, PATHINFO_DIRNAME));

include(ROOT . '/config.php');
include(ROOT . '/common.php');

if (!$cur_user || $cur_user['flag']<99) exit('error: 403 Access Denied');

$nid = intval($_GET['nid']);
if($nid){
    $query = "SELECT * FROM `yunbbs_categories` WHERE `id`='$nid'";
    $c_obj = $DBS->fetch_one_array($query);
    if(!$c_obj){
        header('location: /admin-node#edit');
        exit;
    }
}

$tip1 = '';
$tip2 = '';
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $action = $_POST['action'];
    
    $DBM = new DB_MySQL;
    $DBM->connect($servername_m, $dbport, $dbusername, $dbpassword, $dbname);
    
    if($action=='find'){
        $n_id = trim($_POST['findid']);
        if($n_id){
            header('location: /admin-node-'.$n_id);
        }else{
            header('location: /admin-node#edit');
        }
        exit;
    }else if($action=='add'){
        $n_name = trim($_POST['name']);
        $n_about = trim($_POST['about']);
        if($n_name){
            $check_obj = $DBS->fetch_one_array("SELECT * FROM `yunbbs_categories` WHERE `name`='".$n_name."'");
            if($check_obj){
                $tip1 = $n_name.' 分类名已存在，请修改为不同的分类名';
            }else{
                if($DBM->query("INSERT INTO `yunbbs_categories` (`id`,`name`,`about`) VALUES (null,'$n_name','$n_about')")){
                    //更新缓存
                    $MMC->delete('newest_nodes');
                    $MMC->delete('bot_nodes');
                    $MMC->delete('site_infos');
                    $tip1 = '已成功添加';
                }else{
                    $tip1 = '数据库更新失败，修改尚未保存，请稍后再试';
                }
            }
        }else{
            $tip1 = '分类名不能留空';
        }
    }else if($action=='edit'){
        $n_name = trim($_POST['name']);
        $n_about = trim($_POST['about']);
        if($n_name){
            if($DBM->unbuffered_query("UPDATE `yunbbs_categories` SET `name`='$n_name',`about`='$n_about' WHERE `id`='$nid'")){
                //更新缓存
                $MMC->delete('newest_nodes');
                $MMC->delete('bot_nodes');
                $MMC->delete('n-'.$nid);
                $c_obj['name'] = $n_name;
                $c_obj['about'] = $n_about;
                $tip2 = '已成功保存';
            }else{
                $tip2 = '数据库更新失败，修改尚未保存，请稍后再试';
            }
            
        }else{
            $tip2 = '分类名不能留空';
        }
        
    }

}

// 页面变量
$title = '分类管理';


$pagefile = ROOT . '/templates/default/'.$tpl.'admin-node.php';

include(ROOT . '/templates/default/'.$tpl.'layout.php');

?>
