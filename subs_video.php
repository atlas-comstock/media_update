<?php 
define('INIT_NO_WECHAT',1);
define('INIT_NO_USERS',1);
define('INIT_NO_SESSION',1);
include 'includes/init.php';
$title_id = intval( $_GET['title_id']);
$sql = "SELECT * FROM av_video WHERE title_id = $title_id";  
$subs_video_result = $db->getAll($sql); 
$smarty->assign('subs_video_result', $subs_video_result);
$smarty->display('subs_video.html');
die();
 ?>