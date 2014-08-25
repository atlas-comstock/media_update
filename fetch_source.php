<?php 
define('INIT_NO_WECHAT',1);
define('INIT_NO_SMARTY',1);
define('INIT_NO_USERS',1);
define('INIT_NO_SESSION',1);
include 'includes/init.php'; //../
require_once('fetch/pinyin_table.php');
//$fetch_id=12;
//$fetch=$db->getRow("select latest_ep_url,url from av_fetch where fetch_id=$fetch_id");
//$url=$fetch['url'];
//$latest_ep_url=$fetch['latest_ep_url'];
//if( !$url)  exit("url is empty ");
$url='http://qingkong.net/anime/animation/';
$http = new http();
$re=$http->get($url);
$contents=$re['body'];
if( !$contents )  exit("contents is empty ");

$fetch_start = time();
 
preg_match_all('/<table cellspacing="1" cellpadding="1" width="100%" align="center" border="0">(.+)<\/tbody>/is',$contents, $core );  
preg_match_all('/<a href="(?<url>[^"]+)" title="(?<title>[^"]+)" target="_blank">/is', $core[1][0], $media);
 
foreach( $media['url']  as $subscript=> $single_list_url )
{
 	$name =  $media['title'][$subscript];
	$name = str_replace("'",'',$name); //  preg_replace('/\'/', '', $name); 
	$title= get_pinyin_array($name);
	$name=iconv('gb2312', 'utf-8', $name);
	$pic_name = preg_replace('/\W*/', '', $title[0]); //È¥²»ÁË_
	$my_prefix = 'http://pic.qingkong.net/pic/'.$pic_name[0].'/';
	$my_suffix = '.jpg';
	$title_img_url = $my_prefix.$pic_name.$my_suffix ;
	$test = $http->get_header($title_img_url);
	if($test['code']!=200)
		 	$title_img_url = '';
	$title_img_url = iconv('gb2312', 'utf-8', $title_img_url);
	$test = $http->get_header($single_list_url);
	if($test['code']!=200)
			$single_list_url = '';
	$single_list_url = iconv('gb2312', 'utf-8', $single_list_url);
	echo $subscript.'<br>';
	echo $name.'  pic url:   '.$title_img_url.' url:    '. $single_list_url.'<br>'.'<br>';
 	$field_values = array( "title_name" => $name, "title_img_url" =>$title_img_url, "list_url" =>$single_list_url);  
 	$db->autoExecute("av_title", $field_values);  
}
echo '@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@2endl';
sleep(100);
exit ("end");
?>


