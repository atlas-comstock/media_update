<?php 
define('INIT_NO_WECHAT',1);
define('INIT_NO_USERS',1);
define('INIT_NO_SESSION',1);
include 'includes/init.php';
$title_name = $_GET['title_name'];
if( $title_name ){
 $sql = "SELECT * FROM av_title WHERE title_name  LIKE '%$title_name%'"; 
 $search_result = $db->getAll($sql);
 if( $search_result ) 
	{
	$smarty->assign('search_result', $search_result);
	$smarty->display('search_result.html');
	}
 else
	{
	echo 'Null'; 
	}
}
 
die();
 ?>