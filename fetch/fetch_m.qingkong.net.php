<?php
define('INIT_NO_WECHAT',1);
define('INIT_NO_SMARTY',1);
define('INIT_NO_USERS',1);
define('INIT_NO_SESSION',1);
include '/var/www/html/chris/includes/init.php';


$fetch_id=10;
$dbfetch=$db->getRow("select * from av_fetch where fetch_id=$fetch_id");
if( $dbfetch['suspended']) die();

$http=new http();

echo '<pre>';
$db->query("update av_fetch set last_fetch_started=UNIX_TIMESTAMP() where fetch_id=$fetch_id");

$c=$http->get("http://m.qingkong.net/mone/page_recent.html");

$st='<div class="updateList clearfix">';
$en='<div class="blank10">';
$m=strpos( $c['body'], $st); 
$n=strpos( $c['body'], $en); 
$c=substr( $c['body'], $m, $n-$m);
/*
<li><span>2014-01-09</span><strong>34</strong><a href="http://m.qingkong.net/mh/miwuzhongdehudie.html" title="迷雾中的蝴蝶" class="video" i="/upload/2011-08-21/2011821155710361.jpg">迷雾中的蝴蝶</a> [<a href="http://m.qingkong.net/mm/2287/151438.html" title="迷雾中的蝴蝶 第55话" class="red">第55话</a>] <em></em></li>
*/
$pattern='/<li><span>(<font[^>]+>)?([^<]+)(<\/font>)?<\/span><strong>(\d+)<\/strong><a href="([^"]+)" title="([^"]+)" class="video" i="([^"]+)">[^<]+<\/a> \[<a href="([^"]+)"[^>]+>([^<]+)<\/a>/';

preg_match_all( $pattern, $c, $m);

$date=2;
$num=4;
$title_url=5;
$title=6;
$img_url=7;
$ep_url=8;
$ep_title=9;

$tm=time();
$maxline=count($m[2])-1;
if( $dbfetch['latest_ep_url']){
	for( $k=0; $k<=$maxline; $k++){
		if($m[$ep_url][$k]==$dbfetch['latest_ep_url']) {
			if( $k==0){
				echo '<div>没有更新！</div>';
				$db->query("update av_fetch set last_fetch_ended=UNIX_TIMESTAMP() where fetch_id=$fetch_id");
				die();
			}
			$maxline=$k-1;
			break;
		}
	}
}
echo '<div>有'.($maxline+1).'项更新！</div>';

for( $k=$maxline; $k>=0; $k--){
	$data=array( 
		$num=>$m[$num][$k], 
		$title_url=>$m[$title_url][$k], 
		$title=>iconv('gb2312','utf-8', $m[$title][$k]), 
		$img_url=>'http://m.qingkong.net'.$m[$img_url][$k], 
		$ep_url=>$m[$ep_url][$k], 
		$ep_title=>iconv('gb2312','utf-8', $m[$ep_title][$k]) 
	);

	if( $data[$title]=='') {
		if( urlencode($m[$title][$k])=='%81%A2%E1%F7') {;
			$data[$title]='仮狩';
		}else{
			die();
		}
	}

	if( $data[$title]!=''){
		$dbtitle=$db->getRow("select * from av_comic_title where title='$data[$title]'");
		if( $dbtitle){ //title 已经在数据库
			$dbep=$db->getRow("select * from av_comic_ep where title_id=$dbtitle[title_id] and ep_title='$data[$ep_title]'");
			if( $dbep){ //ep_title已经在库
				echo $data[$num].'>>'.$data[$title]."在库($dbtitle[title_id]) ".$data[$ep_title]."在库 $dbep[video_id]\n";
				if( $dbep['video_url']==$data[$ep_url]){
				}else{
					//更新 av_comic_ep 的 video_url
					$db->query("update av_comic_ep set video_url='$data[$ep_url]',fetch_time=UNIX_TIMESTAMP() where video_id=$dbep[video_id]");
				}
			}else{
				echo $data[$num].'>>'.$data[$title]."在库($dbtitle[title_id]) $data[$ep_title] 不在库\n";
				$eps=$db->getRow("select max(ep) maxep,count(video_id) epcnt from av_comic_ep where title_id=$dbtitle[title_id]");
				$eps['maxep']=intval($eps['maxep'])+1;

				//更新 av_comic_ep
				$db->query("insert into av_comic_ep (title_id, ep, ep_title, video_url, fetch_id, fetch_time) values ($dbtitle[title_id], $eps[maxep], '$data[$ep_title]', '$data[$ep_url]', $fetch_id, UNIX_TIMESTAMP())");

				//更新 av_comic_title
				$db->query("update av_comic_title set mtime=UNIX_TIMESTAMP(),latest_ep_title='$data[$ep_title]',ep_qty=$eps[epcnt] where title_id=$dbtitle[title_id]");
			}

		}else{ //title 不存在
			$dbtitle=array(
				'title'=>$data[$title],
				'ep_qty'=>1,
				'latest_ep_title'=>$data[$ep_title],
				'is_ended'=>0,
				'title_img_url'=>$data[$img_url],
				'list_url'=>$data[$title_url],
				'mtime'=>$tm
			);
			echo strlen( $dbtitle[title]);
			echo $data[$num].'>>'."$dbtitle[title] 不在库\n"; 
			$c=$http->get( $data[$title_url]);
			$i=strpos( $c['body'], '<div class="plist pnormal" id="play_0">');
			$j=strpos( $c['body'], '</div>', $i);
			if( $i>0 && $j>$i){
				$c=substr( $c['body'], $i, $j-$i);

				preg_match_all( '/<li[^>]+><a href="([^"]+)" title="([^"]+)"/i', $c, $n);
				if( $n[0]){
					$latest_ep_title=$data[$ep_title];
					$ep=count( $n[2]);
					$db->autoExecute( 'av_comic_title', $dbtitle);
					$title_id=$db->insert_id();
					$dbep=array();
					foreach( $n[1] as $x=>$y){
						$n2=iconv('gb2312','utf-8', $n[2][$x]);
						$n1=$n[1][$x]; 
						array_unshift( $dbep, "($title_id,$ep,'$n2',0,'$n1',$fetch_id,$tm)");
						$ep--;
					}
					$db->query( "insert into av_comic_ep (title_id, ep, ep_title, is_ended, video_url, fetch_id, fetch_time) values ". implode(',',$dbep) );
					$db->query("update av_comic_title set ep_qty=".count($n[2]).",latest_ep_title='$latest_ep_title',mtime=UNIX_TIMESTAMP() WHERE title_id=$title_id");
				}
			}

		}
	}else{
		echo '<font color=red>'.urlencode($data[$title]).'错误!</font>'."\n";
	}

	$db->query("update av_fetch set latest_ep_url='$data[$ep_url]' where fetch_id=$fetch_id");
	flush();
	//echo $data[$num].' '.$data[$date].' '.$data[$title_url]."\n".
	//	$data[$title].' '.$data[$img_url].' '.$data[$ep_url].' '.$data[$ep_title]."\n";
}
$db->query("update av_fetch set last_fetch_ended=UNIX_TIMESTAMP() where fetch_id=$fetch_id");


//$c=iconv( 'gb2312','utf-8', $c);


echo '</pre>';

?>