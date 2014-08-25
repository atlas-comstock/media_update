<?php 
include 'includes/init.php';

 $sql = "SELECT * FROM av_title order by title_id desc LIMIT 5"; 
 $all_media = $db->getAll($sql); 
 if( $all_media ) 
	{ 
     $smarty->assign('all_media', $all_media);
     $smarty->display('show_all.html');
	}
die();
 ?>