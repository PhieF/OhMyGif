<?php
require_once('config/config.php'); 
require_once('db.php'); 

class GifDBHelper { 
	function addToDb($url, $keywords, $originalName, $description, $title, $category, $originalUrl, $video, $thumbnail){
		$error="";
		global $CONFIG;
		$conn = DB::Connect();
		$description = $conn->quote($description);
		$originalName = $conn->quote($originalName);
		$url = $conn->quote($url);
        $category = $conn->quote($category);
        $title = $conn->quote($title);
        $originalUrl = $conn->quote($originalUrl);
        $video = $conn->quote($video);
        $thumbnail = $conn->quote($thumbnail);

		$sql = "INSERT INTO ".$CONFIG["table_prefix"]."gif (url, video, original_name, description, category, title, original_url, thumbnail)
		VALUES ($url, $video, $originalName, $description, $category, $title,$originalUrl, $thumbnail)";

		$sth = $conn->prepare($sql);
		$sth->execute();
		$last_id = $conn->lastInsertId();
		$conn->commit();
		if($error)
			return $error;
		if($CONFIG["db_type"] == 1){//sqlite
			$sql = "INSERT INTO ".$CONFIG["table_prefix"]."fulltext (gif_id, original_name, description, title)
			VALUES ('$last_id', $originalName, $description, $title)";
			$conn = DB::Connect();
			$sth = $conn->prepare($sql);
			$sth->execute();
			$conn->commit();
			if($error)
				return $error;	
		}
	}
	
	function getLast($start){
		return $this->get($start+0,$start+20);
		
	}
	
	function get($start, $limit){
		global $CONFIG;

		$conn = DB::Connect();
		

		$sql = "SELECT id,url, video,original_name,description, title, original_url, category, thumbnail FROM ".$CONFIG["table_prefix"]."gif ORDER BY id DESC"; 
		if(isset($start)){
			if(!is_int($start))
				$start = 0;
			if(!is_int($limit))
				$limit = $start + 40;
			$sql .= " limit ".$start.",".$limit;
		}

		$sth = $conn->prepare($sql);

		$result = $sth->execute();
		$return = array();
		while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
			array_push($return,$row);
		}
		
		
		return $return;
		
	}
	function getCount(){
		global $CONFIG;
		$conn = DB::Connect();
		// Check connection
		if (isset($conn->connect_error)) {
				die("Connection failed: " . $conn->connect_error);
		}
		$sql = "SELECT count(id) as count FROM ".$CONFIG["table_prefix"]."gif";
		$result = $conn->query($sql);
		return $row = $result->fetchColumn();
	}
	
	function getByGifFileName($filename){
		global $CONFIG;

		$conn = DB::Connect();
		// Check connection
		if (isset($conn->connect_error)) {
				die("Connection failed: " . $conn->connect_error);
		}
		 
		$filename = $conn->quote('%/'.$filename);
		$sql = "SELECT id, url, video, original_name, description, thumbnail FROM ".$CONFIG["table_prefix"]."gif WHERE url like $filename or original_url like $filename"; 

		$sth = $conn->prepare($sql);

		$result = $sth->execute();
		$return = array();
		while($row = $sth->fetch(PDO::FETCH_ASSOC)) {
				array_push($return,$row);
		}

		return $return;

	}

	function getGif($id){
		global $CONFIG;

		$conn = DB::Connect();
		// Check connection
		if (isset($conn->connect_error)) {
				die("Connection failed: " . $conn->connect_error);
		}
		 
		$id = $conn->quote($id);
		$sql = "SELECT id, url, video, original_name, description, thumbnail FROM ".$CONFIG["table_prefix"]."gif WHERE id = $id"; 

		$sth = $conn->prepare($sql);

		$result = $sth->execute();
		$return = array();
		while($row = $sth->fetch(PDO::FETCH_ASSOC)) {
				array_push($return,$row);
		}

		return $return;

	}


	
	function isUrlInDB($url){
		global $CONFIG;

		$conn = DB::Connect();
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		$url = $conn->quote($url);
		$sql = "SELECT COUNT(url) FROM ".$CONFIG["table_prefix"]."gif WHERE url = '$url' OR original_url = '$url'";
		$result = $conn->query($sql);
		
		return $result->fetchColumn() > 0;
	}
	
	function search($query,$start){
		global $CONFIG;
		if(!isset($start))
			$start= 0 ;
		if(!is_int($start))
			$start = 0;
		if($CONFIG["db_type"] == 1)
			return $this->searchSQLite($query, $start);
		
		else 
			return $this->searchMySQL($query, $start);		
	}

	function searchSQLite($query,$start){
		
		global $CONFIG;

		$conn = DB::Connect();
		if (isset($conn->connect_error)) {
			die("Connection failed: " . $conn->connect_error);
		}
		
		$query = $conn->quote($query);

		$sql = "SELECT id,url, video,original_name,description,category, title, original_url, thumbnail  FROM ".$CONFIG["table_prefix"]."gif WHERE id IN (SELECT gif_id FROM ".$CONFIG["table_prefix"]."fulltext WHERE ".$CONFIG["table_prefix"]."fulltext MATCH $query ORDER BY rank)";
		$sth = $conn->prepare($sql);

		$result = $sth->execute();
		$return = array();
		while($row = $sth->fetch(PDO::FETCH_ASSOC)) {
				array_push($return,$row);
		}
		
		return $return;
	}

	function searchMySQL($query,$start){
		global $CONFIG;

		$conn = DB::Connect();
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		if(!is_int($start))
			$start = 0;
		$query = $conn->quote($query);

		$sql = "SELECT id,url, video,original_name,description,category, title, original_url, thumbnail  FROM ".$CONFIG["table_prefix"]."gif WHERE MATCH (original_name, description, title) AGAINST ('$query') OR description like '%$query%' OR title like '%$query%' OR original_name like '%$query%' limit ".$start.",".($start+20);
		$result = $conn->query($sql);
		$return = array();
		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				array_push($return,$row);
			}
		}
		return $return;
		
	}
}


?>
