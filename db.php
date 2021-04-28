<?php
class DB {
    public static function Connect(){
        require_once('config/config.php'); 
        global $CONFIG;

        return DB::ConnectWithCred($CONFIG["db_type"], $CONFIG["mysql_server"], $CONFIG["database"], $CONFIG["database_user"], $CONFIG["database_pswd"]);
    }

    public static function ConnectWithCred($db_type,$sql_server, $db, $user, $password){
        if(!isset($db_type) || $db_type == 0)
            $dbco = new PDO("mysql:host=".$sql_server.";dbname=".$db, $user, $password);
        else{
            $dbco = new PDO('sqlite:'.dirname(__FILE__).'/data/database.sqlite');
            }    
        $dbco->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $dbco->beginTransaction();
        return $dbco;
    }
    

}