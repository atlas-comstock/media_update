<?php 
include 'includes/init.php';
$title_id = intval( $_GET['title_id']);
$user_id = $_COOKIE['av_user_id'];
if( $user_id ){
 $field_values = array( "user_id" => $user_id, "title_id" =>$title_id);  
 $sql = "SELECT title_id FROM av_subs WHERE user_id = $user_id ";  
 $title_result = $db->getAll($sql);
 $followed_flag = 0;
 if( $title_result )
	{ 
	 	 foreach( $title_result as $sigle_result )
		{  
		 if( $sigle_result[title_id]==$title_id )
			{
			 $followed_flag = 1; 
			 break;
			}
		}
		if( $followed_flag == 0 ) 
			$db->autoExecute("av_subs", $field_values);
	}
 else
	 $db->autoExecute("av_subs", $field_values);		
}
 
die();
 ?>