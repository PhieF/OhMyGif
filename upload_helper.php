<?php
require_once('config/config.php'); 
require_once 'vendor/autoload.php';
class UploadHelper { 
	function saveUploadedGif($file){
		$uploaddir = 'uploads/';
		$uploadfile = $uploaddir . md5(uniqid()).".gif";
		$result =array();
		if($file['type'] != "image/gif"){
			$result["status"] = 1;
			echo "img should be a gif";
			return $result;
		}
		if (move_uploaded_file($file['tmp_name'], $uploadfile)) {
			$result["status"] = 0;
			$result["url"] = $uploadfile;
			$result["webm"] = $this->toWebM($uploadfile);
			$result["original_name"] = $file['name'];

		} else {
			$result["status"] = 1;
			
		}
		//print_r($file);
		return $result;
		
	}
	
	function downloadGif($url){
		$uploaddir = 'uploads/';
		$uploadfile = $uploaddir . md5(uniqid()).".gif";
		$result =array();
		$result["status"] = 1;
		if(file_put_contents($uploadfile,file_get_contents($url))){
			$result["status"] = 0;
			$result["url"] = $uploadfile;
			$result["webm"] = $this->toWebM($uploadfile);
			$result["original_name"] = "";
		}
		return $result;
		
	}
	
	function toWebM($path){
		$ffmpeg = \FFMpeg\FFMpeg::create(array(
			'timeout' => 3600
		));
		$video = $ffmpeg->open($path);
		$format = new \FFMpeg\Format\Video\WebM();
		$format->setAdditionalParameters(array("-auto-alt-ref","0"));
		if($video
			->save($format, "uploads/".basename($path,"gif")."webm"))
		 return "uploads/".basename($path,"gif")."webm";
	}
}


?>
