<?php
require_once("config.php");
require_once("home_data.php");
require_once("room_data.php");
require_once("hardware_data.php");
require_once("user_data.php");
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
function createSubscription($gotData){  // creates subscription before hardware is being created
  $hwSeries=$gotData->user->hw->hwSeries;
  $sql="SELECT * FROM amc WHERE serial_no='$hwSeries'";
  $result=mysqli_query($gotData->con,$sql);
  if($result){
    if(mysqli_num_rows($result)==0){
      $sql="INSERT INTO amc(serial_no) VALUES('$hwSeries')";
      $result=mysqli_query($gotData->con,$sql);
      if($result)
      {
        return $gotData;
      }
      $gotData->error=true;
      $gotData->errorMessage="Error in Subscription.";
      return $gotData;
    }
  }
  return $gotData;
}
function createHardware($gotData){  // creates hardware
  $gotData=getUserID($gotData);
  if($gotData->error) return $gotData;
  $homeID=$gotData->user->hw->homeID;
  $roomID=$gotData->user->hw->roomID;
  $hwName=$gotData->user->hw->hwName;
  $hwSeries=$gotData->user->hw->hwSeries;
  $hwIP=$gotData->user->hw->hwIP;
  $got=(object) null;
  $got->user=(object) null;
  $got->user->email=$gotData->user->email;
  $got->user->userID=$gotData->user->userID;
  $got->user->hwSeries=$hwSeries;
  $got->con=$gotData->con;
  $got->isModifying=false;
  $got->error=false;
  $got->ownedBy=false;
  $got=checkHardwareSeries($got);
  if($got->error) return $got;
  $gotData->ownedBy=$got->ownedBy;
  $userID=$gotData->user->userID;
  $sql="INSERT INTO hardware(uid,hid,rid,name,series,ip_value) VALUES('$userID','$homeID','$roomID','$hwName','$hwSeries','$hwIP')";
  $result=mysqli_query($gotData->con,$sql);
  if($result)
  {
    $gotData->error=false;
    $hw=getHardwareDataUsingNameIDs($gotData->con,$userID,$hwName,$roomID,$homeID);
    if($hw->error) return $hw;
    $gotData->user->hw->id=$hw->hwID;
    if($gotData->ownedBy){
      $gotData=createSubscription($gotData);
      if($gotData->error){
        $got=deleteHardware($gotData);
      }
    }
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="Try again!";
  return $gotData;
}
function hasOwnerShip($con,$hwSeries,$userID){  // checks if user owns hardware series
  $sql="SELECT product_serial.serial_no FROM product_serial INNER JOIN sold_product ON sold_product.serial_id=product_serial.id
        INNER JOIN user ON user.email=sold_product.customer_email WHERE user.id='$userID' AND product_serial.serial_no='$hwSeries'";
  $result=mysqli_query($con,$sql);
  if($result){
    if(mysqli_num_rows($result)==1){
      return true;
    }
  }
  return false;
}
function deleteControlledMembers($gotData){  // deletes all control members from list who has access to given hardwareID
  $hwID=$gotData->user->hw->id;
  $email=$gotData->user->email;
  $userID=$gotData->userID;
  $sql="DELETE allowed_user FROM allowed_user INNER JOIN hardware ON hardware.series=allowed_user.serial_no INNER JOIN product_serial ON product_serial.serial_no=hardware.series INNER JOIN sold_product ON sold_product.id=product_serial.sold_product_id WHERE hardware.id='$hwID' AND sold_product.customer_email='$email' AND allowed_user.uid='$userID'";
  $result=mysqli_query($gotData->con,$sql);
  if($result){
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="Error in removing controlled members.";
  return $gotData;
}
function deleteControlledHardwares($gotData){ // deletes all hardware from user who has creates hardware in their room
  $hwID=$gotData->user->hw->id;
  $userID=$gotData->userID;
  $hw=getHardwareDataUsingID($gotData->con,$userID,$hwID);
  if($hw->error) return $hw;
  $hwSeries=$hw->hwSeries;
  if(!hasOwnerShip($gotData->con,$hwSeries,$userID)){
    return $gotData;
  }
  $sql="DELETE FROM hardware WHERE series='$hwSeries' AND uid!='$userID'";
  $result=mysqli_query($gotData->con,$sql);
  if($result){
    return deleteControlledMembers($gotData);
  }
  $gotData->error=true;
  $gotData->errorMessage="Error in removing controlled hardwares";
  return $gotData;
}
function deleteHardware($gotData){  // deletes hardware using hardware id
  $user=getUserDataUsingEmail($gotData->con,$gotData->user->email);
  if($user->error) return $user;
  $gotData->userID=$user->id;
  $gotData=deleteControlledHardwares($gotData);
  if($gotData->error) return $gotData;
  $id=$gotData->user->hw->id;
  $sql="DELETE `hardware`,`room_device`,`schedule_device`, `devicevalue` FROM hardware LEFT JOIN room_device ON room_device.hw_id=hardware.id LEFT JOIN schedule_device ON schedule_device.device_id=room_device.id LEFT JOIN devicevalue ON devicevalue.did=room_device.id WHERE hardware.id='$id'";
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
function renameHardware($gotData){  // modifies hardware configuration
  $gotData=getUserID($gotData);
  if($gotData->error) return $gotData;
  $hwName=$gotData->user->hw->hwName;
  $hwSeries=$gotData->user->hw->hwSeries;
  $hwIP=$gotData->user->hw->hwIP;
  $id=$gotData->user->hw->id;
  $got=(object) null;
  $got->user=(object) null;
  $got->user->email=$gotData->user->email;
  $got->user->userID=$gotData->user->userID;
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
function getHardwareData($gotData){  // get hardware list for web
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
function getHardwareList($gotData){ // get hardware list for validation
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
function checkAlreadyAdded($con,$hwSeries,$userID){  // check hardware is already has been used
    $sql="SELECT * FROM hardware WHERE uid='$userID' AND series='$hwSeries'";
    $result=mysqli_query($con,$sql);
    if($result){
      if(mysqli_num_rows($result)>0){
        return false;
      }
    }
    return true;
}
function checkAllowedUser($con,$hwSeries,$email,$userID){ // check if user has been given to use hardware
  $sql="SELECT allowed_user.id as `allowedID`, hardware.id as `hwID` FROM allowed_user INNER JOIN hardware ON hardware.uid=allowed_user.uid WHERE allowed_user.member_id='$userID' AND hardware.series='$hwSeries' AND allowed_user.serial_no='$hwSeries'";
  $result=mysqli_query($con,$sql);
  if($result){
    if(mysqli_num_rows($result)==1){
      return checkAlreadyAdded($con,$hwSeries,$userID);
    }
  }
  return false;
}
function checkHardwareSeries($gotData){ // check if user owns hardware which is being created
  $hwSeries = $gotData->user->hwSeries;
  $email = $gotData->user->email;
  $userID = $gotData->user->userID;
  $sql="SELECT product_serial.id FROM product_serial INNER JOIN sold_product ON sold_product.id=product_serial.sold_product_id WHERE product_serial.serial_no='$hwSeries' AND sold_product.customer_email='$email'";
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
      $hwID=$gotData->user->id;
      $sql="SELECT * FROM hardware WHERE series='$hwSeries' AND id!='$hwID'";
    }else{
        $sql="SELECT * FROM hardware WHERE series='$hwSeries'";
    }
    $result=mysqli_query($gotData->con,$sql);
    if($result){
        if(mysqli_num_rows($result)>0){
            $gotData->error=true;
            $gotData->errorMessage="Hardware series has been already used.";
        }else{
          $gotData->ownedBy=true;
        }
        // $productSerialRow=mysqli_fetch_array($result);
        // $gotData->productSerialID=$productSerialRow=['id'];
        return $gotData;
    }
  }
  $gotData->error=true;
  $gotData->errorMessage="Try Again!";
  return $gotData;
}

if(isset($_REQUEST['action'])){
  $action=$_REQUEST['action'];
  if($action=="0" && isset($_REQUEST['email']) && isset($_REQUEST['homeName']) && isset($_REQUEST['roomName']))
  {
    $email=$_REQUEST['email'];
    $homeName=ucfirst($_REQUEST['homeName']);
    $roomName=ucfirst($_REQUEST['roomName']);
    $userID=$_REQUEST['userID'];
    $gotData = (object) null;
    $gotData->error=false;
    $gotData->errorMessage="null";
    $gotData->user=(object) null;
    $gotData->con=$con;
    $gotData->user->homeName=$homeName;
    $gotData->user->email=$email;
    $user=getUserDataUsingEmail($gotData->con,$email);
    $userID=$user->id;
    $gotData->user->user=$userID;
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
  }else if($action=="1" && isset($_REQUEST['email']) && isset($_REQUEST['homeName']) && isset($_REQUEST['roomName']) && isset($_REQUEST['hwName']) && isset($_REQUEST['hwSeries']) && isset($_REQUEST['hwIP'])){
    $email=$_REQUEST['email'];
    $homeName=ucfirst($_REQUEST['homeName']);
    $roomName=ucfirst($_REQUEST['roomName']);
    $hwName=ucfirst($_REQUEST['hwName']);
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
    $user=getUserDataUsingEmail($gotData->con,$email);
    $userID=$user->id;
    $gotData->user->user=$userID;
    $gotData->user->hw->email=$email;
    $gotData->user->hw->homeName=$homeName;
    $gotData->user->hw->roomName=$roomName;
    $gotData->user->hw->hwName=$hwName;
    $gotData->user->hw->hwSeries=$hwSeries;
    $gotData->user->hw->hwIP=$hwIP;
    $r=getRoomDataUsingName($gotData->con,$userID,$roomName,$homeName);
    if($r->error){
      $gotData->con=(object) null;
      echo json_encode($r);
      exit();
    }else{
      $roomID=$r->roomID;
      $gotData->user->hw->roomID=$roomID;
      $homeID=$r->homeID;
      $gotData->user->hw->homeID=$homeID;
      $gotData=createHardware($gotData);
      $gotData->con=(object) null;
      echo json_encode($gotData);
      exit();
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
    $gotData->con=(object) null;
    echo json_encode($gotData);
    exit();
  }
  else if($action=="3" && isset($_REQUEST['email']) && isset($_REQUEST['hwName']) && isset($_REQUEST['hwSeries']) && isset($_REQUEST['hwIP']) && isset($_REQUEST['id'])){
    $email=$_REQUEST['email'];
    $hwName=ucfirst($_REQUEST['hwName']);
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
    $gotData->con=(object) null;
    echo json_encode($gotData);
    exit();
  }else if($action=="4" && isset($_REQUEST['email']) && isset($_REQUEST['homeName']) && isset($_REQUEST['roomName']) && isset($_REQUEST['userID']))
  {
    $email=$_REQUEST['email'];
    $homeName=ucfirst($_REQUEST['homeName']);
    $roomName=ucfirst($_REQUEST['roomName']);
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
      $gotData->con=(object) null;
      echo json_encode($r);
      exit();
    }else{
      $roomID=$r->roomID;
      $gotData->user->roomID=$roomID;
      $homeID=$r->homeID;
      $gotData->user->homeID=$homeID;
      $gotData=getHardwareList($gotData);
      $gotData->con=(object) null;
      echo json_encode($gotData);
      exit();
    }
  }else if($action=="5" && isset($_REQUEST['hwSeries'])){
    $hwSeries=$_REQUEST['hwSeries'];
    $gotData = (object) null;
    $gotData->error=false;
    $gotData->errorMessage="null";
    $gotData->user=(object) null;
    $gotData->con=$con;
    $gotData->user->hwSeries=$hwSeries;
    $gotData=checkHardwareSeries($gotData);
    $gotData->con=(object) null;
    echo json_encode($gotData);
    exit();
  }else{
    $gotData = (object) null;
    $gotData->error=true;
    $gotData->errorMessage="Please, try again after few minutes!";
    echo json_encode($gotData);
    exit();
  }
}else{
  $gotData = (object) null;
  $gotData->error=true;
  $gotData->errorMessage="Please, try again after few minutes!";
  echo json_encode($gotData);
  exit();
}
?>
