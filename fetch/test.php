<?php 

define('INIT_NO_WECHAT',1);
define('INIT_NO_SMARTY',1);
define('INIT_NO_USERS',1);
define('INIT_NO_SESSION',1);
include '../includes/init.php';

/*
$s='<div class="summary">
		<div class="cont">
		<div class="title1">
		        		<a href="http://qingkong.net/anime/04/50544.html" title="属性同好会02" target="_blank">属性同好会02</a>
        <span class="xinfan">[一月新番]</span>                                <span class="write">[2014-01-14]</span>
        </div>
        </div>
        </div><div class="summary">
		<div class="cont">
		<div class="title1">
		                	<span class="xinfan">[<a href="http://qingkong.net/anime/GuanLanGaoShou/" title="灌篮高手" target="_blank">灌篮高手</a>]</span>
        		<a href="http://qingkong.net/anime/ab/50541.html" title="灌篮高手HD重制版46" target="_blank">灌篮高手HD重制版46</a>
                                        <span class="write">[2014-01-13]</span>
        </div>
        </div>
        </div><div class="summary">
		<div class="cont"><div class="title1">
		        		<a href="http://qingkong.net/anime/03/50543.html" title="攻壳机动队入门02" target="_blank">攻壳机动队入门02</a>
        <span class="xinfan"><a href="xxxx">[一月新番]</a></span>                                <span class="write">[2014-01-14]</span>
        </div>
        </div>
        </div>';
	*/
$http = new http();
$re=$http->get("http://qingkong.net/anime/renew/");
$s=$re['body'];

preg_match_all( '/<div class="title1">\s*(.+?)\s*<\/div>/is', $s, $m);

echo '<pre>';
foreach( $m[1] as $k=>$v){
	preg_match( '/<span class="xinfan">.*?<a href="([^"]+)".*?<\/span>/', $v, $n);

	$list_url=$n[1]?$n[1]:'';
	if( $list_url)	$v=str_replace( $n[0], '', $v);

	preg_match('/<a href="(?<url>[^"]+)" title="(?<title>[^"]+)" .*?<span class="write">\[(?<dd>[^\]]+)/s', $v, $p);

	$p['title']=iconv('gb2312', 'utf-8', $p['title']);
	
	$p[0]=$list_url;
	if( !preg_match('/\d$|\d\)$/', $p['title'])) {
		echo "\n $k=>";
		print_r($p);
	}
}

echo '</pre>';
?>


