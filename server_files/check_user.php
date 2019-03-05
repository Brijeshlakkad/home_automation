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
?>