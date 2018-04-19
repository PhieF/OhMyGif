<?php
require_once('config/config.php'); 

class GifDBHelper { 
	function addToDb($url, $keywords, $originalName, $description){
		global $CONFIG;
		$conn = new mysqli($CONFIG["mysql_server"], $CONFIG["database_user"], $CONFIG['database_pswd'], $CONFIG["database"]);
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		$description = mysqli_real_escape_string($conn,$description);
		$originalName = mysqli_real_escape_string($conn,$originalName);
		$url = mysqli_real_escape_string($conn,$url);

		$sql = "INSERT INTO ".$CONFIG["table_prefix"]."gif (url,original_name, description)
		VALUES ('$url', '$originalName', '$description')";

		if ($conn->query($sql) !== TRUE) {
			echo "Error: " . $sql . "<br>" . $conn->error;
		}

		$conn->close();
		
	}
	
	function getLast($start){
		return $this->get($start,20);
		
	}
	
	function get($start, $limit){
		global $CONFIG;

		$conn = new mysqli($CONFIG["mysql_server"], $CONFIG["database_user"], $CONFIG['database_pswd'], $CONFIG["database"]);
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		
		$sql = "SELECT id,url,original_name,description FROM ".$CONFIG["table_prefix"]."gif ORDER BY id DESC"; 
		if($start != null){
			$start = mysqli_real_escape_string($conn,$start);
			if(!is_int($start))
				$start = 0;
			$limit = mysqli_real_escape_string($conn,$limit);
			if(!is_int($limit))
				$limit = $start + 20;
			$sql .= " limit ".$start.",".$limit;
		}
		
		
		$result = $conn->query($sql);
		$return = array();
		if ($result->num_rows > 0) {
			// output data of each row
			while($row = $result->fetch_assoc()) {
				array_push($return,$row);
			}
		}
		return $return;
		
	}
	
	function search($query,$start){
		global $CONFIG;

		$conn = new mysqli($CONFIG["mysql_server"], $CONFIG["database_user"], $CONFIG['database_pswd'], $CONFIG["database"]);
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		$start = mysqli_real_escape_string($conn,$start);
		if(!is_int($start))
			$start = 0;
		$query = mysqli_real_escape_string($conn,$query);

		$sql = "SELECT id,url,original_name,description  FROM ".$CONFIG["table_prefix"]."gif WHERE MATCH (original_name, description) AGAINST ('$query' IN NATURAL LANGUAGE MODE) limit ".$start.",".($start+20);
		$result = $conn->query($sql);
		$return = array();
		if ($result->num_rows > 0) {
			// output data of each row
			while($row = $result->fetch_assoc()) {
				array_push($return,$row);
			}
		}
		return $return;
		
	}
}


?>
