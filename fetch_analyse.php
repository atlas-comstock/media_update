<?php 
define('INIT_NO_WECHAT',1);
define('INIT_NO_SMARTY',1);
define('INIT_NO_USERS',1);
define('INIT_NO_SESSION',1);
include '../includes/init.php';
$fetch_id=12;
$fetch=$db->getRow("select latest_ep_url,url from av_fetch where fetch_id=$fetch_id");
$url=$fetch['url'];
$latest_ep_url=$fetch['latest_ep_url'];
if( !$url)
   exit("url is empty ");
$contents = file_get_contents($url);
if( !$contents )
   exit("contents is empty ");

preg_match_all('/<div class="title1">(\w+)</div>',$contents,$core );
$clear_code = preg_replace('<span class="xinfan">\w+</span>/', '', $core[1]);  
preg_match_all('/<a href="([^"]+)"\s*title="([^"]+)"/', $clear_code, $media );//取出视频名称（含集数） 
$skip=0;

$video_url = $media[1][$skip];
if( $video_url == $latest_ep_url )
   exit("No update video ");
else
{
	$latest_ep_url = $video_url;
	$localtime =  date( "y-m-d ", time());
	preg_match_all( '/<span class="write">([^>]+)</' , $clear_code, $times );//取出更新时间

	for( $i=0; $times[1][$i]; $i++ )
	{
		for( $j=3, $k=0 ;  $times[1][$i][$j]!=']'; $j++, $k++ )//判斷是否今天更新
		{
			if( $times[1][$i][$j]!=$localtime[$k] )
				break;
		}
		if( $times[1][$i][$j]==']' )//在今天更新
		{
			if( !preg_match('/[0-9)]+$/',$media[2][$skip] ) )
   {
	    $rawtext .= $media[2][$skip];
	    ++$skip;
		 continue;
    }
			$name = preg_replace('/[0-9]*[(0-9]*[)]*$/', '', $media[2][$i+$skip]);
			require_once('pinyin_table.php');
			$title= get_pinyin_array($name);
			$pic_name = preg_replace('/\W*/', '', $title[0]); //去不了_
			$list_url = 'http://qingkong.net/anime/'.$pic_name.'/';
			$my_prefix = 'http://pic.qingkong.net/pic/'.$pic_name[0].'/';
			$my_suffix = '.jpg';
			$title_img_url = $my_prefix.$pic_name.$my_suffix ;
			$ep = preg_replace('/[^()0-9]+/', '', $media[2][$i+$skip]);
			$video_url = $media[1][$i+$skip];

			$sql = "SELECT title_name FROM av_title WHERE title_name = '$name' ";  
			$title_result = $db->getRow($sql);

			if( !$title_result ) //空的 
			{
				$field_values = array( "title_name" => $name, "ep_qty" =>intval($ep, 10), "latest_ep_title" =>$ep, "title_img_url" =>$title_img_url, "list_url" =>$list_url);  
				$db->autoExecute("av_title", $field_values);  
				$sql = "SELECT title_name FROM av_title WHERE title_name = '$name' ";  
		     	$title_result = $db->getRow($sql);
			}

			$field_values = array( "title_id" =>$title_result['title_id'],  "ep_title" => $ep, "video_url" =>$video_url , "rawtext" =>$rawtext, "fetch_id" => $fetch_id, "fetch_time" =>time() );  
			$db->autoExecute("av_video", $field_values);  

			$field_values = array( "ep_title" => $ep, "video_url" =>$video_url , "rawtext" =>$rawtext, "fetch_id" => $fetch_id, "fetch_time" =>time());  
			$db->autoExecute("av_fetch", $field_values);  
			
			$sql ="insert into av_msg_queue (user_id,msg) 
				select  user_id,CONCAT('你关注的视频： ' ,t.title_name,' 已经更新至 ',av_video.latest_ep_title)  from av_subs s
				inner join av_title t on s.title_id = t.title_id
				where s.title_id = $title_result[title_id]";
			$db->query($sql);



	/*	$sql = "select  user_id, t.title_name,av_video.latest_ep_title   from av_subs s
				inner join av_title t on s.title_id = t.title_id
				where s.title_id = $title_result[title_id] ";  
			$msg_queue_result = $db->getAll($sql);  */
		/*	foreach( $msg_queue_result as $value )
			{
				$field_values = array( "user_id" =>$value[$i][$user_id], "msg" => '你关注的视频： ' .$value[$i][$title_name]. ' 已经更新至 ' .$value[$i][$latest_ep_title]);
			$db->autoExecute("av_msg_queue", $field_values);  
			}
*/

			/*insert into av_msg_queue (user_id,msg) 
				select  user_id, '你关注的视频： ' + t.title_name + ' 已经更新至 ' + av_video.latest_ep_title   from av_subs s
				inner join av_title t on s.title_id = t.title_id
				where s.title_id = $title_result['title_id'];*/

		}
	}
}
?>


