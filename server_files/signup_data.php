<?php
require_once("config.php");
require_once('check_user.php');
$gotData=(object) null;
function createUser($gotData){
  $got=checkEmailExists($gotData->con,$gotData->email);
  if($got->error) return $got;
  $got=checkMobileExists($gotData->con,$gotData->mobile,false,null);
  if($got->error) return $got;
  $name=$gotData->name;
  $city=$gotData->city;
  $email=$gotData->email;
  $mobile=$gotData->mobile;
  $password=$gotData->password;
  $address=$gotData->address;
  $sql="INSERT INTO `user`(`name`, `city`, `email`, `mobile`, `address`,`password`) VALUES ('$name','$city','$email','$mobile', '$address','$password')";
  $k=mysqli_query($gotData->con,$sql);
  if($k)
  {
    $gotData->error=false;
    $gotData->errorMessage="Registation Successfully";
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="Registation Problem...try again...";
  return $gotData;
}
if(isset($_REQUEST['action'])){
  $action=$_REQUEST['action'];
  $gotData->con=$con;
  $gotData->error=false;
  $gotData->errorMessage=null;
if($action==1 && isset($_REQUEST['name']) && isset($_REQUEST['city']) && isset($_REQUEST['email']) && isset($_REQUEST['mobile']) && isset($_REQUEST['password']) && isset($_REQUEST['address'])){
    $gotData->name=$_REQUEST['name'];
    $gotData->city=$_REQUEST['city'];
    $gotData->email=$_REQUEST['email'];
    $gotData->mobile=$_REQUEST['mobile'];
    $gotData->password=$_REQUEST['password'];
    $gotData->address=$_REQUEST['address'];
    $gotData=createUser($gotData);
    $gotData->con=(object) null;
    echo json_encode($gotData);
    exit();
  }else
  {
    $gotData->error=true;
    $gotData->errorMessage="Wrong Input Data";
    echo json_encode($gotData);
    exit();
  }
}else
{
  $gotData->error=true;
  $gotData->errorMessage="Wrong Input Data";
  echo json_encode($gotData);
  exit();
}
?>
