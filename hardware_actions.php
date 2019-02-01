<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once("config.php");
require_once("home_data.php");
require_once("room_data.php");
function getUserID($gotData)
{
  $email=$gotData->user->email;
  $sql="SELECT * FROM user where email='$email'";
  $check=mysqli_query($gotData->con,$sql);
  if($check && (mysqli_num_rows($check)==1))
  {
    $row=mysqli_fetch_array($check);
    $gotData->user->userID=$row['id'];
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="You do not have account in OUR app. Please registor yourself at OUR app";
  return $gotData;
}
function checkHomeID($gotData)
{
  $homeID=$gotData->user->homeID;
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
function createHardware($gotData){
  $gotData=getUserID($gotData);
  if($gotData->error) return $gotData;
  $userID=$gotData->user->userID;
  $homeID=$gotData->user->hw->homeID;
  $roomID=$gotData->user->hw->roomID;
  $hwName=$gotData->user->hw->hwName;
  $hwSeries=$gotData->user->hw->hwSeries;
  $hwIP=$gotData->user->hw->hwIP;
  $sql="INSERT INTO hardware(uid,hid,rid,name,series,ip_value) VALUES('$userID','$homeID','$roomID','$hwName','$hwSeries','$hwIP')";
  $result=mysqli_query($gotData->con,$sql);
  if($result)
  {
    $gotData->error=false;
    $gotData->user->hw->id=mysqli_insert_id($gotData->con);
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="Try again!";
  return $gotData;
}
function deleteHardware($gotData){
  $gotData=getUserID($gotData);
  if($gotData->error) return $gotData;
  $id=$gotData->user->hw->id;
  $sql="DELETE FROM room_device where hw_id='$id'";
  $result=mysqli_query($gotData->con,$sql);
  if($result)
  {
    $sql="DELETE FROM hardware where id='$id'";
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
  $gotData->error=true;
  $gotData->errorMessage="Try again!";
  return $gotData;
}
function renameHardware($gotData){
  $gotData=getUserID($gotData);
  if($gotData->error) return $gotData;
  $hwName=$gotData->user->hw->hwName;
  $hwSeries=$gotData->user->hw->hwSeries;
  $hwIP=$gotData->user->hw->hwIP;
  $id=$gotData->user->hw->id;
  $sql="UPDATE hardware SET name='$hwName', series='$hwSeries', ip_value='$hwIP' where id='$id'";
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
function getHardwareData($gotData){
  $gotData=getUserID($gotData);
  if($gotData->error) return $gotData;
  $userID=$gotData->user->userID;
  $homeID=$gotData->user->homeID;
  $roomID=$gotData->user->roomID;
  $homeName=$gotData->user->homeName;
  $roomName=$gotData->user->roomName;
  $sql="SELECT * FROM hardware where uid='$userID' and hid='$homeID' and rid='$roomID'";
  $check=mysqli_query($gotData->con,$sql);
  $gotData->total=mysqli_num_rows($check);
  if($check && ($gotData->total>=0))
  {
    $gotData->user->allHardware=(object) null;
    $email=$gotData->user->email;
    if($gotData->total==0){
      $noFound='<div class="row" align="center" style="margin-top: 80px;">
              	<div id="no_found"><img src="images/not-found.png" width="100px" alt="no found" /></div>
              	<br/>
              	<div style="color:gray;"><h4>No Hardwares</h4></div>
              	</div>';
      $gotData->user->allHardware=$noFound;
      return $gotData;
    }
    $allHardware="<div class='grid-container' style='cursor:pointer;'>";
    while($row=mysqli_fetch_array($check)){
      $hwName=$row['name'];
      $hwSeries=$row['series'];
      $hwIP=$row['ip_value'];
      $id=$row['id'];
      $editFunc = "editHardware($id,'".$hwName."','".$hwSeries."','".$hwIP."')";
      $deleteButton="<button class='btn btn-default' onclick='deleteHardware($id)' ><span class='glyphicon glyphicon-trash'></span> Delete</button>";
      $editButton="<button class='btn btn-default' onclick=$editFunc><span class='glyphicon glyphicon-pencil'></span> Edit</button>";
      $allHardware.='<div class="grid-item card"><div class="row"><a href="#!customer/home/'.$homeName.'/'.$roomName.'/'.$hwName.'">'.$hwName.'</a></div><div class="row"><div class="col-md-3"></div><div class="col-md-3">'.$editButton.'</div><div class="col-md-3">'.$deleteButton.'</div><div class="col-md-3"></div></div></div>';
    }
    $allHardware.='</div>';
    $gotData->user->allHardware=$allHardware;
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="Try again!";
  return $gotData;
}
function getHardwareList($gotData){
  $gotData=getUserID($gotData);
  if($gotData->error) return $gotData;
  $userID=$gotData->user->userID;
  $homeID=$gotData->user->homeID;
  $roomID=$gotData->user->roomID;
  $sql="SELECT * FROM hardware where uid='$userID' and hid='$homeID' and rid='$roomID'";
  $check=mysqli_query($gotData->con,$sql);
  $gotData->total=mysqli_num_rows($check);
  if($check && ($gotData->total>=0))
  {
    $i=0;
    $email=$gotData->user->email;
    while($row=mysqli_fetch_array($check)){
      $gotData->user->hw[$i] = (object) null;
      $hwName=$row['name'];
      $hwSeries=$row['series'];
      $hwIP=$row['ip_value'];
      $id=$row['id'];
      $gotData->user->hw[$i]=(object) null;
      $gotData->user->hw[$i]->hwName=$hwName;
      $gotData->user->hw[$i]->hwSeries=$hwSeries;
      $gotData->user->hw[$i]->hwIP=$hwIP;
      $gotData->user->hw[$i]->homeID=$homeID;
      $gotData->user->hw[$i]->roomID=$roomID;
      $gotData->user->hw[$i]->id=$id;
      $gotData->user->hw[$i]->email=$email;
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
  if($action=="0" && isset($_REQUEST['email']) && isset($_REQUEST['homeName']) && isset($_REQUEST['roomName']) && isset($_REQUEST['userID']))
  {
    $email=$_REQUEST['email'];
    $homeName=$_REQUEST['homeName'];
    $roomName=$_REQUEST['roomName'];
    $userID=$_REQUEST['userID'];
    $gotData = (object) null;
    $gotData->error=false;
    $gotData->errorMessage="null";
    $gotData->user=(object) null;
    $gotData->con=$con;
    $gotData->user->homeName=$homeName;
    $gotData->user->email=$email;
    $gotData->user->roomName=$roomName;
    $gotData->user->action=$action;
    $r=getRoomDataUsingName($gotData->con,$userID,$roomName,$homeName);
    if($r->error){
      echo json_encode($r);
    }else{
      $roomID=$r->roomID;
      $gotData->user->roomID=$roomID;
      $homeID=$r->homeID;
      $gotData->user->homeID=$homeID;
      $gotData=getHardwareData($gotData);
      echo json_encode($gotData);
    }
  }else if($action=="1" && isset($_REQUEST['email']) && isset($_REQUEST['homeName']) && isset($_REQUEST['roomName']) && isset($_REQUEST['hwName']) && isset($_REQUEST['hwSeries']) && isset($_REQUEST['hwIP']) && isset($_REQUEST['userID'])){
    $email=$_REQUEST['email'];
    $homeName=$_REQUEST['homeName'];
    $roomName=$_REQUEST['roomName'];
    $hwName=$_REQUEST['hwName'];
    $hwSeries=$_REQUEST['hwSeries'];
    $hwIP=$_REQUEST['hwIP'];
    $userID=$_REQUEST['userID'];
    $gotData = (object) null;
    $gotData->user=(object) null;
    $gotData->user->hw=(object) null;
    $gotData->error=false;
    $gotData->errorMessage="null";
    $gotData->con=$con;
    $gotData->user->email=$email;
    $gotData->user->hw->email=$email;
    $gotData->user->hw->homeName=$homeName;
    $gotData->user->hw->roomName=$roomName;
    $gotData->user->hw->hwName=$hwName;
    $gotData->user->hw->hwSeries=$hwSeries;
    $gotData->user->hw->hwIP=$hwIP;
    $r=getRoomDataUsingName($gotData->con,$userID,$roomName,$homeName);
    if($r->error){
      echo json_encode($r);
    }else{
      $roomID=$r->roomID;
      $gotData->user->hw->roomID=$roomID;
      $homeID=$r->homeID;
      $gotData->user->hw->homeID=$homeID;
      $gotData=createHardware($gotData);
      echo json_encode($gotData);
    }
  }
  else if($action=="2" && isset($_REQUEST['email']) && isset($_REQUEST['id'])){
    $email=$_REQUEST['email'];
    $id=$_REQUEST['id'];
    $gotData = (object) null;
    $gotData->user=(object) null;
    $gotData->user->hw=(object) null;
    $gotData->error=false;
    $gotData->errorMessage="null";
    $gotData->con=$con;
    $gotData->user->email=$email;
    $gotData->user->hw->id=$id;
    $gotData->user->hw->email=$email;
    $gotData=deleteHardware($gotData);
    echo json_encode($gotData);
  }
  else if($action=="3" && isset($_REQUEST['email']) && isset($_REQUEST['hwName']) && isset($_REQUEST['hwSeries']) && isset($_REQUEST['hwIP']) && isset($_REQUEST['id'])){
    $email=$_REQUEST['email'];
    $hwName=$_REQUEST['hwName'];
    $hwSeries=$_REQUEST['hwSeries'];
    $hwIP=$_REQUEST['hwIP'];
    $id=$_REQUEST['id'];
    $gotData = (object) null;
    $gotData->user=(object) null;
    $gotData->user->hw=(object) null;
    $gotData->error=false;
    $gotData->errorMessage="null";
    $gotData->con=$con;
    $gotData->user->email=$email;
    $gotData->user->hw->email=$email;
    $gotData->user->hw->id=$id;
    $gotData->user->hw->email=$email;
    $gotData->user->hw->hwName=$hwName;
    $gotData->user->hw->hwSeries=$hwSeries;
    $gotData->user->hw->hwIP=$hwIP;
    $gotData=renameHardware($gotData);
    echo json_encode($gotData);
  }else if($action=="4" && isset($_REQUEST['email']) && isset($_REQUEST['homeName']) && isset($_REQUEST['roomName']) && isset($_REQUEST['userID']))
  {
    $email=$_REQUEST['email'];
    $homeName=$_REQUEST['homeName'];
    $roomName=$_REQUEST['roomName'];
    $userID=$_REQUEST['userID'];
    $gotData = (object) null;
    $gotData->error=false;
    $gotData->errorMessage="null";
    $gotData->user=(object) null;
    $gotData->con=$con;
    $gotData->user->homeName=$homeName;
    $gotData->user->email=$email;
    $gotData->user->roomName=$roomName;
    $gotData->user->action=$action;
    $r=getRoomDataUsingName($gotData->con,$userID,$roomName,$homeName);
    if($r->error){
      echo json_encode($r);
    }else{
      $roomID=$r->roomID;
      $gotData->user->roomID=$roomID;
      $homeID=$r->homeID;
      $gotData->user->homeID=$homeID;
      $gotData=getHardwareList($gotData);
      echo json_encode($gotData);
    }
  }else{
    $gotData = (object) null;
    $gotData->error=true;
    $gotData->errorMessage="Please, try again after few minutes!";
    echo json_encode($gotData);
  }
}
?>
