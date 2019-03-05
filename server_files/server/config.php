<?php
define('DB_HOST','localhost');
define('DB_NAME','homeauto_new');
define('DB_USER','homeauto_iotproj');
define('DB_PASSWORD','iotProject$$##');
global $con;
$con=new mysqli(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
if(mysqli_connect_error())
{
  printf("Connection failed: %s\n",mysqli_connect_error());
  exit();
}
?>
