<?php
require_once("config.php");
require_once("user_data.php");
function check_user($gotData){
  $email=$gotData->user->email;
  $password=$gotData->user->password;
  $sql="SELECT * FROM user where email='$email' and password='$password'";
  $result=mysqli_query($gotData->con,$sql);
  if($result && (mysqli_num_rows($result)==1))
  {
    $gotData->error=false;
    $u=getUserDataUsingEmail($gotData->con,$email);
    if($u->error){
      $gotData->error=true;
      $gotData->errorMessege="Email or Password is wrong!";
      return $gotData;
    }
    $gotData->user->userID=$u->id;
    $gotData->user->location="#!/customer";
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
