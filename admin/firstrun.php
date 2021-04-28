<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once('../base.php'); 
require_once('../db.php'); 

if(!file_exists("../config/config.php") 
and (!empty($_POST['mysqlserver']) 
and !empty($_POST['database']) 
and !empty($_POST['databasepswd']) 
and !empty($_POST['databaseuser']) 
and !empty($_POST['tableprefix']) 
and !empty($_POST['username']) 
and !empty($_POST['password'])
or
isset($_POST['use_sqlite']) and $_POST['use_sqlite']
)){
	$sqlite = $_POST['use_sqlite'];
	$conn = DB::ConnectWithCred($_POST['use_sqlite']?1:0,$_POST['mysqlserver'], $_POST['database'], $_POST['username'], $_POST['password']);
	// create table
	if(!$sqlite) {
		$sql = "CREATE TABLE ".$_POST['tableprefix']."gif"." (
			id INT(12) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
			url VARCHAR(255) NOT NULL UNIQUE,
			video VARCHAR(255) UNIQUE,
			original_name VARCHAR(255) NOT NULL,
			description TEXT NOT NULL,
			category VARCHAR(100),
			title VARCHAR(255),
			original_url VARCHAR(255),
			date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
			thumbnail VARCHAR(255)
		) ENGINE=MyISAM";
	}
	else {
		$sql = "CREATE TABLE ".$_POST['tableprefix']."gif"." (
			id INTEGER PRIMARY KEY AUTOINCREMENT,
			url TEXT NOT NULL UNIQUE,
			video TEXT UNIQUE,
			original_name TEXT NOT NULL,
			description TEXT NOT NULL,
			category TEXT,
			title TEXT,
			original_url TEXT,
			date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
			thumbnail TEXT
		)";
	}
	$sth = $conn->prepare($sql);
	if(!$sth->execute())
		die("fail");
	
		

	if(!$sqlite){
		$sql = "CREATE FULLTEXT INDEX ".$_POST['tableprefix']."fulltext
			ON ".$_POST['tableprefix']."gif (original_name, description, title)";
		$sth = $conn->prepare($sql);
		if(!$sth->execute())
			die("fail");
	} else {
		$sql = "CREATE VIRTUAL TABLE ".$_POST['tableprefix']."fulltext USING fts5(gif_id, original_name, description, title)";
		$sth = $conn->prepare($sql);
		if(!$sth->execute())
			die("fail");
		;
	}
	if(!$sqlite) {
		$sql = "CREATE TABLE ".$_POST['tableprefix']."keywords (
			gif_id INT(12) UNSIGNED,
			keyword VARCHAR(30) NOT NULL,
			FOREIGN KEY (gif_id)
			REFERENCES ".$_POST['tableprefix']."gif (id)
			ON DELETE CASCADE,
			PRIMARY KEY (gif_id,keyword)
		) ENGINE=MyISAM";
	} else {
		$sql = "CREATE TABLE ".$_POST['tableprefix']."keywords (
			gif_id INTEGER UNSIGNED,
			keyword TEXT NOT NULL,
			FOREIGN KEY (gif_id)
			REFERENCES ".$_POST['tableprefix']."gif (id)
			ON DELETE CASCADE,
			PRIMARY KEY (gif_id,keyword)
		)";
	}
	
	$sth = $conn->prepare($sql);
	if(!$sth->execute())
		die("fail");
		$sql = "SELECT id,url, video,original_name,description, title, original_url, category FROM ".$_POST['tableprefix']."gif ORDER BY id DESC"; 
		$sth = $conn->prepare($sql);
		echo $sth->execute();

		$conn->commit();

	$config='<?php
		$CONFIG = array (
		\'mysql_server\' =>\''.$_POST['mysqlserver'].'\',
		\'database\' =>\''.$_POST['database'].'\',
		\'database_user\' =>\''.$_POST['databaseuser'].'\',
		\'database_pswd\' =>\''.$_POST['databasepswd'].'\',
		\'table_prefix\' =>\''.$_POST['tableprefix'].'\',
		\'username\' =>\''.$_POST['username'].'\',
		\'password\' =>\''.md5($_POST['password']).'\',
		\'db_type\' => \''.($sqlite?1:0).'\',
		)
?>';
	$fp = fopen('../config/config.php', 'w');
			fwrite($fp, $config);
			fclose($fp);
		//	header("location: ../");
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, height=device-height,initial-scale=1.0">
		<title>Gif me</title>
		<script src="js/masonry.pkgd.min.js"></script>
		<style>
		body{
			background:grey;
		}
		</style>
	</head>
<body>
	<h2>Config</h2>
	<form action="" method="post">
	mysql server  <br /><input type="text" name="mysqlserver" value="localhost"/> <br /><br />

	database <br /><input type="text" name="database" /> <br /><br />
	database username <br /><input type="text" name="databaseuser" /> <br /><br />
	database password <br /><input type="password" name="databasepswd" /> <br /><br />

	table prefix <br /><input type="text" name="tableprefix"/> <br /><br />
	admin username 	<br /><input type="text" name="username" /> <br /><br />
	admin password <br /><input type="password" name="password" /> <br /><br />
	Use Sqlite <br /><input type="checkbox" name="use_sqlite" checked/> <br /><br />

<input type="submit" /> 
	</form>

</body>
</html>

