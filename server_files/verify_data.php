<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
function getUserID($gotResult)  // gets user id
{
  $sql="SELECT * FROM user where email='$gotResult->email'";
  $check=mysqli_query($gotResult->con,$sql);
  if($check && (mysqli_num_rows($check)==1))
  {
    $row=mysqli_fetch_array($check);
    $gotResult->userID=$row['id'];
    return $gotResult;
  }
  $gotResult->error=true;
  $gotResult->errorMessage="You do not have account in OUR app. Please registor yourself at OUR app";
  return $gotResult;
}

function getRoomID($gotResult)  // gets room id usind room name and user id
{
  $sql="SELECT * FROM room where uid='$gotResult->userID' and roomname='$gotResult->roomName'";
  $check=mysqli_query($gotResult->con,$sql);
  if($check && (mysqli_num_rows($check)==1))
  {
    $row=mysqli_fetch_array($check);
    $gotResult->roomID=$row['id'];
    return $gotResult;
  }
  $gotResult->error=true;
  $gotResult->errorMessage="You do not have room named ".$gotResult->roomName;
  return $gotResult;
}
function verifyData($gotResult){ // verifies data
  if(!filter_var($gotResult->email, FILTER_VALIDATE_EMAIL))
  {
    $gotResult->error=true;
    $gotResult->errorMessage="email address is not verified with our application.";
    return $gotResult;
  }
  if($gotResult->deviceName=="null")
  {
    $gotResult->error=true;
    $gotResult->errorMessage="Device name is not specified";
    return $gotResult;
  }else{
    $gotResult->deviceName=str_replace(" ","",$gotResult->deviceName);
  }
  if($gotResult->roomName=="null")
  {
    $gotResult->error=true;
    $gotResult->errorMessage="Room name is not specified";
    return $gotResult;
  }else{
    $gotResult->roomName=str_replace(" ","",$gotResult->roomName);
  }
  $gotResult=getUserID($gotResult);
  if($gotResult->error==true) return $gotResult;
  $gotResult=getRoomID($gotResult);
  if($gotResult->error==true) return $gotResult;
  $gotResult->error=false;
  $gotResult->errorMessage="null";
  $gotResult->deviceName = ucfirst($gotResult->deviceName);
  $gotResult->roomName = ucfirst($gotResult->roomName);
  return $gotResult;
}
?>
