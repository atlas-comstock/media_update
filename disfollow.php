<?php 
include 'includes/init.php';
$title_id = intval( $_GET['title_id']); 
$user_id = $_COOKIE['av_user_id'];
if( $user_id ){
 $sql = "DELETE FROM av_subs WHERE user_id = $user_id AND title_id=$title_id ";
 $db->query($sql); 
}
 die();
 ?>