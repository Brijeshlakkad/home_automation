<?php
require_once("room_data.php");
class Hardware{
  var $userID,$homeID,$roomID,$hwID,$hwName,$hwSeries,$hwIP,$error,$errorMessage;
  function getData($con,$sql){
    $check=mysqli_query($con,$sql);
    if($check && (mysqli_num_rows($check)==1))
    {
      $row=mysqli_fetch_array($check);
      $this->userID=$row['uid'];
      $this->homeID=$row['hid'];
      $this->roomID=$row['rid'];
      $this->hwID=$row['id'];
      $this->hwName=$row['name'];
      $this->hwSeries=$row['series'];
      $this->hwIP=$row['ip_value'];
      $this->error=false;
      $this->errorMessage="null";
    }
    else{
      $this->error=true;
      $this->errorMessage="Hardware does not exists.";
    }
  }
}
function getHardwareDataUsingID($con,$userID,$hwID){
  $hw = new Hardware;
  $sql="SELECT * FROM hardware where id='$hwID' and uid='$userID'";
  $hw->getData($con,$sql);
  return $hw;
}
function getHardwareDataUsingName($con,$userID,$hwName,$roomName,$homeName){
  $hw = new Hardware;
  $r=getRoomDataUsingName($con,$userID,$roomName,$homeName);
  if($r->error) return $r;
  $homeID=$r->homeID;
  $roomID=$r->roomID;
  $sql="SELECT * FROM hardware where name='$hwName' and hid='$homeID' and rid='$roomID' and uid='$userID'";
  $hw->getData($con,$sql);
  return $hw;
}
?>
