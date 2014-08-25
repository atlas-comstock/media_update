<?php
include 'includes/init.php';

/*
$h=print_r($visitor,true);
$h.=print_r($vvcfg,true);
$smarty->assign('visitor', $visitor);
$smarty->assign('content', $h);

$data=$db->getAll("select nickname,openid from av_wx_visitor");
$smarty->assign('var1', $data);
*/

$data=$db->getAll("select title_id id,title_name n from av_title order by title_id desc limit 20");
$smarty->assign('titles', $data);
echo $_COOKIE['av_user_id'];

$smarty->display("index.html");
?>
