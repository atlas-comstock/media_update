<?php 
include 'includes/init.php';
$media=$db->getAll("select * from av_video order by fetch_time desc LIMIT 10,16");
 if($media){
		$smarty->assign('media', $media);
		$smarty->display('hot.html');
		   }
else{
		$smarty->assign('msg','你输入的路径不正确，或视频已经被删除！');
		$smarty->display('error.html');
	}
	die();
 ?>
