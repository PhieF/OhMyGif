<?php
error_reporting(E_ALL^E_NOTICE);
require_once('gif_db_helper.php'); 
require_once('upload_helper.php'); 

if(!empty($_GET['gif_id'])){
    $gif_db_helper = new GifDBHelper();
    $gif = $gif_db_helper->getGif($_GET['gif_id']);
    $upload = new UploadHelper();
    $gifPath = $upload->toGif($gif[0]['video']);
    header('Content-Type: image/gif');
    readfile($gifPath); 
}
?>