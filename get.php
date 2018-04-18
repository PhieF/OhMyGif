<?php
require_once('config/config.php'); 
require_once('gif_db_helper.php');
$gif_db_helper = new GifDBHelper();
$page = 0;
if(!empty($_GET["page"]))
	$page = $_GET["page"];
if(!empty($_GET["query"])){
	echo json_encode($gif_db_helper->search($_GET["query"],0));
}
else
	echo json_encode($gif_db_helper->getLast(0));
?>
