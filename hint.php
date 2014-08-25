<?php 
define('INIT_NO_WECHAT',1);
define('INIT_NO_USERS',1);
define('INIT_NO_SESSION',1);
include 'includes/init.php';
$input = $_GET['input'];
$sql = "SELECT * FROM av_title WHERE title_name  LIKE '$input%' LIMIT 8"; 
$all_media = $db->getAll($sql);
$hint = '</br>';//"<ul data-role='listview' data-inset='true'>"
 if( $all_media ) 
	{ 
		foreach( $all_media as $single_media)
		{
		$hint = $hint.$single_media['title_name'].'</br>';
		}
		echo $hint;
	}
die();
 ?>