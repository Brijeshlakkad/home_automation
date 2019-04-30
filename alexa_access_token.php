<?php
require_once("config.php");
function getAccessToken($con){
  $getTokenSQL="SELECT * FROM access_token WHERE id='1'";
  $getTokenResult=mysqli_query($con,$getTokenSQL);
  if($getTokenResult){
    $row=mysqli_fetch_array($getTokenResult);
    $oldToken=$row['token'];
    $newToken=$oldToken+rand(1,10);
    $updateTokenSQL="UPDATE access_token SET token='$newToken' WHERE id='1'";
    $updateTokenResult=mysqli_query($con,$updateTokenSQL);
    return $newToken;
  }
  return 10001;
}
function checkUserTokenExists($code){
  $sql="SELECT * FROM user_token WHERE user_id='$code'";
  $result=mysqli_query($con,$sql);
  if($result){
    if(mysqli_num_rows($result)==1){
      return true;
    }
  }
  return false;
}
if(isset($_REQUEST['code'])){
  $code=$_REQUEST['code'];
  $token=getAccessToken($con);
  if(checkUserTokenExists($code)){
    $sql="UPDATE user_token SET token='$token' WHERE user_id='$code'";
  }else{
    $sql="INSERT INTO user_token(user_id,token) VALUES('$code','$token')";
  }
  $result=mysqli_query($con,$sql);
  $gotData=(object) null;
  if($result){
    $gotData->access_token=$token;
  }
  echo json_encode($gotData);
}
?>
