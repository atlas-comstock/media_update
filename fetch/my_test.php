<?php 
define('INIT_NO_WECHAT',1);
define('INIT_NO_SMARTY',1);
define('INIT_NO_USERS',1);
define('INIT_NO_SESSION',1);
include '../includes/init.php';
require_once('pinyin_table.php');
$fetch_id=12;
$fetch=$db->getRow("select latest_ep_url,url from av_fetch where fetch_id=$fetch_id");
$url=$fetch['url'];
$latest_ep_url=$fetch['latest_ep_url'];
if( !$url)  exit("url is empty ");

$http = new http();
$re=$http->get($url);
$contents=$re['body'];
if( !$contents )  exit("contents is empty ");

$fetch_start = time();

preg_match_all('/<div class="title1">(.+?)<\/div>/is',$contents, $core ); 
foreach( $core[1] as $subscript=>$blocks)
{
	preg_match( '/<span class="xinfan">.*?<a href="([^"]+)".*?<\/span>/', $blocks, $n);
	$list_url=$n[1]?$n[1]:'';
	if( $list_url)	$blocks=str_replace( $n[0], '', $blocks);

	preg_match('/<a href="(?<url>[^"]+)" title="(?<title>[^"]+)" .*?<span class="write">\[20(?<update_time>[^\]]+)/s', $blocks, $media);
	$media[0]=$list_url;
	$all_media[$subscript] =$media;
}
if( $latest_ep_url &&  $all_media[0]['url'] == $latest_ep_url ) exit('no media update ');

foreach( $all_media as $subscript=> $single_media )
{
	if( $single_media['url'] == $latest_ep_url )
	{
		$latest_ep_url=$all_media[0]['url'];
		$field_values = array("latest_ep_url"=>$latest_ep_url , "last_fetch_started"=>$fetch_start, "last_fetch_ended"=>time());  
		$db->autoExecute("av_fetch", $field_values, "UPDATE", "fetch_id=$fetch_id");  
		exit('update finished ');
	}
	else
	{
		$name = preg_replace('/[0-9]*\s*[(0-9]*[)]*\s*$/', '', $single_media['title']);	
		$ep_title = preg_replace('/[^()0-9]+/', '', $single_media['title']);
		$video_url = $single_media['url'];

		$title= get_pinyin_array($name);
		$name=iconv('gb2312', 'utf-8', $name);
		$ep_title=iconv('gb2312', 'utf-8', $ep_title);
		$video_url=iconv('gb2312', 'utf-8', $video_url);
		echo $name.'更新了 :  '.$ep_title.'  url: '.$video_url.'<br>';
		$sql = "SELECT title_name,title_id,latest_ep_title FROM av_title WHERE title_name = '$name'";  
		$title_result = $db->getRow($sql);
		if( $title_result ) //已在庫中 
		{		
			$ID=$title_result['title_id']; 
			//	$sql = "SELECT max(ep_title) FROM av_video WHERE title_id =$ID";  
			$latest_ep = $title_result['latest_ep_title']; //$db->getOne($sql);
			if( $ep_title > $latest_ep || $latest_ep=='' )
			{
				$ep = intval($ep_title, 10);
				$rawtext = $single_media['title'];
				$rawtext=iconv('gb2312', 'utf-8', $rawtext);
				$field_values = array( "title_id" =>$ID,  "ep" =>$ep, "ep_title" => $ep_title, "video_url" =>$video_url , "rawtext" =>$rawtext, "fetch_id" => $fetch_id, "fetch_time" =>time());
				$db->autoExecute("av_video", $field_values);  
				$field_values = array("latest_ep_title"=>$ep_title, "ep_qty"=>intval($ep_title, 10));  
				$db->autoExecute("av_title", $field_values, "UPDATE", "title_id = $ID");   
				$send_msg =  '你关注的视频： '.$title_result['title_name'].' 已经更新至 '.$ep_title;
				$sql="insert into av_msg_queue(msg,create_time,user_id) select '$send_msg', $fetch_start, 
						     user_id from av_subs where title_id = $ID"; 
				$db->query($sql);
			} 	
		}

		else
		{
			$pic_name = preg_replace('/\W*/', '', $title[0]); //去不了_
			$my_prefix = 'http://pic.qingkong.net/pic/'.$pic_name[0].'/';
			$my_suffix = '.jpg';
			$title_img_url = $my_prefix.$pic_name.$my_suffix ;
			$test = $http->get_header($title_img_url);
	 		if($test['code']!=200)
		 		$title_img_url = '';
			$title_img_url = iconv('gb2312', 'utf-8', $title_img_url);
			$list_url=$single_media[0]?$single_media[0]:"http://qingkong.net/anime/$pic_name/";
			$test = $http->get_header($list_url);
			if($test['code']!=200)
				$list_url = '';
			$list_url = iconv('gb2312', 'utf-8', $list_url);
			$field_values = array( "title_name" => $name, "ep_qty" =>intval($ep_title, 10), "latest_ep_title" =>$ep_title, "title_img_url" =>$title_img_url, "list_url" =>$list_url);  
			$db->autoExecute("av_title", $field_values);  
			//   		 $sql = "SELECT title_name FROM av_title WHERE title_name = '$name' ";  
			//   	 $title_result = $db->getRow($sql);
		}
	}
}
 
$latest_ep_url=$all_media[0]['url'];
$field_values = array("latest_ep_url"=>$latest_ep_url , "last_fetch_started"=>$fetch_start, "last_fetch_ended"=>time());  
$db->autoExecute("av_fetch", $field_values, "UPDATE", "fetch_id=$fetch_id");  
　
?>


