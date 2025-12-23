<?php

ini_set('display_errors',1);
ini_set ( ' display_startup_error',1);
error_reporting (E_ALL);





$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bank_system";

$conn = new mysqli ($servername , $username , $password , $dbname);

if ($conn-> connect_error) {

    die ("no connect" . $conn-> connect_error);
}

echo " connect";

?>