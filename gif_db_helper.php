<?php
require_once('config/config.php'); 

class GifDBHelper { 
	function addToDb($url, $keywords, $originalName, $title, $description, $category, $originalUrl){
		$error="";
		global $CONFIG;
		$conn = new mysqli($CONFIG["mysql_server"], $CONFIG["database_user"], $CONFIG['database_pswd'], $CONFIG["database"]);
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		$description = mysqli_real_escape_string($conn,$description);
		$originalName = mysqli_real_escape_string($conn,$originalName);
		$url = mysqli_real_escape_string($conn,$url);
        $category = mysqli_real_escape_string($conn,$category);
        $title = mysqli_real_escape_string($conn,$title);
        $originalUrl = mysqli_real_escape_string($conn,$originalUrl);

		$sql = "INSERT INTO ".$CONFIG["table_prefix"]."gif (url,original_name, description, category, title, original_url)
		VALUES ('$url', '$originalName', '$description', '$category', '$title','$originalUrl')";

		if ($conn->query($sql) !== TRUE) {
			$error= "Error: " . $sql . "<br>" . $conn->error;
		}

		$conn->close();
		return $error;
	}
	
	function getLast($start){
		return $this->get($start+0,$start+20);
		
	}
	
	function get($start, $limit){
		global $CONFIG;

		$conn = new mysqli($CONFIG["mysql_server"], $CONFIG["database_user"], $CONFIG['database_pswd'], $CONFIG["database"]);
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}

		$sql = "SELECT id,url,original_name,description, title, original_url, category FROM ".$CONFIG["table_prefix"]."gif ORDER BY id DESC"; 
		if(isset($start)){
			if(!is_int($start))
				$start = 0;
			if(!is_int($limit))
				$limit = $start + 40;
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


        function getByGifFileName($filename){
                global $CONFIG;

                $conn = new mysqli($CONFIG["mysql_server"], $CONFIG["database_user"], $CONFIG['database_pswd'], $CONFIG["database"]);
                // Check connection
                if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                }
                 
                $filename = mysqli_real_escape_string($conn,$filename);
                $sql = "SELECT id,url,original_name,description FROM ".$CONFIG["table_prefix"]."gif WHERE url like '%/$filename'"; 


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


	
	function isUrlInDB($url){
		global $CONFIG;

		$conn = new mysqli($CONFIG["mysql_server"], $CONFIG["database_user"], $CONFIG['database_pswd'], $CONFIG["database"]);
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		$url = mysqli_real_escape_string($conn,$url);
		$sql = "SELECT url FROM ".$CONFIG["table_prefix"]."gif WHERE url = '$url' OR original_url = '$url'";
		$result = $conn->query($sql);
		return $result->num_rows > 0;
	}
	
	function search($query,$start){
		global $CONFIG;

		$conn = new mysqli($CONFIG["mysql_server"], $CONFIG["database_user"], $CONFIG['database_pswd'], $CONFIG["database"]);
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		$start = mysqli_real_escape_string($conn,$start);
		if(!is_int($start))
			$start = 0;
		$query = mysqli_real_escape_string($conn,$query);

		$sql = "SELECT id,url,original_name,description,category, title, original_url  FROM ".$CONFIG["table_prefix"]."gif WHERE MATCH (original_name, description) AGAINST ('$query' IN NATURAL LANGUAGE MODE) limit ".$start.",".($start+20);
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
