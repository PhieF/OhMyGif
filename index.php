<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
    header('Access-Control-Max-Age: 1000');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
   
if(!file_exists("config/config.php")){
	header("location: admin/firstrun.php");
}
else{
	
	require_once('config/config.php'); 
	require_once('gif_db_helper.php');
	$gif_db_helper = new GifDBHelper();
	$original_name="";
	$video = "";
	if(!empty($_FILES['giffile']['name'])){
		require_once('upload_helper.php'); 
		$upload = new UploadHelper();
		$result = $upload->saveUploadedGif($_FILES['giffile']);
		if($result["status"] != 0)
			die("An error occured");
		$_POST["url"] = $result["url"];
		$original_name = $result["original_name"];
		$video = $result["webm"];
		
	}
	if(!empty($_POST["url"])){
		$keywords=array();
		if(!empty($_POST["keywords"])){
			$keywords = explode(",",$_POST["keywords"]);
		}
		//discover
		$url = $_POST["url"];
		if(substr($url,0, strlen("https://giphy.com/gifs/")) == "https://giphy.com/gifs/"){
			echo "https://media.giphy.com/media/".substr($url,strrpos($url, "-")+1)."/giphy.gif";
		}
		$gif_db_helper->addToDb($url, $keywords,$original_name, $_POST["description"], $_POST["category"],$_POST["title"],$url, $video);
		
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, height=device-height,initial-scale=1.0">
		<title>Oh my gif !</title>
		<script src="js/masonry.pkgd.min.js"></script>
		<script src="js/gifffer.min.js"></script>
		<script src="js/jquery.min.js"></script>
		<script src="js/mastodon.js"></script>
		 <link rel="stylesheet" type="text/css" href="design/design_index.css">
		 
	</head>
<body>


	<header>
		<h1>
		<a href="?">Oh My Gif !</a>
		</h1>
		<form action="" method="get"><input placeholder="Search a gif" name="query" value="" hint="search"/></form>
		<a href="" class="upload" onclick="displayUploadForm(); return false;"><img style="vertical-align:middle" src="img/upload.svg"/><span>Upload</span></a>
	</header>
	<div id="main">
	<div id="gif-grid">
		
	</div>
	</div>
	<div id="footer">
		<a href="https://github.com/PhieF/OhMyGif">Sources</a>
		 <a href="get.php?export=1" download>Export</a>
		 
		 <?php echo $gif_db_helper->getCount();?> gif in this instance

	</div>
	<script src="js/index.js">


	</script>
	<div id="mastodon-auth" class="mastodon-dialog">
		<img src="img/mastodon.png"/>
		Connect to your Mastodon account<br />
		<br />
		Identification tokens will be stored locally, not on our server
		<form action="">
		<input id="mastodon-instance" type="text" placeholder="instance address (https://instance)"/>
		<br />
		<br />
		<a onclick="$('#mastodon-auth').hide(); return false;" href="">Cancel</a><button onclick="onClickAuth($('#mastodon-instance').val()); return false;">Connect</button>
		</form>
	</div>
	<div id="mastodon-post" class="mastodon-dialog">
		<img src="img/mastodon.png"/>
		Post to your Mastodon account<br />
		<br />
		<form action="">
		<input id="mastodon-post-message" placeholder="Message along with gif" type="text"/> <br />
		<input id="mastodon-post-via" type="checkbox" checked /> Promote: "Send via http://instance"
		 <br /><br />	
		<a onclick="$('#mastodon-post').hide(); return false;" href="">Cancel</a><button id="mastodon-post-button" type="submit">Send</button>
		</form>
	</div>
	<div id="upload-div">
		<div id="form-container">
	<h2>Upload gif</h2>
	<form action="" method="post" enctype="multipart/form-data">
		Image <br /><br /><input type="file" name="giffile" id="giffile"><br /><br />
		Url (if no file) <br /><input type="text" name="url" /> <br /><br />

		Description <br /><input type="text" name="description" id="description"/> <br /><br />
		
		<button onclick="hideUploadForm(); return false;" type=button>Cancel</button><input onclick="if(document.getElementById('description').value==''){alert('Description is mandatory'); return false;}"type="submit" /> 
	</form>
	</div>
	</div>
</body>
</html>
<?php

}
?>
