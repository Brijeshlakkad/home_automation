<?php
require_once("config.php");
require_once('user_data.php');
require_once("hardware_data.php");
function createSubscription($gotData){
  $hwID=$gotData->user->hw->id;
  $sql="INSERT INTO amc(serial_no_id) VALUES('$hwID')";
  $result=mysqli_query($gotData->con,$sql);
  if($result)
  {
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="Error in Subscription.";
  return $gotData;
}
function createHardware($gotData){
  $homeID=$gotData->user->hw->homeID;
  $roomID=$gotData->user->hw->roomID;
  $hwName=$gotData->user->hw->hwName;
  $hwSeries=$gotData->user->hw->hwSeries;
  $hwIP=$gotData->user->hw->hwIP;
  $u=getUserDataUsingEmail($gotData->con,$gotData->user->email);
  if($u->error) return $u;
  $userID=$u->id;
  $got=(object) null;
  $got->user=(object) null;
  $got->user->email=$gotData->user->email;
  $got->user->userID=$userID;
  $got->user->hwSeries=$hwSeries;
  $got->con=$gotData->con;
  $got->isModifying=false;
  $got->error=false;
  $got=checkHardwareSeries($got);
  if($got->error) return $got;
  $u=getUserDataUsingEmail($gotData->con,$gotData->user->email);
  if($u->error) return $u;
  $userID=$u->id;
  $sql="INSERT INTO hardware(uid,hid,rid,name,series,ip_value) VALUES('$userID','$homeID','$roomID','$hwName','$hwSeries','$hwIP')";
  $result=mysqli_query($gotData->con,$sql);
  if($result)
  {
    $gotData->error=false;
    $hw=getHardwareDataUsingNameIDs($gotData->con,$userID,$hwName,$roomID,$homeID);
    if($hw->error) return $hw;
    $gotData->user->hw->id=$hw->hwID;
    $gotData=createSubscription($gotData);
    if($gotData->error){
      $got=deleteHardware($gotData);
    }
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="Try again!";
  return $gotData;
}
function deleteHardware($gotData){
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
  $hwName=$gotData->user->hw->hwName;
  $hwSeries=$gotData->user->hw->hwSeries;
  $hwIP=$gotData->user->hw->hwIP;
  $id=$gotData->user->hw->id;
  $u=getUserDataUsingEmail($gotData->con,$gotData->user->email);
  if($u->error) return $u;
  $userID=$u->id;
  $got=(object) null;
  $got->user=(object) null;
  $got->user->email=$gotData->user->email;
  $got->user->userID=$userID;
  $got->user->hwSeries=$hwSeries;
  $got->isModifying=true;
  $got->user->id=$id;
  $got->con=$gotData->con;
  $got->error=false;
  $got=checkHardwareSeries($got);
  if($got->error) return $got;
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
  $u=getUserDataUsingEmail($gotData->con,$gotData->user->email);
  if($u->error) return $u;
  $userID=$u->id;
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
function checkAlreadyAdded($con,$hwSeries,$userID){
    $sql="SELECT * FROM hardware WHERE uid='$userID' AND series='$hwSeries'";
    $result=mysqli_query($con,$sql);
    if($result){
      if(mysqli_num_rows($result)>0){
        return false;
      }
    }
    return true;
}
function checkAllowedUser($con,$hwSeries,$email,$userID){
  $sql="SELECT allowed_user.id as `allowedID`, hardware.id as `hwID` FROM allowed_user INNER JOIN hardware ON hardware.uid=allowed_user.uid WHERE allowed_user.member_id='$userID' AND hardware.series='$hwSeries' AND allowed_user.serial_no='$hwSeries'";
  $result=mysqli_query($con,$sql);
  if($result){
    if(mysqli_num_rows($result)==1){
      return checkAlreadyAdded($con,$hwSeries,$userID);
    }
  }
  return false;
}
function checkHardwareSeries($gotData){
  $hwSeries = $gotData->user->hwSeries;
  $email = $gotData->user->email;
  $userID = $gotData->user->userID;
  $sql="SELECT * FROM product_serial WHERE serial_no='$hwSeries' AND customer_email='$email'";
  $check=mysqli_query($gotData->con,$sql);
  if($check){
    if(mysqli_num_rows($check)==0){
        if(checkAllowedUser($gotData->con,$hwSeries,$email,$userID)){
          return $gotData;
        }else{
          $gotData->error=true;
          $gotData->errorMessage="Hardware series can not be used.";
          return $gotData;
        }
    }
    if($gotData->isModifying){
      $id=$gotData->user->id;
      $sql="SELECT * FROM hardware WHERE series='$hwSeries' AND id!='$id'";
    }else{
        $sql="SELECT * FROM hardware WHERE series='$hwSeries'";
    }
    $result=mysqli_query($gotData->con,$sql);
    if($result){
        if(mysqli_num_rows($result)>0){
            $gotData->error=true;
            $gotData->errorMessage="Hardware series has been already used.";
        }
        return $gotData;
    }
  }
  $gotData->error=true;
  $gotData->errorMessage="Try Again!";
  return $gotData;
}
if(isset($_REQUEST['action'])){
  $action=$_REQUEST['action'];
  if($action=="0" && isset($_REQUEST['email']) && isset($_REQUEST['homeID']) && isset($_REQUEST['roomID']))
  {
    $email=$_REQUEST['email'];
    $roomID=$_REQUEST['roomID'];
    $homeID=$_REQUEST['homeID'];
    $gotData = (object) null;
    $gotData->error=false;
    $gotData->errorMessage="null";
    $gotData->user=(object) null;
    $gotData->con=$con;
    $gotData->user->homeID=$homeID;
    $gotData->user->email=$email;
    $gotData->user->roomID=$roomID;
    $gotData->user->action=$action;
    $gotData=getHardwareData($gotData);
    echo json_encode($gotData);
  }else if($action=="1" && isset($_REQUEST['email']) && isset($_REQUEST['homeID']) && isset($_REQUEST['roomID']) && isset($_REQUEST['hwName']) && isset($_REQUEST['hwSeries']) && isset($_REQUEST['hwIP'])){
    $email=$_REQUEST['email'];
    $homeID=$_REQUEST['homeID'];
    $roomID=$_REQUEST['roomID'];
    $hwName=$_REQUEST['hwName'];
    $hwSeries=$_REQUEST['hwSeries'];
    $hwIP=$_REQUEST['hwIP'];
    $gotData = (object) null;
    $gotData->user=(object) null;
    $gotData->user->hw=(object) null;
    $gotData->error=false;
    $gotData->errorMessage="null";
    $gotData->con=$con;
    $gotData->user->email=$email;
    $gotData->user->hw->email=$email;
    $gotData->user->hw->homeID=$homeID;
    $gotData->user->hw->roomID=$roomID;
    $gotData->user->hw->hwName=ucfirst($hwName);
    $gotData->user->hw->hwSeries=$hwSeries;
    $gotData->user->hw->hwIP=$hwIP;
    $gotData=createHardware($gotData);
    echo json_encode($gotData);
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
    $gotData->user->hw->hwName=ucfirst($hwName);
    $gotData->user->hw->hwSeries=$hwSeries;
    $gotData->user->hw->hwIP=$hwIP;
    $gotData=renameHardware($gotData);
    echo json_encode($gotData);
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
