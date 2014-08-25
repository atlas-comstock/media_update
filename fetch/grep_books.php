<?php 
define('INIT_NO_WECHAT',1);
define('INIT_NO_SMARTY',1);
define('INIT_NO_USERS',1);
define('INIT_NO_SESSION',1);
include '../includes/init.php';
set_time_limit(0);
$sql = "SELECT ISBN FROM book_information order by ISBN desc LIMIT 1"; 
$all_books = $db->getRow($sql);
for( $ISBN = $all_books['ISBN']+1; $ISBN<9999999999999; ++$ISBN )//9999999999999
{
	if($ISBN%10==0)
		sleep(60);
	$url = 'http://api.douban.com/book/subject/isbn/'.$ISBN.'?apikey=0e093900e808e3012a1bab02b81aa903';
	$http = new http();
	$re = $http->get($url);
	$contents = $re['body'];
	print_r($contents); 
	if( !$contents || $contents=='bad isbn')
	{
	echo "contents is empty ";
	continue;
	}
	else
	{
	$xml = simplexml_load_string($contents); 
 	var_dump($xml);
	echo $xml;
	$title = $xml->title;
	$title = str_replace("'",'',$title);
	echo $title;
	$author = $xml->author->name;
	echo $author;
	if( $author )
	   $picture_url = $xml->link[2]->attributes()->href;
	echo $picture_url;
	$summary = $xml->summary;
	echo $summary ;
       if( $title )
		{
	$field_values = array( "book_name" => $title, "author" => $author, "summary" =>$summary, "ISBN" =>intval($ISBN, 10), "picture_url" =>$picture_url); 
	$db->autoExecute("book_information", $field_values);
		}
		sleep(5);
	}
}
?>