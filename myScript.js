function showHint(str)
{
var xmlhttp;
if (str.length==0)
  { 
  document.getElementById("txtHint").innerHTML="";
  return;
  }
if (window.XMLHttpRequest)
  {
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    document.getElementById("txtHint").innerHTML=xmlhttp.responseText;
    }
  }
xmlhttp.open("GET","hint.php?input="+str,true);
xmlhttp.send();
}


function httpGet(theUrl)
{ 
    var xmlHttp = null;
    xmlHttp = new XMLHttpRequest();
    xmlHttp.open( "GET", theUrl, false );
    xmlHttp.send();
}

function SearchGet(theUrl)
{ 
    var xmlHttp = null;
    xmlHttp = new XMLHttpRequest();
    xmlHttp.open( "GET", theUrl, false );
    xmlHttp.send();
	if( xmlHttp.responseText=='Null')
	    alert("找不到你查找的動漫");
    else
	   javascript:window.location.href = theUrl;
}

 $("#search").click(function(){
	 var temp = $('#input_search').val();
	 var url = 'search.php?title_name='+temp;
	 SearchGet(url);
});