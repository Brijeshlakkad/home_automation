<?php
include_once('config.php');
include_once('user_data.php');
function getAccountDetails($gotData){
  return getUserDataUsingID($gotData->con,$gotData->userID);
}
function updateUser($gotData){
  $name=$gotData->user->name;
  $email=$gotData->user->email;
  $address=$gotData->user->address;
  $city=$gotData->user->city;
  $contact=$gotData->user->contact;
  $sql="UPDATE user SET name='$name',address='$address',city='$city',mobile='$contact' WHERE email='$email'";
  $result=mysqli_query($gotData->con,$sql);
  if($result && (mysqli_affected_rows($gotData->con)==1))
  {
    $gotData->error=false;
    $gotData->errorMessage="null";
    $u=getUserDataUsingEmail($gotData->con,$email);
    if($u->error) return $u;
    $gotData->userUpdated=$u;
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="Try again!";
  return $gotData;
}
function changePassword($gotData){
  $newPassword=$gotData->newPassword;
  $userID=$gotData->userID;
  $sql="UPDATE user SET password='$newPassword' WHERE id='$userID'";
  $result=mysqli_query($gotData->con,$sql);
  if($result && (mysqli_affected_rows($gotData->con)==1))
  {
    $gotData->error=false;
    $gotData->errorMessage="null";
    $gotData->responseMessage="Password is changed.";
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="Try again!";
  return $gotData;
}
if(isset($_REQUEST['action'])){
  $action=$_REQUEST['action'];
  if($action==0 && isset($_REQUEST['userID'])){
    $userID=$_REQUEST['userID'];
    $gotData=(object) null;
    $gotData->userID=$userID;
    $gotData->con=$con;
    $gotData=getAccountDetails($gotData);
    $gotData->con=(object) null;
    echo json_encode($gotData);
  }else if($action==1 && isset($_REQUEST['name']) && isset($_REQUEST['email']) &&  isset($_REQUEST['address']) && isset($_REQUEST['city']) && isset($_REQUEST['contact']))
  {
    $name=$_REQUEST['name'];
    $email=$_REQUEST['email'];
    $address=$_REQUEST['address'];
    $city=$_REQUEST['city'];
    $contact=$_REQUEST['contact'];
    $gotData=(object) null;
    $gotData->error=false;
    $gotData->errorMessage="null";
    $gotData->con=$con;
    $gotData->user->name=$name;
    $gotData->user->email=$email;
    $gotData->user->address=$address;
    $gotData->user->city=$city;
    $gotData->user->contact=$contact;
    $gotData=updateUser($gotData);
    $gotData->con=(object) null;
    echo json_encode($gotData);
  }else if($action==2 && isset($_REQUEST['oldPassword']) && isset($_REQUEST['newPassword']) && $_REQUEST['userID']){
    $oldPassword=$_REQUEST['oldPassword'];
    $newPassword=$_REQUEST['newPassword'];
    $userID=$_REQUEST['userID'];
    $gotData = (object) null;
    $gotData->error=false;
    $gotData->errorMessage="null";
    $gotData->userID=$userID;
    $gotData->newPassword=$newPassword;
    $gotData->con=$con;
    $got=getAccountDetails($gotData);
    if(!$got->error){
      $gotData->password=$got->password;
      if($gotData->password!=$oldPassword){
        $gotData->error=true;
        $gotData->errorMessage="Old password is incorrect.";
      }
      else if($gotData->password==$newPassword){
        $gotData->error=true;
        $gotData->errorMessage="Password should not match previous password.";
      }else{
        $gotData=changePassword($gotData);
      }
      $gotData->con=(object) null;
      echo json_encode($gotData);
    }else{
      echo json_encode($got);
    }
  }
}
?>
