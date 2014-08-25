<?php
include 'includes/init.php';

echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="zh-cn" lang="zh-cn">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-Ua-Compatible" content="IE=EmulateIE7" />
<meta name="language" content="zh" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
';

echo '$_cookie[av_user_id]: <br><input size=30 value="'.$_COOKIE['av_user_id'].'"><BR>';
echo '$av_user数组：<br>';
echo '<textarea rows=16 cols=30>'.print_r( $av_user,true).'</textarea>';

echo '<style>a {padding:20px; margin:5px;}</style><br><br>';
$thisurl= 'rmcookie.php';
echo '<a href="'.$thisurl.'?removeck=1">移除COOKIE</a>';
echo '<a href="'.$thisurl.'?debug=1">设为调试机</a>';
echo '<a href="'.$thisurl.'?removedebug=1">取消调试机</a>';

if( $_GET['debug']){
	setcookie('willy', '1', time()+365*86400);
}elseif( $_GET['removedebug']){
	setcookie('willy', '');
}elseif( $_GET['removeck']){
	setcookie('av_user_id', '');
}
?>
