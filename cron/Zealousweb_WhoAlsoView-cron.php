<?php
require_once ("../app/Mage.php");
$app = Mage::app('default');

$config  = Mage::getConfig()->getResourceConnectionConfig("default_setup");

$dbinfo = array("host" => $config->host,
            "user" => $config->username,
            "pass" => $config->password,
            "dbname" => $config->dbname
);

$hostname = $dbinfo["host"];
$user = $dbinfo["user"];
$password = $dbinfo["pass"];
$dbname = $dbinfo["dbname"];

$model_moodle = mysql_connect($hostname,$user,$password)  
        or die("Unable to connect to MySQL");       
        //select a database to work with
        $selected = mysql_select_db($dbname,$model_moodle)
          or die("Could not select moodle");
        //execute the SQL query and delete record from before 30days to cureent_time. 
        $result = mysql_query("DELETE FROM `who_also_view` WHERE `current_time` < ( NOW( ) - INTERVAL 30 DAY ) ");
        //close the connection
        mysql_close($model_moodle);
        echo "done";
?>