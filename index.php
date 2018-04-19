<?php

if(!file_exists("config/config.php")){
	header("location: admin/firstrun.php");
}
else{
	
	require_once('config/config.php'); 
	require_once('gif_db_helper.php');
	$gif_db_helper = new GifDBHelper();
	$original_name="";
	if(!empty($_FILES['giffile']['name'])){
		require_once('upload_helper.php'); 
		$upload = new UploadHelper();
		$result = $upload->saveUploadedGif($_FILES['giffile']);
		if($result["status"] != 0)
			die("An error occured");
		$_POST["url"] = $result["url"];
		$original_name = $result["original_name"];
		
		
	}
	if(!empty($_POST["url"])){
		$keywords=array();
		if(!empty($_POST["keywords"])){
			$keywords = explode(",",$_POST["keywords"]);
		}
		$gif_db_helper->addToDb($_POST["url"], $keywords,$original_name, $_POST["description"]);
		
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, height=device-height,initial-scale=1.0">
		<title>Oh my gif !</title>
		<script src="js/masonry.pkgd.min.js"></script>
		 <link rel="stylesheet" type="text/css" href="design/design_index.css">
		 
	</head>
<body>


	<header>
		<h1>
		<a href="?">OMG</a>
		</h1>
		<form action="" method="get"><input name="query" value="" hint="search"/></form>
		<a href="" class="upload" onclick="displayUploadForm(); return false;"><img style="vertical-align:middle" src="img/upload.svg"/><span>Upload</span></a>
	</header>
	<div id="main">
	<div id="gif-grid">
		
	</div>
	</div>
	<div id="footer">
		<a href="https://github.com/PhieF/OhMyGif">Sources</a>
		 <a href="get.php?export=1" download>Export</a>

	</div>
	<script src="js/index.js">


	</script>
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
