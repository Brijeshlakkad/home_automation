<?php
session_start();
function protect_anything($str)
{
	$str = addslashes($str);
	$str = stripslashes($str);
	return $str;
}
function checkSession(){
  if(!isset($_SESSION['Userid']))
  {
    header("Location:index.php");
  }
}
?>
