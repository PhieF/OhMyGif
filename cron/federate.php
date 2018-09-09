<?php
/*
 * To federate
 * create a "federated" text file in this directory
 * fill it with one instance per line. Example : 
 * https://omg.phie.fi
 * 
 * (instances must start with http)
 * create a cron job for federation, please one time a day should be enough
 * 
 * 
 * */
require_once('../config/config.php'); 
require_once('../gif_db_helper.php');
$gif_db_helper = new GifDBHelper();
$to_federate = explode("\n", file_get_contents("federated"));

foreach($to_federate as $url){
	if(!empty($url)){
		$string = file_get_contents($url."/get.php?export=1");
		if(!empty($string)){
			$data = json_decode($string, true);
			foreach(array_reverse($data) as $item){
				if(substr($item['url'], 0, 4 ) !== "http")
					$item['url'] = $url."/".$item['url'];
				echo $item['url'];
				if(!preg_match('/^[a-f0-9]{32}$/', basename($item['url'],".gif")) || count($gif_db_helper->getByGifFileName(basename($item['url']))) == 0) //if basename is a md5 string, adding even our gif  (they don't have the instance domain name when local...)
				    $gif_db_helper->addToDb($item['url'], null,$item['original_name'], $item['description']);
					
				
				
			}
		}
	}
}
?>
