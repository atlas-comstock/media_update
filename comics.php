<?php

include 'includes/init.php';


if( $title_id=$_GET['title_id']){

	$v=$db->getRow("select * from av_comic_title where title_id=$title_id");
	echo "<div><img src=\"$v[title_img_url]\" /><a href=\"$v[list_url]\">$v[title] ........ 更新至 $v[latest_ep_title]</div>"; 

	$data=$db->getAll("select * from av_comic_ep where title_id=$title_id order by ep desc");
	foreach( $data as $vv){
		echo '<div><a href="'.$vv['video_url'].'" target=blank>'.$vv['ep_title'].'</a> 更新：'.date('Y-m-d H:i:s', $vv['fetch_time']).'</div>';

	}
}else{
	$data=$db->getAll("select * from av_comic_title order by title");
	foreach( $data as $v){
		echo "<div><a href=\"comics.php?title_id=$v[title_id]\">$v[title] ........ 更新至 $v[latest_ep_title]</div>";
	}
}

?>