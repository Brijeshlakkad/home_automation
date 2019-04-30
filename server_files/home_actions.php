<?php
require_once("config.php");
require_once('home_data.php');
require_once('user_data.php');
function createHome($gotData){  // creates home
  $u=getUserDataUsingEmail($gotData->con,$gotData->user->email);
  if($u->error) return $u;
  $userID=$u->id;
  $homeName=$gotData->user->home->homeName;
  $sql="INSERT INTO home(uid,homename) VALUES('$userID','$homeName')";
  $result=mysqli_query($gotData->con,$sql);
  if($result)
  {
    $gotData->error=false;
    $h=getHomeDataUsingName($gotData->con,$userID,$homeName);
    if($h->error) return $h;
    $gotData->user->home->id=$h->homeID;
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="Try again!";
  return $gotData;
}
function deleteHome($gotData){ // removes home
  $id=$gotData->user->home->id;
  $sql="DELETE `home`, `room`, `hardware`,`room_device`, `devicevalue`,`schedule_device` FROM home LEFT JOIN room ON room.hid=home.id LEFT JOIN hardware ON hardware.rid=room.id LEFT JOIN room_device ON room_device.hw_id=hardware.id LEFT JOIN schedule_device ON schedule_device.device_id=room_device.id LEFT JOIN devicevalue ON devicevalue.did=room_device.id WHERE home.id='$id'";
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
function renameHome($gotData){ // modifies home
  $homeName=$gotData->user->home->homeName;
  $id=$gotData->user->home->id;
  $sql="UPDATE home SET homename='$homeName' where id='$id'";
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
function getHomeData($gotData){ // gets home list in json
  $u=getUserDataUsingEmail($gotData->con,$gotData->user->email);
  if($u->error) return $u;
  $userID=$u->id;
  $sql="SELECT * FROM home where uid='$userID'";
  $check=mysqli_query($gotData->con,$sql);
  $gotData->total=mysqli_num_rows($check);
  if($check && ($gotData->total>=0))
  {
    $i=0;
    $email=$gotData->user->email;
    while($row=mysqli_fetch_array($check)){
      $gotData->user->home[$i] = (object) null;
      $homeName=$row['homename'];
      $id=$row['id'];
      $gotData->user->home[$i]->homeName=$homeName;
      $gotData->user->home[$i]->id=$id;
      $gotData->user->home[$i]->email=$email;
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
  if($action=="0" && isset($_REQUEST['email'])){
    $email=$_REQUEST['email'];
    $gotData = (object) null;
    $gotData->user=(object) null;
    $gotData->error=false;
    $gotData->errorMessage="null";
    $gotData->con=$con;
    $gotData->user->email=$email;
    $gotData=getHomeData($gotData);
    $gotData->con=(object) null;
    echo json_encode($gotData);
    exit();
  }else if($action=="1" && isset($_REQUEST['email']) && isset($_REQUEST['homeName'])){
    $email=$_REQUEST['email'];
    $homeName=$_REQUEST['homeName'];
    $gotData = (object) null;
    $gotData->user=(object) null;
    $gotData->user->home=(object) null;
    $gotData->error=false;
    $gotData->errorMessage="null";
    $gotData->con=$con;
    $gotData->user->email=$email;
    $gotData->user->home->homeName=ucfirst($homeName);
    $gotData->user->home->email=$email;
    $gotData->user->home->action=$action;
    $gotData=createHome($gotData);
    $gotData->con=(object) null;
    echo json_encode($gotData);
    exit();
  }else if($action=="2" && isset($_REQUEST['email']) && isset($_REQUEST['id']))
  {
    $email=$_REQUEST['email'];
    $id=$_REQUEST['id'];
    $gotData = (object) null;
    $gotData->user=(object) null;
    $gotData->user->home=(object) null;
    $gotData->error=false;
    $gotData->errorMessage="null";
    $gotData->con=$con;
    $gotData->user->email=$email;
    $gotData->user->home->id=$id;
    $gotData->user->home->email=$email;
    $gotData->user->home->action=$action;
    $gotData=deleteHome($gotData);
    $gotData->con=(object) null;
    echo json_encode($gotData);
    exit();
  }else if($action=="3" && isset($_REQUEST['email']) && isset($_REQUEST['id']) && isset($_REQUEST['homeName']))
  {
    $email=$_REQUEST['email'];
    $id=$_REQUEST['id'];
    $homeName=$_REQUEST['homeName'];
    $gotData = (object) null;
    $gotData->user=(object) null;
    $gotData->user->home=(object) null;
    $gotData->error=false;
    $gotData->errorMessage="null";
    $gotData->user->home->id=$id;
    $gotData->con=$con;
    $gotData->user->email=$email;
    $gotData->user->home->homeName=ucfirst($homeName);
    $gotData->user->home->email=$email;
    $gotData->user->home->action=$action;
    $gotData=renameHome($gotData);
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
