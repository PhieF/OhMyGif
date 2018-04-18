<?php
require_once('config/config.php'); 

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
			$result["original_name"] = $file['name'];

		} else {
			$result["status"] = 1;
			
		}
		//print_r($file);
		return $result;
		
	}
}


?>
