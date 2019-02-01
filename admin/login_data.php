<?php
require_once("../config.php");
function check_user($gotData){
  $email=$gotData->user->email;
  $password=$gotData->user->password;
  if($email=="brijeshlakkad22@gmail.com" && $password="123456bB")
  {
    $gotData->user->userID="-99";
    $gotData->user->userType="admin";
    $gotData->user->location="#!/admin/home";
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessege="Email or Password is wrong!";
  return $gotData;
}
if(isset($_REQUEST['email']) && isset($_REQUEST['password']))
{
  $email=$_REQUEST['email'];
  $password=$_REQUEST['password'];
  $gotData=(object) null;
  $gotData->user=(object) null;
  $gotData->con=$con;
  $gotData->user->email=$email;
  $gotData->user->password=$password;
  $gotData=check_user($gotData);
  $gotData->con=(object) null;
  echo json_encode($gotData);
}
?>
