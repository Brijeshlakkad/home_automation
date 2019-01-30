<?php
require_once("config.php");
function checkUserExists($con,$email)
{
  $sql="SELECT * FROM user where email='$email'";
  $check=mysqli_query($con,$sql);
  if($check && (mysqli_num_rows($check)==1))
  {
    return true;
  }
  return false;
}
function create_user($gotData){
  if(checkUserExists($gotData->con,$gotData->user->email)){
    $gotData->error=false;
    $gotData->message="Account is already created";
    $gotData->errorMessage=null;
    return $gotData;
  }
  $name=$gotData->user->name;
  $email=$gotData->user->email;
  $password=$gotData->user->password;
  $address=$gotData->user->address;
  $city=$gotData->user->city;
  $contact=$gotData->user->contact;
  $sql="INSERT INTO user(name,email,password,address,city,mobile) VALUES('$name','$email','$password','$address','$city','$contact')";
  $result=mysqli_query($gotData->con,$sql);
  if($result)
  {
    $gotData->error=false;
    $gotData->message="Account created";
    $gotData->errorMessage=null;
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="Try again!";
  return $gotData;
}
if(isset($_REQUEST['name']) && isset($_REQUEST['email']) && isset($_REQUEST['password']) && isset($_REQUEST['address']) && isset($_REQUEST['city']) && isset($_REQUEST['contact']))
{
  $name=$_REQUEST['name'];
  $email=$_REQUEST['email'];
  $password=$_REQUEST['password'];
  $address=$_REQUEST['address'];
  $city=$_REQUEST['city'];
  $contact=$_REQUEST['contact'];
  $gotData=(object) null;
  $gotData->con=(object) null;
  $gotData->user=(object) null;
  $gotData->error=false;
  $gotData->errorMessage=null;
  $gotData->con=$con;
  $gotData->user->name=$name;
  $gotData->user->email=$email;
  $gotData->user->password=$password;
  $gotData->user->address=$address;
  $gotData->user->city=$city;
  $gotData->user->contact=$contact;
  $gotData=create_user($gotData);
  $gotData->con=(object) null;
  echo json_encode($gotData);
}
?>
