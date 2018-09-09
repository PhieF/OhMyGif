<?php
require_once('config/config.php'); 
require_once('gif_db_helper.php');
$gif_db_helper = new GifDBHelper();
$page = 0;
header('Content-Type: application/json');
header('Content-Disposition: attachment; filename="data.json"');
if(!empty($_GET["page"]))
	$page = $_GET["page"];
if(!empty($_GET["query"])){
	echo json_encode($gif_db_helper->search($_GET["query"],$_GET["start"]+0));
}
else if(!empty($_GET["export"])){
	echo json_encode($gif_db_helper->get(null, null));
}
else
	echo json_encode($gif_db_helper->getLast($_GET["start"]));
?>
