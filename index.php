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
        $thumbnail = "";
	if(!empty($_FILES['giffile']['name'])){
		require_once('upload_helper.php'); 
		$upload = new UploadHelper();
		$result = $upload->saveUploadedGif($_FILES['giffile']);
		if($result["status"] != 0)
			die("An error occured");
		$_POST["url"] = $result["url"];
		$original_name = $result["original_name"];
		$video = $result["webm"];
                $thumbnail = $result["thumbnail"];
		
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
		$gif_db_helper->addToDb($url, $keywords,$original_name, $_POST["description"], $_POST["category"],$_POST["title"],$url, $video, $thumbnail);
		
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
                <script src="js/api2.js?t=rerer"></script>
		 <link rel="stylesheet" type="text/css" href="design/design_index.css">
		 
	</head>
<body>
<script>
function parseFragment() {
    const fragmentString = (window.location.hash || "?");
    //alert(fragmentString);
    return new URLSearchParams(fragmentString.substring(Math.max(fragmentString.indexOf('?'), 0)));
}

function assertParam(fragment, name) {
    const val = fragment.get(name);
    return val;
}

function handleError(e) {
    console.error(e);
    document.getElementById("container").innerText = "There was an error with the widget. See JS console for details.";
}
  try {
        const qs = parseFragment();
        const  widgetId = assertParam(qs, 'widgetId'); //"customwidget_%40phie%3Alostpod.me_1611237180448";
        const userId = assertParam(qs, 'userId'); 
        let isSticky = false;

        // Set up the widget API as soon as possible to avoid problems with the client
        const widgetApi = new mxwidgets.WidgetApi(widgetId);
 //       widgetApi.requestCapability(mxwidgets.MatrixCapabilities.StickerSending);
//	widgetApi.requestCapabilityToSendMessage("m.video");
widgetApi.requestCapabilityToSendMessage("m.image");
        widgetApi.on("ready", function() {
            // Fill in the basic widget details now that we're allowed to operate.
           /* document.getElementById("main").innerHTML = "Hello <span id='userId'></span>!<br /><br />"
                + "Currently stuck on screen: <span id='stickyState'></span><br /><br />"
                + "<button onclick='toggleSticky()'>Toggle sticky state</button>";*/

            // Fill in the user ID using innerText to avoid XSS
//            document.getElementById("userId").innerText = userId;
	    console.log("reaaady");
            // Update the UI and ensure that we end up not sticky to start
            //sendStickyState();
	   widgetApi.transport.send(mxwidgets.WidgetApiFromWidgetAction.SendImage, {
               name: "pet",
               file: "mastodon-icon.png",
               content: {
                 msgtype: "m.image",
                 url: "https://omg.phie.ovh/img",
                 info: {
            	 mimetype: "image/png"
                 }
               }
           }).then();
        });

        // Start the widget as soon as possible too, otherwise the client might time us out.
        widgetApi.start();
	console.log("staaart");
        function toggleSticky() {
            // called by the button when clicked - toggle the sticky state
            isSticky = !isSticky;
            sendStickyState();
		
        }

        function updateStickyState() {
            document.getElementById("stickyState").innerText = isSticky.toString();
        }

        function sendStickyState() {
            updateStickyState(); // update first to make the UI go faster than the request
            widgetApi.setAlwaysOnScreen(isSticky).then(function(r) {
                console.log("[Widget] Client responded with: ", r);
            }).catch(function(e) {
                handleError(e);
            });
        }
    } catch (e) {
        handleError(e);
    }
</script>


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
