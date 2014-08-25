<?php
define('INIT_NO_WECHAT',1);
define('INIT_NO_SMARTY',1);
define('INIT_NO_USERS',1);
define('INIT_NO_SESSION',1);
include '/var/www/html/chris/includes/init.php';

$cron=$db->getAll("select *, UNIX_TIMESTAMP() tm from av_fetch order by fetch_id");

echo "<div>当前时间:".date("Y-m-d H:i:s")."</div>";
foreach( $cron as $v){
	$en=$v['last_fetch_ended']; $st=$v['last_fetch_started']; $tm=$v['tm'];

	echo "<div>$v[fetch_id] - 定时:".s2w($v['period'])."...上次触发:".s2w($tm-$st).'前';
	if($st>0){
		echo '...最后执行时间:'.date("Y-m-d H:i:s",$v['last_fetch_started'])."..".
		($en==$st?'瞬间完成':($en>$st?($en-$st."秒完成"):"没有完成！")).
		"-----$v[phpfilename] ";
	}
	if( $v['phpfilename']!='') echo "<a href=\"$v[phpfilename]\" target=view>马上执行</a>";
	echo " <a href=\"$v[url]\" target=view>来源页</a>";
	echo "</div>";
}
echo '<iframe name="view" width=90% height=300></iframe>';
die();

function s2w( $s){
	if( $s<60) return $s."秒";
	if( $s<=3600) return round($s/60).'分钟';
	if( $s<86400) return round($s/3600).'天';
	if( $s<2592000) return round($s/86400)."天";
	return '&gt;30天';
}
?>