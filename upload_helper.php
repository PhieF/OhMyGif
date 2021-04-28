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
                        $result["thumbnail"] = $this->getThumbnail($uploadfile);

		} else {
			$result["status"] = 1;
			
		}
		//print_r($file);
		return $result;
		
	}
	
	function downloadGif($url, $webm){
		$uploaddir = 'uploads/';
		$name = md5(uniqid());
		$uploadfile = $uploaddir . $name.".gif";
		$result =array();
		$result["status"] = 1;
		if(file_put_contents($uploadfile,tor_file_get_contents($url))){
			$result["status"] = 0;
			$result["url"] = $uploadfile;
			if(empty($webm))
				$result["webm"] = $this->toWebM($uploadfile);
			else{
			   $uploadfile = $uploaddir . $name.".webm";
			   if(file_put_contents($uploadfile,tor_file_get_contents($webm)))
				$result["webm"] = $uploadfile;
			}
                        $result["thumbnail"] = $this->getThumbnail($uploadfile);
			$result["original_name"] = "";
		}
		return $result;
		
	}
	
	function toWebM($path){
		echo "starting encoding";
		$ffmpeg = \FFMpeg\FFMpeg::create(array(
			'ffmpeg.binaries'  =>'/usr/bin/ffmpeg',
      			'ffprobe.binaries' => '/usr/bin/ffprobe',
			'timeout' => 3600
		));
		$video = $ffmpeg->open($path);
		$format = new \FFMpeg\Format\Video\WebM();
		$format->setAdditionalParameters(array("-auto-alt-ref","0"));
		if($video
			->save($format, "uploads/".basename($path,"gif")."webm"))
		 return "uploads/".basename($path,"gif")."webm";
	}
	function getThumbnail($path){
                echo "creating thumbnail";
                $ffmpeg = \FFMpeg\FFMpeg::create(array(
                        'ffmpeg.binaries'  =>'/usr/bin/ffmpeg',
                        'ffprobe.binaries' => '/usr/bin/ffprobe',
                        'timeout' => 3600
                ));
                $video = $ffmpeg->open($path);
		if($video->frame(FFMpeg\Coordinate\TimeCode::fromSeconds(0))->save("uploads/".basename($path,"gif")."png"))
                 return "uploads/".basename($path,"gif")."png";
	}
}


?>
