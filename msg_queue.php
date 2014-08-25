 <?php 
include 'includes/init.php';
$user_id = $_COOKIE['av_user_id'];
$sql = "select  * from av_msg_queue where done=0 and user_id=$user_id";
$msg_queue = $db->getAll($sql);
foreach( $msg_queue as $single_msg )
{
	if( $single_msg['msg'] )
	{
		echo $single_msg['msg'].'<br>';
		echo $single_msg['create_time'].'<br>';
		$single_msg['done']=1;
		$field_values = array("done"=>1);  
		$db->autoExecute("av_msg_queue", $field_values, "UPDATE", "user_id=$user_id");  
		sleep(1);
	}
	else
	{
		$single_msg['count']++;
		$single_msg['try_time']=time();
		$field_values = array("count"=>$single_msg['count'],"try_time"=>time());  
		$db->autoExecute("av_msg_queue", $field_values, "UPDATE", "user_id=$user_id"); 
	}
}
?>
