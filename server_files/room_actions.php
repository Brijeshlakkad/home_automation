<?php
require_once("config.php");
require_once('user_data.php');
require_once('home_data.php');
require_once('room_data.php');
function checkHomeID($gotData)
{
  $homeID=$gotData->user->room->homeID;
  $sql="SELECT * FROM home where id='$homeID'";
  $check=mysqli_query($gotData->con,$sql);
  if($check && (mysqli_num_rows($check)==1))
  {
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="You do not have home in OUR app.";
  return $gotData;
}
function roomExistsInAnotherHome($gotData,$ignoreID){
    $userID=$gotData->user->userID;
    $roomName=$gotData->user->room->roomName;
    $sql="SELECT * FROM room WHERE roomname='$roomName' AND uid='$userID' AND id!='$ignoreID'";
    $result=mysqli_query($gotData->con,$sql);
    if($result){
        if(mysqli_num_rows($result)!=0){
            $row=mysqli_fetch_array($result);
            $homeID=$row['hid'];
            $home=getHomeDataUsingID($gotData->con,$userID,$homeID);
            $gotData->error=true;
            $gotData->errorMessage="$roomName exists in $home->homeName Home";
        }
        return $gotData;
    }
    $gotData->error=true;
    $gotData->errorMessage="Try again!";
    return $gotData;
}
function createRoom($gotData){
  $u=getUserDataUsingEmail($gotData->con,$gotData->user->email);
  if($u->error) return $u;
  $userID=$u->id;
  $gotData->user->userID=$userID;
  $gotData=roomExistsInAnotherHome($gotData,null);
  if($gotData->error) return $gotData;
  $roomName=$gotData->user->room->roomName;
  $homeID=$gotData->user->room->homeID;
  $sql="INSERT INTO room(uid,hid,roomname) VALUES('$userID','$homeID','$roomName')";
  $result=mysqli_query($gotData->con,$sql);
  if($result)
  {
    $gotData->error=false;
    $r=getRoomDataUsingNameIDs($gotData->con,$userID,$roomName,$homeID);
    if($r->error) return $r;
    $gotData->user->room->id=$r->roomID;
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="Try again!";
  return $gotData;
}
function deleteRoom($gotData){
  $id=$gotData->user->room->id;
  $sql="DELETE `room`, `hardware`,`room_device`,`schedule_device`, `devicevalue` FROM room LEFT JOIN hardware ON hardware.rid=room.id LEFT JOIN room_device ON room_device.hw_id=hardware.id LEFT JOIN schedule_device ON schedule_device.device_id=room_device.id LEFT JOIN devicevalue ON devicevalue.did=room_device.id WHERE room.id='$id'";
  $result=mysqli_query($gotData->con,$sql);
  if($result)
  {
    $gotData->error=false;
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="Try again!";
  return $gotData;
}
function renameRoom($gotData){
  $u=getUserDataUsingEmail($gotData->con,$gotData->user->email);
  if($u->error) return $u;
  $userID=$u->id;
  $id=$gotData->user->room->id;
  $gotData->user->userID=$userID;
  $gotData=roomExistsInAnotherHome($gotData,$id);
  if($gotData->error) return $gotData;
  $roomName=$gotData->user->room->roomName;
  $sql="UPDATE room SET roomname='$roomName' where id='$id'";
  $result=mysqli_query($gotData->con,$sql);
  if($result && (mysqli_affected_rows($gotData->con)==1))
  {
    $gotData->error=false;
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="Try again!";
  return $gotData;
}
function getRoomData($gotData){
  $u=getUserDataUsingEmail($gotData->con,$gotData->user->email);
  if($u->error) return $u;
  $userID=$u->id;
  $homeID=$gotData->user->homeID;
  $sql="SELECT * FROM room where uid='$userID' and hid='$homeID'";
  $check=mysqli_query($gotData->con,$sql);
  $gotData->total=mysqli_num_rows($check);
  if($check && ($gotData->total>=0))
  {
    $i=0;
    $email=$gotData->user->email;
    while($row=mysqli_fetch_array($check)){
      $gotData->user->room[$i] = (object) null;
      $roomName=$row['roomname'];
      $id=$row['id'];
      $gotData->user->room[$i]->roomName=$roomName;
      $gotData->user->room[$i]->homeID=$homeID;
      $gotData->user->room[$i]->id=$id;
      $gotData->user->room[$i]->email=$email;
      $i++;
    }
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="Try again!";
  return $gotData;
}
if(isset($_REQUEST['action'])){
  $action=$_REQUEST['action'];
  if($action=="0" && isset($_REQUEST['email']) && isset($_REQUEST['homeID']))
  {
    $homeID=$_REQUEST['homeID'];
    $email=$_REQUEST['email'];
    $gotData = (object) null;
    $gotData->user=(object) null;
    $gotData->error=false;
    $gotData->errorMessage="null";
    $gotData->con=$con;
    $gotData->user->email=$email;
    $gotData->user->homeID=$homeID;
    $gotData=getRoomData($gotData);
    $gotData->con=(object) null;
    echo json_encode($gotData);
    exit();
  }
  else if($action=="1" && isset($_REQUEST['email']) && isset($_REQUEST['homeID']) && isset($_REQUEST['roomName']))
    {
      $email=$_REQUEST['email'];
      $roomName=$_REQUEST['roomName'];
      $homeID=$_REQUEST['homeID'];
      $gotData = (object) null;
      $gotData->user=(object) null;
      $gotData->user->room=(object) null;
      $gotData->error=false;
      $gotData->errorMessage="null";
      $gotData->con=$con;
      $gotData->user->room->homeID=$homeID;
      $gotData->user->email=$email;
      $gotData->user->room->email=$email;
      $gotData->user->room->roomName=ucfirst($roomName);
      $gotData->user->room->action=$action;
      $gotData=createRoom($gotData);
      $gotData->con=(object) null;
      echo json_encode($gotData);
      exit();
    }else if($action=="2" && isset($_REQUEST['email']) && isset($_REQUEST['id'])){
      $email=$_REQUEST['email'];
      $id=$_REQUEST['id'];
      $gotData = (object) null;
      $gotData->user=(object) null;
      $gotData->user->room=(object) null;
      $gotData->error=false;
      $gotData->errorMessage="null";
      $gotData->con=$con;
      $gotData->user->room->id=$id;
      $gotData->user->email=$email;
      $gotData->user->room->email=$email;
      $gotData->user->room->action=$action;
      $gotData=deleteRoom($gotData);
      $gotData->con=(object) null;
      echo json_encode($gotData);
      exit();
    }else if($action=="3" && isset($_REQUEST['email']) && isset($_REQUEST['id']) && isset($_REQUEST['roomName'])){
      $email=$_REQUEST['email'];
      $roomName=$_REQUEST['roomName'];
      $id=$_REQUEST['id'];
      $gotData = (object) null;
      $gotData->user=(object) null;
      $gotData->user->room=(object) null;
      $gotData->error=false;
      $gotData->errorMessage="null";
      $gotData->con=$con;
      $gotData->user->room->id=$id;
      $gotData->user->email=$email;
      $gotData->user->room->email=$email;
      $gotData->user->room->roomName=ucfirst($roomName);
      $gotData->user->room->action=$action;
      $gotData=renameRoom($gotData);
      $gotData->con=(object) null;
      echo json_encode($gotData);
      exit();
    }else{
      $gotData = (object) null;
      $gotData->error=true;
      $gotData->errorMessage="Try again!";
      $gotData->con=(object) null;
      echo json_encode($gotData);
      exit();
    }
}else{
  $gotData = (object) null;
  $gotData->error=true;
  $gotData->errorMessage="Try again!";
  $gotData->con=(object) null;
  echo json_encode($gotData);
  exit();
}
?>
