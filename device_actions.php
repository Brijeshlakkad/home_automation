<?php
require_once("config.php");
require_once('user_data.php');
require_once("hardware_data.php");
require_once("device_data.php");
require_once('device_slider_data.php');
require_once('device_controller.php');
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
function deservesDeviceSlider($dvImg){
  $dvImgs=['ac.png','fan.png'];
  $flag=false;
  for($i=0;$i<count($dvImgs);$i++){
    if($dvImg==$dvImgs[$i]){
      $flag=true;
    }
  }
  return $flag;
}
function checkDevicePort($gotData){
  $homeID=$gotData->user->device->homeID;
  $roomID=$gotData->user->device->roomID;
  $hwID=$gotData->user->device->hwID;
  $userID=$gotData->user->userID;
  $dvPort=$gotData->user->device->dvPort;
  $sql="SELECT * FROM room_device WHERE hid='$homeID' AND room_id='$roomID' AND hw_id='$hwID' AND uid='$userID' AND port='$dvPort'";
  $result=mysqli_query($gotData->con,$sql);
  if($result){
    if(mysqli_num_rows($result)>0){
      $row=mysqli_fetch_array($result);
      $dvName=$row['device_name'];
      $dvPort=$row['port'];
      $gotData->error=true;
      $gotData->errorMessage=$dvName." has been assigned to ".$dvPort." already";
    }
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="Try again!";
  return $gotData;
}
function checkDeviceName($gotData){
  $homeID=$gotData->user->device->homeID;
  $roomID=$gotData->user->device->roomID;
  $userID=$gotData->user->userID;
  $dvName=$gotData->user->device->dvName;
  $sql="SELECT * FROM room_device WHERE hid='$homeID' AND room_id='$roomID' AND uid='$userID' AND device_name='$dvName'";
  $result=mysqli_query($gotData->con,$sql);
  if($result){
    if(mysqli_num_rows($result)>0){
      $row=mysqli_fetch_array($result);
      $gotData->error=true;
      $gotData->errorMessage="'".$dvName."' name is already in use!";
    }
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="Try again!";
  return $gotData;
}
function createDevice($gotData){
  $userID=$gotData->user->userID;
  $homeID=$gotData->user->device->homeID;
  $roomID=$gotData->user->device->roomID;
  $hwID=$gotData->user->device->hwID;
  $hwSeries=$gotData->user->hwSeries;
  if(hasOwnerShip($gotData->con,$hwSeries,$userID)){
    $sql="INSERT INTO room_device(uid,hid,room_id,hw_id,device_name,device_image,port,status) VALUES('$userID','$homeID','$roomID','$hwID','$dvName','$dvImg','$dvPort','$dvStatus')";
  }else{
    $gotData->error=true;
    $gotData->errorMessage="You do not have access to create device in this hardware";
    return $gotData;
  }
  $gotData=checkDeviceName($gotData);
  if($gotData->error) return $gotData;
  $gotData=checkDevicePort($gotData);
  if($gotData->error) return $gotData;
  $dvName=$gotData->user->device->dvName;
  $dvPort=$gotData->user->device->dvPort;
  $dvImg=$gotData->user->device->dvImg;
  $dvStatus=$gotData->user->device->dvStatus;
  $result=mysqli_query($gotData->con,$sql);
  if($result)
  {
    $gotData->error=false;
    $dv=getDeviceDataUsingNameIDs($gotData->con,$userID,$dvName,$hwID,$roomID,$homeID);
    if($dv->error) return $dv;
    $gotData->user->device->id=$dv->dvID;
    $gotData->user->device->deviceSlider="null";
    $email=$gotData->user->device->email;
    if(deservesDeviceSlider($dvImg)){
      $dvID=$gotData->user->device->id;
      $got=createDeviceSlider($dvID,$gotData->con);
      if($got->error) return $got;
      $gotData->user->device->deviceSlider=$got->user->deviceSlider;
      $gotData->user->device->deviceSlider->email=$email;
    }
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="Try again!";
  return $gotData;
}
function deleteDevice($gotData){
  $id=$gotData->user->device->id;
  $userID=$gotData->user->userID;
  if(!hasOwnerShipUsigDeviceID($gotData->con,$id,$userID)){
    $gotData->error=true;
    $gotData->errorMessage="You do not have access to perform delete operation!";
    return $gotData;
  }
  $sql="DELETE `room_device`,`schedule_device`, `devicevalue` FROM room_device LEFT JOIN schedule_device ON schedule_device.device_id=room_device.id LEFT JOIN devicevalue ON devicevalue.did=room_device.id WHERE room_device.id='$id'";
  $result=mysqli_query($gotData->con,$sql);
  if($result)
  {
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="Try again!";
  return $gotData;
}
function checkDevicePortUsingID($gotData){
  $dvPort=$gotData->user->device->dvPort;
  $id=$gotData->user->device->id;
  $sql="SELECT * FROM room_device WHERE id='$id'";
  $result=mysqli_query($gotData->con,$sql);
  if($result){
    $row=mysqli_fetch_array($result);
    $gotData->user->device->homeID=$row['hid'];
    $gotData->user->device->roomID=$row['room_id'];
    $gotData->user->device->hwID=$row['hw_id'];
    $gotData->user->device->userID=$row['uid'];
    return checkDevicePort($gotData);
  }
  $gotData->error=true;
  $gotData->errorMessage="Try Again!";
  return $gotData;
}
function renameDevice($gotData){
  $gotData=getUserID($gotData);
  if($gotData->error==true) return $gotData;
  $dvName=$gotData->user->device->dvName;
  $dvPort=$gotData->user->device->dvPort;
  $dvImg=$gotData->user->device->dvImg;
  $id=$gotData->user->device->id;
  $email=$gotData->user->device->email;
  $gotData=checkDevicePortUsingID($gotData);
  if($gotData->error) return $gotData;
  $sql="UPDATE room_device SET device_name='$dvName', port='$dvPort', device_image='$dvImg' where id='$id'";
  $result=mysqli_query($gotData->con,$sql);
  if($result && (mysqli_affected_rows($gotData->con)==1))
  {
    $dvID=$gotData->user->device->id;
    $got=deleteDeviceSlider($dvID,$gotData->con);
    if($got->error) return $got;
    if(deservesDeviceSlider($dvImg)){
      if(!checkDeviceSliderExists($dvID,$gotData->con)){
        $got=createDeviceSlider($dvID,$gotData->con);
        if($got->error) return $got;
        $gotData->user->device->deviceSlider=$got->user->deviceSlider;
        $gotData->user->device->deviceSlider->email=$email;
      }
    }
    $gotData->error=false;
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="Try again!";
  return $gotData;
}
function getDvImgValue($con,$dvImg){
  $sql="select * from device where image='$dvImg'";
  $result=mysqli_query($con,$sql);
  if($result){
    $row=mysqli_fetch_array($result);
    return $row['name'];
  }else{
    return $dvImg;
  }
}
function getDeviceImg($con,$dvImg){
  $sql="SELECT * FROM device WHERE image='$dvImg'";
  $check=mysqli_query($con,$sql);
  if($check && (mysqli_num_rows($check)>0))
  {
    $row=mysqli_fetch_array($check);
    $key=$row['image'];
    $value=$row['name'];
    $maxVal=$row['max_val'];
    $deviceImg=(object) null;
    $deviceImg->key=$key;
    $deviceImg->value=$value;
    $deviceImg->maxVal=$maxVal;
    return $deviceImg;
  }
  return null;
}
function hasOwnerShip($con,$hwSeries,$userID){
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
function hasOwnerShipUsigDeviceID($con,$deviceID,$userID){
  $sql="SELECT * FROM room_device WHERE id='$deviceID' AND uid='$userID'";
  $result=mysqli_query($con,$sql);
  if($result){
    if(mysqli_num_rows($result)==1){
      return true;
    }
  }
  return false;
}
function getDeviceData($gotData){
  $userID=$gotData->user->userID;
  $homeID=$gotData->user->homeID;
  $roomID=$gotData->user->roomID;
  $hwID=$gotData->user->hwID;
  $homeName=$gotData->user->homeName;
  $roomName=$gotData->user->roomName;
  $hwName=$gotData->user->hwName;
  $hwSeries=$gotData->user->hwSeries;
  $owner=0;
  if(hasOwnerShip($gotData->con,$hwSeries,$userID)){
    $sql="SELECT room_device.id as `dvID`, room_device.device_name as `dvName`, room_device.port as `dvPort`, room_device.device_image as `dvImg`
          FROM room_device INNER JOIN hardware ON hardware.id=room_device.hw_id WHERE hardware.series='$hwSeries' AND hardware.uid='$userID'";
    $owner=1;
  }else{
    $sql="SELECT room_device.id as `dvID`, room_device.device_name as `dvName`, room_device.port as `dvPort`, room_device.device_image as `dvImg`
          FROM room_device INNER JOIN hardware ON hardware.id=room_device.hw_id INNER JOIN allowed_user ON allowed_user.serial_no=hardware.series
          INNER JOIN product_serial ON product_serial.serial_no=allowed_user.serial_no INNER JOIN sold_product ON sold_product.serial_id=product_serial.id
          WHERE allowed_user.member_id='$userID' AND allowed_user.serial_no='$hwSeries'";
  }
  $check=mysqli_query($gotData->con,$sql);
  $gotData->total=mysqli_num_rows($check);
  if($check && ($gotData->total>=0))
  {
    $gotData->user->allDevice=(object) null;
    $email=$gotData->user->email;
    if($gotData->total==0){
      $noFound='<div class="row" align="center" style="margin-top: 80px;">
              	<div id="no_found"><img src="images/not-found.png" width="100px" alt="no found" /></div>
              	<br/>
              	<div style="color:gray;"><h4>No Devices</h4></div>
              	</div>';
      $gotData->user->allDevice=$noFound;
      return $gotData;
    }
    $allDevice="<div class='grid-container' style='cursor:pointer;'>";
    while($row=mysqli_fetch_array($check)){
      $dvID=$row['dvID'];
      $got=getDeviceStatusUsingScheduling($gotData->con,$dvID);
      if($got->error) return $got;
      if($got->isScheduleRunning){
        $dvStatus=$got->afterStatus;
      }
      else{
        $dvStatus=$got->status;
      }
      $dvName=$row['dvName'];
      $dvPort=$row['dvPort'];
      $dvImg=$row['dvImg'];
      // $dvStatus=$row['status'];
      $id=$row['dvID'];
      $editFunc = "editDevice($id,'".$dvName."','".$dvPort."','".$dvImg."')";
      $dvImgValue=getDvImgValue($gotData->con,$dvImg);
      if($dvStatus==1){
        $drawStatus="<span class='greenDot'></span>";
      }
      else{
        $drawStatus="<span class='redDot'></span>";
      }
      $deleteButton="<button class='btn btn-default' onclick='deleteDevice($id)' ><span class='glyphicon glyphicon-trash'></span> Delete</button>";
      $editButton="<button class='btn btn-default' onclick=$editFunc><span class='glyphicon glyphicon-pencil'></span> Edit</button>";
      // if($owner==1){
      //   $actionButton='<div class="col-md-3">'.$editButton.'</div><div class="col-md-3">'.$deleteButton.'</div>';
      // }else{
      //   $actionButton='<div class="col-md-6">'.$editButton.'</div>';
      // }
      $actionButton='<div class="col-md-3">'.$editButton.'</div><div class="col-md-3">'.$deleteButton.'</div>';
      $allDevice.='<div class="grid-item card"><div class="row pull-right" style="padding-right:10px;">'.$drawStatus.'</div><div class="row"><a href="#!customer/home/'.$homeName.'/'.$roomName.'/'.$hwName.'/'.$dvName.'">'.$dvName.'</a></div><div class="row" style="color:rgba(180,180,180,1.0);font-size:15px;">'.$dvImgValue.'</div><div class="row"><div class="col-md-3"></div>'.$actionButton.'<div class="col-md-3"></div></div></div>';
    }
    $allDevice.='</div>';
    $gotData->user->allDevice=$allDevice;
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="Try again!";
  return $gotData;
}
function getDeviceList($gotData){
  $gotData=getUserID($gotData);
  if($gotData->error==true) return $gotData;
  $userID=$gotData->user->userID;
  $homeID=$gotData->user->homeID;
  $roomID=$gotData->user->roomID;
  $hwID=$gotData->user->hwID;
  $hwSeries=$gotData->user->hwSeries;
  if(hasOwnerShip($gotData->con,$hwSeries,$userID)){
    $sql="SELECT room_device.id as `dvID`, room_device.device_name as `dvName`, room_device.port as `dvPort`, room_device.device_image as `dvImg`, room_device.status as `status`
          FROM room_device INNER JOIN hardware ON hardware.id=room_device.hw_id WHERE hardware.series='$hwSeries' AND hardware.uid='$userID'";
    $owner=1;
  }else{
    $sql="SELECT room_device.id as `dvID`, room_device.device_name as `dvName`, room_device.port as `dvPort`, room_device.device_image as `dvImg`, room_device.status as `status`
          FROM room_device INNER JOIN hardware ON hardware.id=room_device.hw_id INNER JOIN allowed_user ON allowed_user.serial_no=hardware.series
          INNER JOIN product_serial ON product_serial.serial_no=allowed_user.serial_no INNER JOIN sold_product ON sold_product.serial_id=product_serial.id
          WHERE allowed_user.member_id='$userID' AND allowed_user.serial_no='$hwSeries'";
  }
  $check=mysqli_query($gotData->con,$sql);
  $gotData->total=mysqli_num_rows($check);
  if($check && ($gotData->total>=0))
  {
    $i=0;
    $email=$gotData->user->email;
    while($row=mysqli_fetch_array($check)){
      $gotData->user->device[$i] = (object) null;
      $dvName=$row['dvName'];
      $dvPort=$row['dvPort'];
      $dvImg=$row['dvImg'];
      $dvStatus=$row['status'];
      $id=$row['dvID'];
      $deviceImg=getDeviceImg($gotData->con,$dvImg);
      $gotData->user->device[$i]=(object) null;
      $gotData->user->device[$i]->deviceImg=(object) null;
      $gotData->user->device[$i]->deviceImg=$deviceImg;
      $gotData->user->device[$i]->dvName=$dvName;
      $gotData->user->device[$i]->dvPort=$dvPort;
      $gotData->user->device[$i]->dvImg=$dvImg;
      $gotData->user->device[$i]->dvStatus=$dvStatus;
      $gotData->user->device[$i]->homeID=$homeID;
      $gotData->user->device[$i]->roomID=$roomID;
      $gotData->user->device[$i]->hwID=$hwID;
      $gotData->user->device[$i]->id=$id;
      $gotData->user->device[$i]->email=$email;
      $gotData->user->device[$i]->deviceSlider="null";
      if(deservesDeviceSlider($dvImg)){
        $dvID=$id;
        $got=getDeviceSlider($dvID,$gotData->con);
        if($got->error) return $got;
        $gotData->user->device[$i]->deviceSlider=$got->user->deviceSlider;
        $gotData->user->device[$i]->deviceSlider->email=$email;
      }
      $i++;
    }
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="Try again!";
  return $gotData;
}
function getDevice($gotData){
  $userID=$gotData->user->userID;
  $email=$gotData->user->email;
  $deviceID=$gotData->user->deviceID;
  $deviceName=$gotData->user->deviceName;
  $hwSeries=$gotData->user->hwSeries;
  if(hasOwnerShip($gotData->con,$hwSeries,$userID)){
    $sql="SELECT room_device.id as `dvID`, room_device.device_name as `dvName`, room_device.port as `dvPort`, room_device.device_image as `dvImg`
          FROM room_device INNER JOIN hardware ON hardware.id=room_device.hw_id WHERE hardware.series='$hwSeries' AND hardware.uid='$userID' AND room_device.device_name='$deviceName'";
  }else{
    $sql="SELECT room_device.id as `dvID`, room_device.device_name as `dvName`, room_device.port as `dvPort`, room_device.device_image as `dvImg`,
          room_device.hid as `homeID`, room_device.room_id as `roomID`, room_device.hw_id as `hwID`
          FROM room_device INNER JOIN hardware ON hardware.id=room_device.hw_id INNER JOIN allowed_user ON allowed_user.serial_no=hardware.series
          INNER JOIN product_serial ON product_serial.serial_no=allowed_user.serial_no INNER JOIN sold_product ON sold_product.serial_id=product_serial.id
          WHERE allowed_user.member_id='$userID' AND allowed_user.serial_no='$hwSeries' AND room_device.device_name='$deviceName'";
  }
  $check=mysqli_query($gotData->con,$sql);
  $gotData->total=mysqli_num_rows($check);
  if($check && ($gotData->total==1))
  {
    $row=mysqli_fetch_array($check);
    $dvID=$row['dvID'];
    $dvName=$row['dvName'];
    $dvPort=$row['dvPort'];
    $dvImg=$row['getDeviceImg'];
    $got=getDeviceStatusUsingScheduling($gotData->con,$dvID);
    if($got->error) return $got;
    $isScheduleRunning=$got->isScheduleRunning;
    if($got->isScheduleRunning){
      $dvStatus=$got->afterStatus;
    }
    else{
      $dvStatus=$got->status;
    }
    $homeID=$row['homeID'];
    $roomID=$row['roomID'];
    $hwID=$row['hwID'];
    $id=$row['dvID'];
    $gotData->error=false;
    $deviceImg=getDeviceImg($gotData->con,$dvImg);
    $gotData->user->deviceImg=$deviceImg;
    $gotData->user->dvName=$dvName;
    $gotData->user->dvPort=$dvPort;
    $gotData->user->dvImg=$dvImg;
    $gotData->user->dvStatus=$dvStatus;
    $gotData->user->homeID=$homeID;
    $gotData->user->roomID=$roomID;
    $gotData->user->isScheduleRunning=$isScheduleRunning;
    $gotData->user->hwID=$hwID;
    $gotData->user->id=$id;
    $gotData->user->deviceSlider="null";
    if(deservesDeviceSlider($dvImg)){
      $dvID=$id;
      $got=getDeviceSlider($dvID,$gotData->con);
      if($got->error) return $got;
      $gotData->user->deviceSlider=$got->user->deviceSlider;
      $gotData->user->deviceSlider->email=$email;
    }
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="Try again!";
  return $gotData;
}
function getDeviceDataUsingHwSeries($gotData){
  $userID=$gotData->user->userID;
  $deviceName=$gotData->user->deviceName;
  $hwSeries=$gotData->user->hwSeries;
  if(hasOwnerShip($gotData->con,$hwSeries,$userID)){
    $sql="SELECT room_device.id as `dvID`, room_device.device_name as `dvName`, room_device.port as `dvPort`, room_device.device_image as `dvImg`
          FROM room_device INNER JOIN hardware ON hardware.id=room_device.hw_id WHERE hardware.series='$hwSeries' AND hardware.uid='$userID' AND room_device.device_name='$deviceName'";
  }else{
    $sql="SELECT room_device.id as `dvID`, room_device.device_name as `dvName`, room_device.port as `dvPort`, room_device.device_image as `dvImg`,
          room_device.hid as `homeID`, room_device.room_id as `roomID`, room_device.hw_id as `hwID`
          FROM room_device INNER JOIN hardware ON hardware.id=room_device.hw_id INNER JOIN allowed_user ON allowed_user.serial_no=hardware.series
          INNER JOIN product_serial ON product_serial.serial_no=allowed_user.serial_no INNER JOIN sold_product ON sold_product.serial_id=product_serial.id
          WHERE allowed_user.member_id='$userID' AND allowed_user.serial_no='$hwSeries' AND room_device.device_name='$deviceName'";
  }
  $check=mysqli_query($gotData->con,$sql);
  $gotData->total=mysqli_num_rows($check);
  if($check && ($gotData->total==1))
  {
    $row=mysqli_fetch_array($check);
    $gotData->user->deviceID=$row['dvID'];
    $gotData->user->dvID=$row['dvID'];
    $gotData->user->dvName=$row['dvName'];
    $gotData->user->dvPort=$row['dvPort'];
    $gotData->user->dvImg=$row['getDeviceImg'];
    $gotData->user->homeID=$row['homeID'];
    $gotData->user->roomID=$row['roomID'];
    $gotData->user->hwID=$row['hwID'];
    return $gotData;
  }else{
    $gotData->error=true;
    $gotData->errorMessage="Device does not exists!";
  }
}
function changeDeviceStatus($gotData){
  $status=$gotData->user->status;
  $deviceID=$gotData->user->deviceID;
  $got=getDeviceStatusUsingScheduling($gotData->con,$deviceID);
  if($got->error) return $got;
  $isScheduleRunning=$got->isScheduleRunning;
  if($got->isScheduleRunning){
    $gotData->error=true;
    $gotData->errorMessage="Your Scheduling is running. Please remove scheduling to change status.";
    return $gotData;
  }
  $sql="UPDATE room_device SET status='$status' where id='$deviceID'";
  $result=mysqli_query($gotData->con,$sql);
  if($result)
  {
    return getDevice($gotData);
  }
  $gotData->error=true;
  $gotData->errorMessage="Try again!";
  return $gotData;
}
function getDeviceImgs($gotData){
  $sql="SELECT * FROM device";
  $check=mysqli_query($gotData->con,$sql);
  $gotData->total=mysqli_num_rows($check);
  if($check && ($gotData->total>=0))
  {
    $i=0;
    while($row=mysqli_fetch_array($check)){
      $gotData->user->deviceImg[$i] = (object) null;
      $key=$row['image'];
      $value=$row['name'];
      $gotData->user->deviceImg[$i]=(object) null;
      $gotData->user->deviceImg[$i]->key=$key;
      $gotData->user->deviceImg[$i]->value=$value;
      $i++;
    }
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="Try again!";
  return $gotData;
}
function checkDeviceSliderExists($dvID,$con){
  $sql="SELECT * from devicevalue where did='$dvID'";
  $check=mysqli_query($con,$sql);
  $total=mysqli_num_rows($check);
  if($check && ($total==1))
  {
    return true;
  }
  return false;
}
function createDeviceSlider($dvID,$con,$value="0"){
  $gotData=(object) null;
  $gotData->user=(object) null;
  $gotData->error=false;
  $sql="INSERT INTO devicevalue(did,value) VALUES('$dvID','$value')";
  $check=mysqli_query($con,$sql);
  if($check)
  {
    $gotData->user->deviceSlider=(object) null;
    $gotData->user->deviceSlider->dvID=$dvID;
    $gotData->user->deviceSlider->value=$value;
    $dvSlider=getDeviceSliderDataUsingDID($con,$dvID);
    if($dvSlider->error) return $dvSlider;
    $gotData->user->deviceSlider->id=$dvSlider->id;
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="Try again!";
  return $gotData;
}
function getDeviceSlider($dvID,$con){
  $gotData=(object) null;
  $gotData->user=(object) null;
  $gotData->error=false;
  $sql="SELECT * FROM devicevalue where did='$dvID'";
  $check=mysqli_query($con,$sql);
  $gotData->total=mysqli_num_rows($check);
  if($check && ($gotData->total==1))
  {
    $row=mysqli_fetch_array($check);
    $id=$row['id'];
    $value=$row['value'];
    $gotData->user->deviceSlider=(object) null;
    $gotData->user->deviceSlider->dvID=$dvID;
    $gotData->user->deviceSlider->id=$id;
    $gotData->user->deviceSlider->value=$value;
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="Try again!";
  return $gotData;
}
function changeDeviceSlider($gotData){
  $gotData=getUserID($gotData);
  if($gotData->error==true) return $gotData;
  $dvID=$gotData->deviceSlider->dvID;
  $value=$gotData->deviceSlider->value;
  $sql="UPDATE devicevalue SET value='$value' where did='$dvID'";
  $check=mysqli_query($gotData->con,$sql);
  if($check)
  {
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="Try again!";
  return $gotData;
}
function deleteDeviceSlider($dvID,$con){
  $gotData=(object) null;
  $gotData->error=false;
  $sql="DELETE FROM devicevalue where did='$dvID'";
  $result=mysqli_query($con,$sql);
  if($result)
  {
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="Try again!";
  return $gotData;
}
if(isset($_REQUEST['action'])){
  $action=$_REQUEST['action'];
  if($action=="0" && isset($_REQUEST['email']) && isset($_REQUEST['homeName']) && isset($_REQUEST['roomName']) && isset($_REQUEST['hwName']))
  {
    $email=$_REQUEST['email'];
    $hwName=ucfirst($_REQUEST['hwName']);
    $roomName=ucfirst($_REQUEST['roomName']);
    $homeName=ucfirst($_REQUEST['homeName']);
    $gotData = (object) null;
    $gotData->error=false;
    $gotData->errorMessage="null";
    $gotData->user=(object) null;
    $gotData->con=$con;
    $gotData->user->email=$email;
    $u=getUserDataUsingEmail($gotData->con,$gotData->user->email);
    if($u->error) return $u;
    $userID=$u->id;
    $gotData->user->homeName=$homeName;
    $gotData->user->roomName=$roomName;
    $gotData->user->hwName=$hwName;
    $gotData->user->action=$action;
    $gotData->user->userID=$userID;
    $hw=getHardwareDataUsingName($gotData->con,$userID,$hwName,$roomName,$homeName);
    if($hw->error){
      echo json_encode($hw);
      exit();
    }else{
      $hwID=$hw->hwID;
      $gotData->user->hwID=$hwID;
      $roomID=$hw->roomID;
      $gotData->user->roomID=$roomID;
      $homeID=$hw->homeID;
      $gotData->user->homeID=$homeID;
      $gotData->user->hwSeries=$hw->hwSeries;
      $gotData=getDeviceData($gotData);
      $gotData->con=(object) null;
      echo json_encode($gotData);
    }
  }else if($action=="1" && isset($_REQUEST['email']) && isset($_REQUEST['homeName']) && isset($_REQUEST['roomName']) && isset($_REQUEST['hwName']) && isset($_REQUEST['dvName']) && isset($_REQUEST['dvPort']) && isset($_REQUEST['dvImg']) && isset($_REQUEST['userID'])){
    $email=$_REQUEST['email'];
    $homeName=ucfirst($_REQUEST['homeName']);
    $roomName=ucfirst($_REQUEST['roomName']);
    $hwName=ucfirst($_REQUEST['hwName']);
    $dvName=ucfirst($_REQUEST['dvName']);
    $dvPort=$_REQUEST['dvPort'];
    $dvImg=$_REQUEST['dvImg'];
    $userID=$_REQUEST['userID'];
    $gotData = (object) null;
    $gotData->user=(object) null;
    $gotData->user->device=(object) null;
    $gotData->error=false;
    $gotData->errorMessage="null";
    $gotData->con=$con;
    $gotData->user->email=$email;
    $u=getUserDataUsingEmail($gotData->con,$gotData->user->email);
    if($u->error) return $u;
    $userID=$u->id;
    $gotData->user->userID=$userID;
    $gotData->user->device->email=$email;
    $gotData->user->device->homeName=$homeName;
    $gotData->user->device->roomName=$roomName;
    $gotData->user->device->hwName=$hwName;
    $gotData->user->device->dvName=$dvName;
    $gotData->user->device->dvPort=$dvPort;
    $gotData->user->device->dvImg=$dvImg;
    $gotData->user->device->dvStatus="0";
    $hw=getHardwareDataUsingName($gotData->con,$userID,$hwName,$roomName,$homeName);
    if($hw->error){
      echo json_encode($hw);
    }else{
      $hwID=$hw->hwID;
      $gotData->user->device->hwID=$hwID;
      $roomID=$hw->roomID;
      $gotData->user->device->roomID=$roomID;
      $homeID=$hw->homeID;
      $gotData->user->device->homeID=$homeID;
      $gotData->user->hwSeries=$hw->hwSeries;
      $gotData=createDevice($gotData);
      $gotData->con=(object) null;
      echo json_encode($gotData);
    }
  }
  else if($action=="2" && isset($_REQUEST['email']) && isset($_REQUEST['id'])){
    $email=$_REQUEST['email'];
    $id=$_REQUEST['id'];
    $gotData = (object) null;
    $gotData->user=(object) null;
    $gotData->user->device=(object) null;
    $gotData->error=false;
    $gotData->errorMessage="null";
    $gotData->con=$con;
    $gotData->user->email=$email;
    $gotData->user->device->email=$email;
    $gotData->user->device->id=$id;
    $u=getUserDataUsingEmail($gotData->con,$gotData->user->email);
    if($u->error) {
      echo json_encode($gotData);
      exit();
    }
    $gotData->user->userID=$u->id;
    $gotData=deleteDevice($gotData);
    $gotData->con=(object) null;
    echo json_encode($gotData);
  }
  else if($action=="3" && isset($_REQUEST['email']) && isset($_REQUEST['dvName']) && isset($_REQUEST['dvPort']) && isset($_REQUEST['dvImg']) && isset($_REQUEST['id'])){
    $email=$_REQUEST['email'];
    $dvName=ucfirst($_REQUEST['dvName']);
    $dvPort=$_REQUEST['dvPort'];
    $dvImg=$_REQUEST['dvImg'];
    $id=$_REQUEST['id'];
    $gotData = (object) null;
    $gotData->user=(object) null;
    $gotData->user->device=(object) null;
    $gotData->error=false;
    $gotData->errorMessage="null";
    $gotData->con=$con;
    $gotData->user->device->id=$id;
    $gotData->user->device->email=$email;
    $gotData->user->email=$email;
    $gotData->user->device->dvName=$dvName;
    $gotData->user->device->dvPort=$dvPort;
    $gotData->user->device->dvImg=$dvImg;
    $gotData=renameDevice($gotData);
    $gotData->con=(object) null;
    echo json_encode($gotData);
  }
  else if($action=="4"){
    $gotData = (object) null;
    $gotData->error=false;
    $gotData->errorMessage="null";
    $gotData->user=(object) null;
    $gotData->con=$con;
    $gotData=getDeviceImgs($gotData);
    $gotData->con=(object) null;
    echo json_encode($gotData);
  }
  else if($action=="5" && isset($_REQUEST['email']) && isset($_REQUEST['deviceName']) && isset($_REQUEST['homeName']) && isset($_REQUEST['roomName']) && isset($_REQUEST['hwName'])){
    $email=$_REQUEST['email'];
    $homeName=ucfirst($_REQUEST['homeName']);
    $roomName=ucfirst($_REQUEST['roomName']);
    $hwName=ucfirst($_REQUEST['hwName']);
    $deviceName=ucfirst($_REQUEST['deviceName']);
    $gotData = (object) null;
    $gotData->error=false;
    $gotData->errorMessage="null";
    $gotData->user=(object) null;
    $gotData->con=$con;
    $gotData->user->email=$email;
    $u=getUserDataUsingEmail($gotData->con,$gotData->user->email);
    if($u->error) return $u;
    $userID=$u->id;
    $gotData->user->homeName=$homeName;
    $gotData->user->roomName=$roomName;
    $gotData->user->hwName=$hwName;
    $gotData->user->action=$action;
    $gotData->user->userID=$userID;
    $gotData->user->deviceName=$deviceName;
    $hw=getHardwareDataUsingName($gotData->con,$userID,$hwName,$roomName,$homeName);
    if($hw->error){
      echo json_encode($hw);
    }else{
      $hwID=$hw->hwID;
      $gotData->user->hwID=$hwID;
      $roomID=$hw->roomID;
      $gotData->user->roomID=$roomID;
      $homeID=$hw->homeID;
      $gotData->user->homeID=$homeID;
      $gotData->user->hwSeries=$hw->hwSeries;
      $gotData=getDevice($gotData);
      $gotData->con=(object) null;
      echo json_encode($gotData);
    }
  }
  else if($action=="6" && isset($_REQUEST['email']) && isset($_REQUEST['deviceName']) && isset($_REQUEST['status']) && isset($_REQUEST['homeName']) && isset($_REQUEST['roomName']) && isset($_REQUEST['hwName'])){
    $email=$_REQUEST['email'];
    $homeName=ucfirst($_REQUEST['homeName']);
    $roomName=ucfirst($_REQUEST['roomName']);
    $hwName=ucfirst($_REQUEST['hwName']);
    $deviceName=ucfirst($_REQUEST['deviceName']);
    $status=$_REQUEST['status'];
    $userID=$_REQUEST['userID'];
    $gotData = (object) null;
    $gotData->error=false;
    $gotData->errorMessage="null";
    $gotData->user=(object) null;
    $gotData->con=$con;
    $gotData->user->email=$email;
    $gotData->user->status=$status;
    $u=getUserDataUsingEmail($gotData->con,$gotData->user->email);
    if($u->error) return $u;
    $userID=$u->id;
    $gotData->user->homeName=$homeName;
    $gotData->user->roomName=$roomName;
    $gotData->user->hwName=$hwName;
    $gotData->user->userID=$userID;
    $gotData->user->deviceName=$deviceName;
    $hw=getHardwareDataUsingName($gotData->con,$userID,$hwName,$roomName,$homeName);
    if($hw->error){
      echo json_encode($hw);
    }else{
      $hwID=$hw->hwID;
      $gotData->user->hwID=$hwID;
      $roomID=$hw->roomID;
      $gotData->user->roomID=$roomID;
      $homeID=$hw->homeID;
      $gotData->user->homeID=$homeID;
      $gotData->user->hwSeries=$hw->hwSeries;
      $gotData=getDeviceDataUsingHwSeries($gotData);
      if($gotData->error){
        echo json_encode($gotData);
      }
      $gotData=changeDeviceStatus($gotData);
      $gotData->con=(object) null;
      echo json_encode($gotData);
    }
  }
  else if($action=="7" && isset($_REQUEST['email']) && isset($_REQUEST['deviceName']) & isset($_REQUEST['value']) && isset($_REQUEST['homeName']) && isset($_REQUEST['roomName']) && isset($_REQUEST['hwName'])){
    $email=$_REQUEST['email'];
    $homeName=ucfirst($_REQUEST['homeName']);
    $roomName=ucfirst($_REQUEST['roomName']);
    $hwName=ucfirst($_REQUEST['hwName']);
    $deviceName=$_REQUEST['deviceName'];
    $userID=$_REQUEST['userID'];
    $value=$_REQUEST['value'];
    $gotData = (object) null;
    $gotData->error=false;
    $gotData->errorMessage="null";
    $gotData->user=(object) null;
    $gotData->deviceSlider=(object) null;
    $gotData->con=$con;
    $gotData->user->email=$email;
    $gotData->deviceSlider->email=$email;
    $gotData->deviceSlider->value=$value;
    $dv=getDeviceDataUsingName($gotData->con,$userID,$deviceName,$hwName,$roomName,$homeName);
    if($dv->error){
      echo json_encode($dv);
    }else{
      $deviceID=$dv->dvID;
      $gotData->deviceSlider->dvID=$deviceID;
      $gotData=changeDeviceSlider($gotData);
      $gotData->con=(object) null;
      echo json_encode($gotData);
    }
  }else if($action=="8" && isset($_REQUEST['email']) && isset($_REQUEST['homeName']) && isset($_REQUEST['roomName']) && isset($_REQUEST['hwName']) && isset($_REQUEST['userID']))
  {
    $email=$_REQUEST['email'];
    $hwName=ucfirst($_REQUEST['hwName']);
    $roomName=ucfirst($_REQUEST['roomName']);
    $homeName=ucfirst($_REQUEST['homeName']);
    $userID=$_REQUEST['userID'];
    $gotData = (object) null;
    $gotData->error=false;
    $gotData->errorMessage="null";
    $gotData->user=(object) null;
    $gotData->con=$con;
    $gotData->user->email=$email;
    $gotData->user->homeName=ucfirst($homeName);
    $gotData->user->roomName=ucfirst($roomName);
    $gotData->user->hwName=ucfirst($hwName);
    $gotData->user->action=$action;
    $hw=getHardwareDataUsingName($gotData->con,$userID,$hwName,$roomName,$homeName);
    if($hw->error){
      echo json_encode($hw);
    }else{
      $hwID=$hw->hwID;
      $gotData->user->hwID=$hwID;
      $roomID=$hw->roomID;
      $gotData->user->roomID=$roomID;
      $homeID=$hw->homeID;
      $gotData->user->homeID=$homeID;
      $gotData->user->hwSeries=$hw->hwSeries;
      $gotData=getDeviceList($gotData);
      $gotData->con=(object) null;
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
