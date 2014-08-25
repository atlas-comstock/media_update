<?php
include 'includes/init.php';
$appid="wx1adc5c4db7b764f2";
$appsecret="7491ebf3e1beb6c9439a419ad52e37e6";

send_message(10, "你关注的视频： 《海贼王》已经更新至641集");
function send_message($user_id, $message)
{
global $db,$appid,$appsecret;
$http=new http();
$acctoken="";
if( file_exists("access_token.txt")){
	if(file_exists("token_expires.txt")){
		if( time()<file_get_contents("token_expires")) $acctoken=file_get_contents("access_token.txt");
	}
}
if( $acctoken==''){
	$m=file_get_contents("https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$appsecret");
	$m=json_decode( $m, true);
//	print_r($m);
	if( $m['access_token']) {
		
		//echo 'save acc token';
		$acctoken=$m['access_token'];
		file_put_contents("access_token.txt", $acctoken);
		file_put_contents("token_expires.txt", time()+intval($m['expires_in'])-10);
	}else{
		die('wx error');
	}
	//echo 'token created<br>';
	unset( $m);
}
$openid=$db->getOne("select openid from av_wx_visitor where visitor_id=$user_id"); 

$url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=$acctoken";
$data='{"touser":"'.$openid.'","msgtype":"text","text":{
														"content":"'.$message.'"
														}	
		}';
//echo $data;
        $ch = curl_init();  
  
        curl_setopt($ch, CURLOPT_URL, $url); 
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");  
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);  
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);  
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);  
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);  
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);  
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
        $tmpInfo = curl_exec($ch);  
        if (curl_errno($ch)) {  
            echo 'Error'.curl_error($ch);  
        }       
        curl_close($ch);
		return 1;
}