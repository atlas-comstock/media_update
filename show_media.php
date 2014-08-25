<?php 
define('INIT_NO_WECHAT',1);
define('INIT_NO_USERS',1);
define('INIT_NO_SESSION',1);
include 'includes/init.php';

$id=intval( $_GET['id']);

if( $id){
	$media=$db->getRow("select * from av_title where title_id=$id");
	if( $media){
		$smarty->assign('media', $media);
		$video=$db->getAll("select * from av_video where title_id=$id order by ep desc");
		$smarty->assign('video', $video);
		$smarty->display('media.html');

	}else{
		$smarty->assign('msg','你输入的路径不正确，或视频已经被删除！');
		$smarty->display('error.html');
	}

}else{
	$smarty->assign('msg','你输入的路径不正确！');
	$smarty->display('error.html');
}




die();

$start_media = 6;
$sql = "SELECT * FROM av_title LIMIT $start_media,6";  
$media = $db->getAll($sql);
foreach( $media as $single_media )
{
echo $single_media['title_name'].'<br>';
echo $single_media['latest_ep_title'].'<br>';
echo $single_media['title_img_url'].'<br>';
echo $single_media['list_url'].'<br>';
}
?>
