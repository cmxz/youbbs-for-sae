<?php
define('IN_SAESPOT', 1);
define('ROOT' , pathinfo(__FILE__, PATHINFO_DIRNAME));

include(ROOT . '/config.php');
include(ROOT . '/common.php');

if (!$cur_user || $cur_user['flag']<99) exit('error: 403 Access Denied');

$tip1 = '';
$tip2 = '';
$tip3 = '';
$tips = array();
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $DBM = new DB_MySQL;
    $DBM->connect($servername_m, $dbport, $dbusername, $dbpassword, $dbname);

    $action = trim($_POST['action']);
    if($action =='base'){
        // 修改设置一些默认参数
        $_POST['name'] = filter_chr($_POST['name']);
        $_POST['site_des'] = filter_chr($_POST['site_des']);
        $_POST['icp'] = filter_chr($_POST['icp']);

        $_POST['home_shownum'] = intval($_POST['home_shownum']);
        if(!$_POST['home_shownum']) $_POST['home_shownum'] = 20;

        $_POST['list_shownum'] = intval($_POST['list_shownum']);
        if(!$_POST['list_shownum']) $_POST['list_shownum'] = 20;


        $_POST['hot_node_num'] = intval($_POST['hot_node_num']);
        if(!$_POST['hot_node_num']) $_POST['hot_node_num'] = 20;

        $_POST['bot_node_num'] = intval($_POST['bot_node_num']);
        if(!$_POST['bot_node_num']) $_POST['bot_node_num'] = 100;

        $_POST['article_title_max_len'] = intval($_POST['article_title_max_len']);
        if(!$_POST['article_title_max_len']) $_POST['article_title_max_len'] = 60;

        $_POST['article_content_max_len'] = intval($_POST['article_content_max_len']);
        if(!$_POST['article_content_max_len']) $_POST['article_content_max_len'] = 5000;

        $_POST['article_post_space'] = intval($_POST['article_post_space']);
        if(!$_POST['article_post_space']) $_POST['article_post_space'] = 5;

        $_POST['comment_min_len'] = intval($_POST['comment_min_len']);
        if(!$_POST['comment_min_len']) $_POST['comment_min_len'] = 1;

        $_POST['comment_max_len'] = intval($_POST['comment_max_len']);
        if(!$_POST['comment_max_len']) $_POST['comment_max_len'] = 3000;

        $_POST['commentlist_num'] = intval($_POST['commentlist_num']);
        if(!$_POST['commentlist_num']) $_POST['commentlist_num'] = 32;

        $_POST['comment_post_space'] = intval($_POST['comment_post_space']);
        if(!$_POST['comment_post_space']) $_POST['comment_post_space'] = 3;

        $_POST['close'] = intval($_POST['close']);

        $_POST['close_note'] = filter_chr($_POST['close_note']);
        if(!$_POST['close_note']) $_POST['close_note'] = '数据调整中';

        $_POST['reg_ip_space'] = intval($_POST['reg_ip_space']);
        if(!$_POST['reg_ip_space']) $_POST['reg_ip_space'] = 3600;

        $_POST['authorized'] = intval($_POST['authorized']);
        $_POST['register_review'] = intval($_POST['register_review']);
        $_POST['close_register'] = intval($_POST['close_register']);
        $_POST['show_debug'] = intval($_POST['show_debug']);

        $_POST['jquery_lib'] = filter_chr($_POST['jquery_lib']);
        if(!$_POST['jquery_lib']) $_POST['jquery_lib'] = '/static/js/jquery-1.6.4.js';

        $_POST['safe_imgdomain'] = filter_chr($_POST['safe_imgdomain']);
        $_POST['SAE_STORAGE'] = filter_chr($_POST['SAE_STORAGE']);



        // 安全图床域名白名单 格式 www.xxx.com
        $safe_imgdomain = trim($_POST['safe_imgdomain']);
        if($safe_imgdomain){
            $safe_imgdomain = str_replace("\n\r", "\n", $safe_imgdomain);
            $safe_imgdomain = str_replace("\r", "\n", $safe_imgdomain);
            $safe_imgdomain = str_replace("http://", "", $safe_imgdomain);
            $safe_imgdomain = str_replace("https://", "", $safe_imgdomain);
            $safe_imgdomain = str_replace("/", "", $safe_imgdomain);
            $safe_arr = explode("\n",$safe_imgdomain);
            // 加入网站域名
            if($_SERVER['HTTP_HOST']){
                $safe_arr[] = $_SERVER['HTTP_HOST'];
            }
            $safe_arr = array_filter(array_unique($safe_arr));
            $_POST['safe_imgdomain'] = implode("|", $safe_arr);
        }

        // 确保main_nodes正确
        $_POST['main_nodes'] = filter_chr($_POST['main_nodes']);
        if($_POST['main_nodes'] && ($options['main_nodes'] != $_POST['main_nodes'] ) ){
            $table_status = $DBS->fetch_one_array("SHOW TABLE STATUS LIKE 'yunbbs_categories'");
            $nodes_num = $table_status['Auto_increment'];
            $main_nodes = str_replace(" ", ",", $_POST['main_nodes']);
            $main_nodes = str_replace("/", ",", $main_nodes);
            $main_nodes = str_replace("、", ",", $main_nodes);
            $main_nodes = str_replace("。", ",", $main_nodes);
            $main_nodes = str_replace("n-", ",", $main_nodes);
            $main_nodes = str_replace("，", ",", $main_nodes);
            $main_nodes_arr = explode(",", $main_nodes);
            $main_nodes_arr = array_filter(array_unique($main_nodes_arr));
            $new_main_nodes_arr = array();
            foreach($main_nodes_arr as $node_id){
                $node_id = intval($node_id);
                if($node_id && ($node_id < $nodes_num)){
                    $new_main_nodes_arr[] = $node_id;
                }
            }
            if($new_main_nodes_arr){
                $_POST['main_nodes'] = implode(",", $new_main_nodes_arr);
            }else{
                $_POST['main_nodes'] = '';
            }
        }

        // spam_words
        $spam_words = filter_chr($_POST['spam_words']);
        $spam_words = str_replace("，", ",", $spam_words);
        $spam_words_arr = explode(",", $spam_words);
        $spam_words_arr = array_filter(array_unique($spam_words_arr));
        $_POST['spam_words'] = implode(",", $spam_words_arr);



        $changed = 0;
        foreach($options as $k=>$v){
            if($k != 'site_create'){
                // 使用反斜线引用字符串
                $newv = addslashes(trim($_POST[$k]));
                if(str_replace('\\', '', $newv)!= $v){
                    $DBM->unbuffered_query("UPDATE `yunbbs_settings` SET `value`='$newv' WHERE `title`='$k'");
                    $changed += 1;
                    // 更新原数据 去掉反斜线
                    $options[$k] = str_replace('\\', '', $newv);
                }
            }
        }
        if($changed){
            $MMC->delete('options');
            $MMC->delete('regip_'.$onlineip);
            $tip1 = '已成功更改了 '.$changed.' 个设置';
        }
    }else if($action =='flushmc'){
        $MMC->flush();
        $tip2 = '缓存已清空';
    }else if($action =='flushdata'){
        //$DBM->query("DROP TABLE IF EXISTS `yunbbs_articles`");
        //$DBM->query("DROP TABLE IF EXISTS `yunbbs_categories`");
        //$DBM->query("DROP TABLE IF EXISTS `yunbbs_comments`");
        //$DBM->query("DROP TABLE IF EXISTS `yunbbs_links`");
        //$DBM->query("DROP TABLE IF EXISTS `yunbbs_settings`");
        //$DBM->query("DROP TABLE IF EXISTS `yunbbs_users`");
        //$DBM->query("DROP TABLE IF EXISTS `yunbbs_favorites`");

        //$MMC->flush();

        $tip3 = '这个按钮太危险了，不能在这里删除，请在mysql后台删除';
        //header('location: /install');
        //exit;
    }
}
// 页面变量
$title = '网站设置';


$pagefile = ROOT . '/templates/default/'.$tpl.'admin-setting.php';

include(ROOT . '/templates/default/'.$tpl.'layout.php');

?>
