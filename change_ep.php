<?php 
include 'includes/init.php';

 $user_id = $_COOKIE['av_user_id']; 
 $watched_ep = intval( $_GET['watched_ep']);
 $title_id = intval( $_GET['title_id']);
 $sql = " UPDATE av_subs SET watched_ep = $watched_ep WHERE user_id = $user_id AND title_id=$title_id";
 $db->query($sql); 
 die();
 ?>