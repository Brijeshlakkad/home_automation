<?php
function checkUserExists($gotData){
  $con=$gotData->con;
  $email=$gotData->email;
  $sql="SELECT * FROM user WHERE email='$email'";
  $result=mysqli_query($con,$sql);
  if($result && mysqli_num_rows($result)){
    return $gotData;
  }
  $gotData->error = true;
  $gotData->errorMessage = "User does not exists";
  return $gotData;
}
function checkEmailExists($con,$email){
  $gotData=(object) null;
  $gotData->error = false;
  $sql="SELECT * FROM user WHERE email='$email'";
  $result=mysqli_query($con,$sql);
  if($result && (mysqli_num_rows($result)==0)){
    return $gotData;
  }else if(mysqli_num_rows($result)>0){
    $gotData->error=true;
    $gotData->errorMessage="Email is already registered with us.";
    $gotData->field="email";
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="Try again!";
  return $gotData;
}
function checkMobileExists($con,$mobile,$isModifying,$email){
  $gotData=(object) null;
  $gotData->error = false;
  if($isModifying){
    $sql="SELECT * FROM user WHERE mobile='$mobile' AND email!='$email'";
  }else{
    $sql="SELECT * FROM user WHERE mobile='$mobile'";
  }
  $result=mysqli_query($con,$sql);
  if($result && (mysqli_num_rows($result)==0)){
    return $gotData;
  }else if(mysqli_num_rows($result)>0){
    $gotData->error=true;
    $gotData->errorMessage="Mobile is already registered with us.";
    $gotData->field="mobile";
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="Try again!";
  return $gotData;
}
?>
