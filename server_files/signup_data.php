<?php
require_once("config.php");
function create_user($gotData){
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
  $gotData->user->name=ucfirst($name);
  $gotData->user->email=$email;
  $gotData->user->password=$password;
  $gotData->user->address=$address;
  $gotData->user->city=ucfirst($city);
  $gotData->user->contact=$contact;
  $gotData=create_user($gotData);
  $gotData->con=null;
  echo json_encode($gotData);
}
else{
  $gotData = (object) null;
  $gotData->error=true;
  $gotData->errorMessage="Try again!";
  $gotData->con=(object) null;
  echo json_encode($gotData);
  exit();
}
?>
