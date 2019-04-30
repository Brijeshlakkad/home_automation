<?php
require_once("home_data.php");
class Room{ // room data using sql
  var $roomID,$userID,$homeID,$roomName,$error,$errorMessage;
  function getData($con,$sql){
    $check=mysqli_query($con,$sql);
    if($check && (mysqli_num_rows($check)==1))
    {
      $row=mysqli_fetch_array($check);
      $this->roomID=$row['id'];
      $this->userID=$row['uid'];
      $this->homeID=$row['hid'];
      $this->roomName=$row['roomname'];
      $this->error=false;
      $this->errorMessage="null";
    }
    else{
      $this->error=true;
      $this->errorMessage="Room does not exists.";
    }
  }
}
function getRoomDataUsingID($con,$userID,$roomID){
  $r = new Room;
  $sql="SELECT * FROM room where id='$roomID' and uid='$userID'";
  $r->getData($con,$sql);
  return $r;
}
function getRoomDataUsingName($con,$userID,$roomName,$homeName){
  $r = new Room;
  $h=getHomeDataUsingName($con,$userID,$homeName);
  if($h->error) return $h;
  $homeID=$h->homeID;
  $sql="SELECT * FROM room where roomname='$roomName' and hid='$homeID' and uid='$userID'";
  $r->getData($con,$sql);
  return $r;
}
function getRoomDataUsingNameIDs($con,$userID,$roomName,$homeID){
  $r = new Room;
  $sql="SELECT * FROM room where roomname='$roomName' and hid='$homeID' and uid='$userID'";
  $r->getData($con,$sql);
  return $r;
}
?>
