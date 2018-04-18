<?php

if(!file_exists("config/config.php")){
	header("location: admin/firstrun.php");
}
else{
	
	require_once('config/config.php'); 
	require_once('gif_db_helper.php');
	$gif_db_helper = new GifDBHelper();
	$original_name="";
	if(!empty($_FILES['giffile'])){
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
	</head>
<body>
	<script>
		
	</script>
	<style>
		a{
			color:white;
			text-decoration:none;
		}
		 body{
			margin:0px;
			padding:0px;
			height:100%;
		}
	.grid-item { width: 300px; }
	.grid-item img{
		width:100%;
	}
	#gif-grid{
		margin:auto;
	}
	#main{
		
		margin:auto;
		margin-top:50px;
		/*max-width:1000px;*/
	}
	header{
		z-index:10;
		width:100%;
		top:0px;
		left:0px;
		position:fixed;
		padding:0px;

		margin:0px;
		background:black;
		color:white;
	}
	header h1{
				padding:5px;
				padding-left:30px;

		margin:0px;
	}
	header input{
		position:absolute;
		left:50%;
		margin-left:-100px;
		top:7px;
		 border: 2px solid red;
		border-radius: 4px;
	}
	header .upload{
				position:absolute;
		right:10px;
		top:7px;
		color:white;
		text-decoration:none;

	}
	.sub{
		display:none;
		position:absolute;
		bottom:0px;
		height:50px;
		width:300px;
		background:rgba(0, 0, 0, 0.8);;
	}
	.sub img{
		height:35px;
		width:45px;
		
	}
	#upload-div{
		display: none;
		z-index:11;
		width:100%;
		height:100%;
		position:fixed;
		top:0;
		left:0;
		background:rgba(0, 0, 0, 0.8);
	}
	#form-container{
		background:white;
		padding:20px;
		top:50%;
		position:relative;
		max-width:800px;
		width:100%;
		margin:auto;
		margin-top:-250px;

	}
	input{
		 border: 2px solid black;
		border-radius: 4px;
	}
	#giffile{
				 border: 0px;

	}
	</style>
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
	<script>
		var grid = document.getElementById("gif-grid");
		var msnry = new Masonry( '#gif-grid', { itemSelector: ".grid-item", columnWidth: 300 ,fitWidth: true});
		function getParameterByName(name, url) {
				if (!url) {
					url = window.location.href;
				}
				name = name.replace(/[\[\]]/g, "\\$&");
				var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
					results = regex.exec(url);
				if (!results) return null;
				if (!results[2]) return '';
				return decodeURIComponent(results[2].replace(/\+/g, " ").replace(/%2F/g, "/"));
			}
			function hideUploadForm(){
				document.getElementById("upload-div").style.display="none";
			}
			function displayUploadForm(){
				document.getElementById("upload-div").style.display="block";
			}
			function createItemElement(url){
				
					var item = document.createElement("div");
					item.classList.add("grid-item");
					var img = document.createElement("img");
					img.src=url;
					img.onload= function(){
											msnry.layout();

					}
					var subT = document.createElement("div");
					subT.classList.add("sub");
					var dl = document.createElement("a");
					dl.href=url
					var dlImg = document.createElement("img");
					dlImg.src= "img/download.svg";
					dl.appendChild(dlImg);
					subT.appendChild(dl);
					item.appendChild(img);
					item.appendChild(subT);
					item.onmouseover=function(){
						this.getElementsByClassName("sub")[0].style.display="block";
					}
					item.onmouseout=function(){
						this.getElementsByClassName("sub")[0].style.display="none";
					}
					return item;
				}
				function addItemElement(url){
					var item = createItemElement(url)
					grid.appendChild(item);
					msnry.appended( item );
				}
				var getJSON = function(url, callback) {
					var xhr = new XMLHttpRequest();
					xhr.open('GET', url, true);
					xhr.responseType = 'json';
					xhr.onload = function() {
					  var status = xhr.status;
					  if (status === 200) {
						callback(null, xhr.response);
					  } else {
						callback(status, xhr.response);
					  }
					};
					xhr.send();
				};
				var url = "get.php";
				var query = getParameterByName("query");
				if(query != null)
					url += "?query="+query;
				getJSON(url,function(status, data){
					for(var gif of data){
							addItemElement(gif['url']);
					}
				});
		

	

	</script>
	<div id="upload-div">
		<div id="form-container">
	<h2>Upload gif</h2>
	<form action="" method="post" enctype="multipart/form-data">
		Image <br /><br /><input type="file" name="giffile" id="giffile"><br /><br />
		Url (if no file) <br /><input type="text" name="url" /> <br /><br />

		Description <br /><input type="text" name="description" /> <br /><br />
		
		<button onclick="hideUploadForm(); return false;" type=button>Cancel</button><input type="submit" /> 
	</form>
	</div>
	</div>
</body>
</html>
<?php

}
?>
