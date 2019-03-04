<?php
require_once("config.php");
require_once("user_data.php");
require_once("home_data.php");
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
    $r=getRoomDataUsingNameIDs($con,$userID,$roomName,$homeID);
    if($r->error) return $r;
    $gotData->user->room->id=$r->id;
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="Try again!";
  return $gotData;
}
function deleteRoom($gotData){
  $gotData=getUserID($gotData);
  if($gotData->error) return $gotData;
  $id=$gotData->user->room->id;
  $sql="DELETE FROM room_device where room_id='$id'";
  $result=mysqli_query($gotData->con,$sql);
  if($result)
  {
    $sql="DELETE FROM hardware where rid='$id'";
    $result=mysqli_query($gotData->con,$sql);
    if($result)
    {
      $sql="DELETE FROM room where id='$id'";
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
  $gotData=getUserID($gotData);
  if($gotData->error) return $gotData;
  $userID=$gotData->user->userID;
  $homeName=$gotData->user->homeName;
  $homeID=$gotData->user->homeID;
  $sql="SELECT * FROM room where uid='$userID' and hid='$homeID'";
  $check=mysqli_query($gotData->con,$sql);
  $gotData->total=mysqli_num_rows($check);
  if($check && ($gotData->total>=0))
  {
    $gotData->user->allRoom=(object) null;
    $email=$gotData->user->email;
    if($gotData->total==0){
      $noFound='<div class="row" align="center" style="margin-top: 80px;">
              	<div id="no_found"><img src="images/not-found.png" width="100px" alt="no found" /></div>
              	<br/>
              	<div style="color:gray;"><h4>No Rooms</h4></div>
              	</div>';
      $gotData->user->allRoom=$noFound;
      return $gotData;
    }
    $allRoom="<div class='grid-container' style='cursor:pointer;'>";
    while($row=mysqli_fetch_array($check)){
      $roomName=$row['roomname'];
      $id=$row['id'];

      $editFunc = "editRoom($id,'".$roomName."')";
      $deleteButton="<button class='btn btn-default' onclick='deleteRoom($id)' ><span class='glyphicon glyphicon-trash'></span> Delete</button>";
      $editButton="<button class='btn btn-default' onclick=$editFunc><span class='glyphicon glyphicon-pencil'></span> Edit</button>";
      $allRoom.='<div class="grid-item card"><div class="row"><a href="#!customer/home/'.$homeName.'/'.$roomName.'">'.$roomName.'</a></div><div class="row"><div class="col-md-3"></div><div class="col-md-3">'.$editButton.'</div><div class="col-md-3">'.$deleteButton.'</div><div class="col-md-3"></div></div></div>';
    }
    $allRoom.='</div>';
    $gotData->user->allRoom=$allRoom;
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="Try again!";
  return $gotData;
}
function getRoomList($gotData){
  $gotData=getUserID($gotData);
  if($gotData->error) return $gotData;
  $userID=$gotData->user->userID;
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
  if($action=="0" && isset($_REQUEST['email']) && isset($_REQUEST['homeName']) && isset($_REQUEST['userID']))
  {
    $homeName=ucfirst($_REQUEST['homeName']);
    $email=$_REQUEST['email'];
    $userID=$_REQUEST['userID'];
    $gotData = (object) null;
    $gotData->user=(object) null;
    $gotData->error=false;
    $gotData->errorMessage="null";
    $gotData->con=$con;
    $gotData->user->email=$email;
    $gotData->user->homeName=ucfirst($homeName);
    $h=getHomeDataUsingName($gotData->con,$userID,$homeName);
    if($h->error){
      echo json_encode($h);
    }else{
      $homeID=$h->homeID;
      $gotData->user->homeID=$homeID;
      $gotData=getRoomData($gotData);
      echo json_encode($gotData);
    }
  }
  else if($action=="1" && isset($_REQUEST['email']) && isset($_REQUEST['homeName']) && isset($_REQUEST['roomName']) && isset($_REQUEST['userID']))
    {
      $email=$_REQUEST['email'];
      $roomName=ucfirst($_REQUEST['roomName']);
      $homeName=ucfirst($_REQUEST['homeName']);
      $userID=$_REQUEST['userID'];
      $gotData = (object) null;
      $gotData->user=(object) null;
      $gotData->user->room=(object) null;
      $gotData->error=false;
      $gotData->errorMessage="null";
      $gotData->con=$con;
      $gotData->user->room->homeName=ucfirst($homeName);
      $gotData->user->email=$email;
      $gotData->user->room->email=$email;
      $gotData->user->room->roomName=ucfirst($roomName);
      $gotData->user->room->action=$action;
      $h=getHomeDataUsingName($gotData->con,$userID,$homeName);
      if($h->error){
        echo json_encode($h);
      }else{
        $homeID=$h->homeID;
        $gotData->user->room->homeID=$homeID;
        $gotData=createRoom($gotData);
        echo json_encode($gotData);
      }
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
      echo json_encode($gotData);
    }else if($action=="3" && isset($_REQUEST['email']) && isset($_REQUEST['id']) && isset($_REQUEST['roomName'])){
      $email=$_REQUEST['email'];
      $roomName=ucfirst($_REQUEST['roomName']);
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
      echo json_encode($gotData);
    }else if($action=="4" && isset($_REQUEST['email']) && isset($_REQUEST['homeName']) && isset($_REQUEST['userID']))
    {
      $homeName=ucfirst($_REQUEST['homeName']);
      $email=$_REQUEST['email'];
      $userID=$_REQUEST['userID'];
      $gotData = (object) null;
      $gotData->user=(object) null;
      $gotData->error=false;
      $gotData->errorMessage="null";
      $gotData->con=$con;
      $gotData->user->email=$email;
      $gotData->user->homeName=ucfirst($homeName);
      $h=getHomeDataUsingName($gotData->con,$userID,$homeName);
      if($h->error){
        echo json_encode($h);
      }else{
        $homeID=$h->homeID;
        $gotData->user->homeID=$homeID;
        $gotData=getRoomList($gotData);
        echo json_encode($gotData);
      }
    }else{
      $gotData = (object) null;
      $gotData->error=true;
      $gotData->errorMessage="Please, try again after few minutes!";
      echo json_encode($gotData);
    }
}else{
  $gotData = (object) null;
  $gotData->error=true;
  $gotData->errorMessage="Please, try again after few minutes!";
  echo json_encode($gotData);
  exit();
}
?>
