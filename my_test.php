<?php 
		    $url = 'http://www.tuling123.com/openapi/api?key=2de48f93cfa6fb3fff1c0ede2ac8b953&info=打发士大夫';
			$content= file_get_contents($url);
			preg_match_all('/{"code":100000,"text":(.+?)"}/is',$content, $core);
print_r($core);
?>


