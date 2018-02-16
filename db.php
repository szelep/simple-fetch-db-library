<?php

class connection {

    protected function dbconnect() {

        $mysql_server = ""; 
        $mysql_admin = "";     
        $mysql_pass = "";   
        $mysql_db = "";  
        @ $con = new mysqli($mysql_server, $mysql_admin, $mysql_pass, $mysql_db);
        if (mysqli_connect_errno()) {
            echo 'Connection error';
        }
        $con->query("SET NAMES 'utf8'");
        return $con;
    }

}

?>