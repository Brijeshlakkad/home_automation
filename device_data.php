<?php
require_once("hardware_data.php");
require_once("room_data.php");
class Device{
  var $userID,$homeID,$roomID,$hwID,$dvID,$dvName,$dvPort,$dvImg,$dvStatus,$error,$errorMessage;
  function getData($con,$sql){
    $check=mysqli_query($con,$sql);
    if($check && (mysqli_num_rows($check)==1))
    {
      $row=mysqli_fetch_array($check);
      $this->userID=$row['uid'];
      $this->homeID=$row['hid'];
      $this->roomID=$row['room_id'];
      $this->hwID=$row['hw_id'];
      $this->dvID=$row['id'];
      $this->dvName=$row['device_name'];
      $this->dvPort=$row['port'];
      $this->dvImg=$row['device_image'];
      $this->dvStatus=$row['status'];
      $this->error=false;
      $this->errorMessage="null";
    }
    else{
      $this->error=true;
      $this->errorMessage="Device does not exists.";
    }
  }
}
function getDeviceDataUsingID($con,$userID,$dvID){
  $hw = new Device;
  $sql="SELECT * FROM room_device where id='$dvID' and uid='$userID'";
  $hw->getData($con,$sql);
  return $hw;
}
function getDeviceDataUsingName($con,$userID,$dvName,$hwName,$roomName,$homeName){
  $dv = new Device;
  $hw=getHardwareDataUsingName($con,$userID,$hwName,$roomName,$homeName);
  if($hw->error) return $hw;
  $homeID=$hw->homeID;
  $roomID=$hw->roomID;
  $hwID=$hw->hwID;
  $sql="SELECT * FROM room_device where device_name='$dvName' and hid='$homeID' and room_id='$roomID' and hw_id='$hwID' and uid='$userID'";
  $dv->getData($con,$sql);
  return $dv;
}
function getDeviceDataUsingNameIDs($con,$userID,$dvName,$hwID,$roomID,$homeID){
  $dv = new Device;
  $sql="SELECT * FROM room_device where device_name='$dvName' and hid='$homeID' and room_id='$roomID' and hw_id='$hwID' and uid='$userID'";
  $dv->getData($con,$sql);
  return $dv;
}
function getDeviceDataUsingRoomID($con,$deviceName,$roomID,$userID){
  $dv = new Device;
  $sql="SELECT * FROM room_device WHERE device_name='$deviceName' AND room_id='$roomID' AND uid='$userID'";
  $dv->getData($con,$sql);
  return $dv;
}
?>
