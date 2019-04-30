<?php
require_once("hardware_data.php");
require_once("room_data.php");
class Device{ // to get device class
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
function getDeviceDataUsingID($con,$userID,$dvID){ // using device id and user id
  $hw = new Device;
  $sql="SELECT * FROM room_device where id='$dvID' and uid='$userID'";
  $hw->getData($con,$sql);
  return $hw;
}
function getDeviceDataUsingName($con,$userID,$dvName,$hwName,$roomName,$homeName){ // using all name and user id
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
function getDeviceDataUsingNameIDs($con,$userID,$dvName,$hwID,$roomID,$homeID){ // using home, room and hardware id and device name
  $dv = new Device;
  $sql="SELECT * FROM room_device where device_name='$dvName' and hid='$homeID' and room_id='$roomID' and hw_id='$hwID' and uid='$userID'";
  $dv->getData($con,$sql);
  return $dv;
}
function getHardwareListUsingRoomID($con,$roomID,$userID){  // using room and user id
  $hwSeriesList=[];
  $sql="SELECT * FROM hardware WHERE rid='$roomID' AND uid='$userID'";
  $result=mysqli_query($con,$sql);
  if($result){
    $num=mysqli_num_rows($result);
    $i=0;
    while($row=mysqli_fetch_array($result)){
      $hwSeriesList[$i]=$row['series'];
      $i++;
    }
  }
  return $hwSeriesList;
}
function hasOwnerShipUsingRoomID($con,$deviceName,$roomID,$userID){ // using device name and room and user id
  $sql="SELECT * FROM room_device WHERE room_id='$roomID' AND uid='$userID' AND device_name='$deviceName'";
  $result=mysqli_query($con,$sql);
  if($result){
    if(mysqli_num_rows($result)==1){
      return true;
    }
  }
  return false;
}
function getDeviceDataUsingRoomID($con,$deviceName,$roomID,$userID){ // device data using user id, room id and device name
  if(hasOwnerShipUsingRoomID($con,$deviceName,$roomID,$userID)){
    $dv = new Device;
    $sql="SELECT * FROM room_device WHERE room_id='$roomID' AND uid='$userID' AND device_name='$deviceName'";
    $dv->getData($con,$sql);
    return $dv;
  }else{
    $dv=(object) null;
    $dv->error=false;
    $hwSeriesList=getHardwareListUsingRoomID($con,$roomID,$userID);
    for($i=0;$i<count($hwSeriesList);$i++){
      $hwSeries=$hwSeriesList[$i];
      $sql="SELECT room_device.id as `dvID`, room_device.device_name as `dvName`, room_device.port as `dvPort`, room_device.device_image as `dvImg`,
            room_device.hid as `homeID`, room_device.room_id as `roomID`, room_device.hw_id as `hwID`, room_device.uid as `userID`, room_device.status as `status`
            FROM room_device
            INNER JOIN hardware ON hardware.id=room_device.hw_id
            INNER JOIN allowed_user ON allowed_user.serial_no=hardware.series
            INNER JOIN product_serial ON product_serial.serial_no=allowed_user.serial_no
            INNER JOIN sold_product ON sold_product.serial_id=product_serial.id
            WHERE allowed_user.member_id='$userID' AND allowed_user.serial_no='$hwSeries' AND room_device.device_name='$deviceName'";
      $result=mysqli_query($con,$sql);
      if($result){
        $num=mysqli_num_rows($result);
        if($num==1){
          $row=mysqli_fetch_array($result);
          $dv=(object) null;
          $dv->error=false;
          $dv->userID=$row['userID'];
          $dv->homeID=$row['homeID'];
          $dv->roomID=$row['roomID'];
          $dv->hwID=$row['hwID'];
          $dv->dvID=$row['dvID'];
          $dv->dvName=$row['dvName'];
          $dv->dvPort=$row['dvPort'];
          $dv->dvImg=$row['dvImg'];
          $dv->dvStatus=$row['status'];
          $dv->error=false;
          $dv->errorMessage="null";
          return $dv;
        }
      }
    }
    $dv->error=true;
    $dv->errorMessage="Device does not exists.";
    return $dv;
  }
}
?>
