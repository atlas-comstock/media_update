<?php
include 'includes/init.php';
$user_id = $_COOKIE['av_user_id'];
if( $user_id ){
 $sql = "SELECT * FROM av_subs WHERE user_id = $user_id ";  
 $subs_result = $db->getAll($sql); 
 if( $subs_result ) 
	{ 
		foreach( $subs_result as $subscript=> $single_subs )
		{
			$media[$subscript] = $db->getRow("select * from av_title where title_id=$single_subs[title_id] "); 
			$media[$subscript][watched_ep] = $single_subs[watched_ep]; 
		}
	}
}
$smarty->assign('media', $media);
$smarty->display('my_follow.html');
die();
?>