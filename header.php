<?php

include 'includes/init.php';


$http=new http();

//echo '<pre>';
$url = 'http://qingkong.net/anime/quanzhilieren2011/';
$c=$http->get_header('$url');
//print_r( $c);
echo $c['code'].'<br>';//successs 200
$d= $http->get_header("http://pic.qingkong.net/pic/h/hhh.jpg");
//print_r($d);
echo $d['code'];//fail 404
//echo '</pre>';
?>