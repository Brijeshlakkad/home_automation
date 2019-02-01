<?php
require_once('config.php');
require_once('home_data.php');
function checkEmailExists($gotData)
{
  $email=$gotData->user->email;
  $sql="SELECT * FROM user where email='$email'";
  $result=mysqli_query($gotData->con,$sql);
  if($result){
    if(mysqli_num_rows($result)==1){
      $gotData->user->emailExists=true;
      return $gotData;
    }
    $gotData->user->emailExists=false;
    return $gotData;
  }
  $gotData->error=true;
  return $gotData;
}
function checkContactExists($gotData)
{
  $contact=$gotData->user->contact;
  if(!checkContactValid($contact)){
    $gotData->error=true;
    $gotData->errorMessage="contact is not valid";
    return $gotData;
  }
  $sql="SELECT * FROM user where mobile='$contact'";
  $result=mysqli_query($gotData->con,$sql);
  if($result){
    if(mysqli_num_rows($result)>0){
      $gotData->error=true;
      $gotData->user->contactExists=true;
      $gotData->errorMessage="contact already exists";
      return $gotData;
    }
    $gotData->user->contactExists=false;
    return $gotData;
  }
  $gotData->error=true;
  return $gotData;
}
function checkContactValid($contact){
  if(preg_match('/^[0-9]{10}+$/', $contact)){
    return true;
  }
  return false;
}
if(isset($_REQUEST['email']))
{
  $gotData=(object) null;
  $gotData->user=(object) null;
  $gotData->error=false;
  $gotData->user->email=$_REQUEST['email'];
  $gotData->con=$con;
  $gotData=checkEmailExists($gotData);
  $gotData->con=(object) null;
  echo json_encode($gotData);
}else if(isset($_REQUEST['contact']))
{
  $gotData=(object) null;
  $gotData->user=(object) null;
  $gotData->error=false;
  $gotData->user->contact=$_REQUEST['contact'];
  $gotData->con=$con;
  $gotData=checkContactExists($gotData);
  $gotData->con=(object) null;
  echo json_encode($gotData);
}
?>
