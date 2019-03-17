<?php
function verifyData($gotResult){
  if(!filter_var($gotResult->email, FILTER_VALIDATE_EMAIL))
  {
    $gotResult->error=1;
    $gotResult->errorMessage="email address is not verified with our application.";
    return $gotResult;
  }
  if($gotResult->deviceName=="null")
  {
    $gotResult->error=1;
    $gotResult->errorMessage="Device name is not specified";
    return $gotResult;
  }else{
    $gotResult->deviceName=str_replace(" ","",$gotResult->deviceName);
  }
  if($gotResult->roomName=="null")
  {
    $gotResult->error=1;
    $gotResult->errorMessage="Room name is not specified";
    return $gotResult;
  }else{
    $gotResult->roomName=str_replace(" ","",$gotResult->roomName);
  }
  if($gotResult->status!=1 && $gotResult->status!=0 && $gotResult->status!=2 && $gotResult->status!=5)
  {
    $gotResult->error=1;
    $gotResult->errorMessage="Specification is not correct";
    return $gotResult;
  }
  $gotResult->error=0;
  $gotResult->errorMessage="null";
  $gotResult->roomName = ucfirst($gotResult->roomName);
  return $gotResult;
}

function getUserID($gotResult)
{
  $sql="SELECT * FROM user where email='$gotResult->email'";
  $check=mysqli_query($gotResult->con,$sql);
  if($check && (mysqli_num_rows($check)==1))
  {
    $row=mysqli_fetch_array($check);
    $gotResult->userID=$row['id'];
    return $gotResult;
  }
  $gotResult->error=1;
  $gotResult->errorMessage="You do not have account in OUR app. Please registor yourself at OUR app";
  return $gotResult;
}

function getRoomID($gotResult)
{
  $sql="SELECT * FROM room where uid='$gotResult->userID' and roomname='$gotResult->roomName'";
  $check=mysqli_query($gotResult->con,$sql);
  if($check && (mysqli_num_rows($check)==1))
  {
    $row=mysqli_fetch_array($check);
    $gotResult->roomID=$row['id'];
    return $gotResult;
  }
  $gotResult->error=1;
  $gotResult->errorMessage="You do not have room named ".$gotResult->roomName;
  return $gotResult;
}

function performAction($gotResult){
  $sql="SELECT id from room_device where uid='$gotResult->userID' and room_id='$gotResult->roomID' and device_name='$gotResult->deviceName'";
  $check=mysqli_query($gotResult->con,$sql);
  if($check && mysqli_num_rows($check)>0)
  {
      $sql="UPDATE room_device SET status='$gotResult->status' WHERE uid='$gotResult->userID' and room_id='$gotResult->roomID' and device_name='$gotResult->deviceName'";
      $check=mysqli_query($gotResult->con,$sql);
      if($check)
      {
        $gotResult->error=0;
        $gotResult->errorMessage="null";
        return $gotResult;
      }else{
        $gotResult->error=1;
        $gotResult->errorMessage="You do not have device named ".$gotResult->deviceName;
        return $gotResult;
      }
  }
  $gotResult->error=1;
  $gotResult->errorMessage="You do not have device named ".$gotResult->deviceName;
  return $gotResult;
}
function getDataAction($gotResult){
  $sql="SELECT id from room_device where uid='$gotResult->userID' and room_id='$gotResult->roomID' and device_name='$gotResult->deviceName'";
  $check=mysqli_query($gotResult->con,$sql);
  if($check && mysqli_num_rows($check)>0)
  {
      $deviceRow=mysqli_fetch_array($check);
      $sql="SELECT * from room_device WHERE uid='$gotResult->userID' and room_id='$gotResult->roomID' and device_name='$gotResult->deviceName'";
      $check=mysqli_query($gotResult->con,$sql);
      if($check)
      {
        $gotResult->error=0;
        $gotResult->errorMessage="null";
        $row=mysqli_fetch_array($check);
        if($row['status']==0)
        {
          $gotResult->data="Your ".$gotResult->deviceName.' is off.';
        }else if($row['status']==1)
        {
          $gotResult->data="Your ".$gotResult->deviceName.' is on';
          try{
            $dvID=$deviceRow['id'];
            $sql="SELECT * FROM devicevalue WHERE did='$dvID'";
            $check=mysqli_query($gotResult->con,$sql);
            if($check && (mysqli_num_rows($check)==1))
            {
              $dvValueRow=mysqli_fetch_array($check);
              $value=$dvValueRow['value'];
              $gotResult->data.=", and it's been set to ".$value;
            }
          }catch(Exception $e)
          {

          }
        }
        else{
          $gotResult->data="Can not reach to your ".$gotResult->deviceName." device";
        }
        return $gotResult;
      }else{
        $gotResult->error=1;
        $gotResult->errorMessage="You do not have device named ".$gotResult->deviceName;
        return $gotResult;
      }
  }
  $gotResult->error=1;
  $gotResult->errorMessage="You do not have device named ".$gotResult->deviceName;
  return $gotResult;
}
function performActionAll($gotResult){
  $sql="SELECT id from room_device where uid='$gotResult->userID' and room_id='$gotResult->roomID'";
  $check=mysqli_query($gotResult->con,$sql);
  if($check && mysqli_num_rows($check)>0)
  {
    $sql="UPDATE room_device SET status='$gotResult->status' WHERE uid='$gotResult->userID' and room_id='$gotResult->roomID'";
    $check=mysqli_query($gotResult->con,$sql);
    if($check)
    {
      $gotResult->error=0;
      $gotResult->errorMessage="null";
      if($gotResult->status==1)
        $gotResult->data="Your all devices in ".$gotResult->roomName." has been turned on";
      else
        $gotResult->data="Your all devices in ".$gotResult->roomName." has been turned off";
      return $gotResult;
    }else{
      $gotResult->error=1;
      $gotResult->errorMessage="You do not have any device in ".$gotResult->roomName;
      return $gotResult;
    }
  }else{
    $gotResult->error=1;
    $gotResult->errorMessage="You do not have any device in ".$gotResult->roomName;
    return $gotResult;
  }
}
function getDataActionAll($gotResult){
  $sql="SELECT * from room_device WHERE uid='$gotResult->userID' and room_id='$gotResult->roomID'";
  $check=mysqli_query($gotResult->con,$sql);
  if($check && (mysqli_num_rows($check)>0))
  {
    $onDeviceCount=0;
    $offDeviceCount=0;
    $gotResult->error=0;
    $gotResult->errorMessage="null";
    $gotResult->data="In ".$gotResult->roomName.", ";
    while($row=mysqli_fetch_array($check)){
      if($row['status']==0)
      {
        $gotResult->data.="Your ".$row['device_name'].' is off, ';
        $offDeviceCount++;
      }else if($row['status']==1)
      {
        $gotResult->data.="Your ".$row['device_name'].' is on, ';
        $onDeviceCount++;
      }
      else{
        $gotResult->data.="Can not reach to your ".$row['device_name']." device, ";
      }
      if($onDeviceCount==mysqli_num_rows($check) && mysqli_num_rows($check)>1){
        $gotResult->data="In ".$gotResult->roomName.", all devices are on";
      }
      if($offDeviceCount==mysqli_num_rows($check) && mysqli_num_rows($check)>1){
        $gotResult->data="In ".$gotResult->roomName.", all devices are off";
      }
    }
    return $gotResult;
  }else{
    $gotResult->error=1;
    $gotResult->errorMessage="You do not have any device in ".$gotResult->roomName;
    return $gotResult;
  }
}
function changeStatus($gotResult)
{
  try{
    $gotResult=verifyData($gotResult);
    if($gotResult->error==1) return $gotResult;
    $gotResult=getUserID($gotResult);
    if($gotResult->error==1) return $gotResult;
    $gotResult=getRoomID($gotResult);
    if($gotResult->error==1) return $gotResult;
    $gotResult=performAction($gotResult);
    return $gotResult;
  }catch(Exception $e)
  {
    return $gotResult;
  }
}
function getStatus($gotResult)
{
  try{
    $gotResult=verifyData($gotResult);
    if($gotResult->error==1) return $gotResult;
    $gotResult=getUserID($gotResult);
    if($gotResult->error==1) return $gotResult;
    $gotResult=getRoomID($gotResult);
    if($gotResult->error==1) return $gotResult;
    $gotResult=getDataAction($gotResult);
    return $gotResult;
  }catch(Exception $e)
  {
    return $gotResult;
  }
}
function changeStatusAll($gotResult)
{
  try{
    $gotResult=verifyData($gotResult);
    if($gotResult->error==1) return $gotResult;
    $gotResult=getUserID($gotResult);
    if($gotResult->error==1) return $gotResult;
    $gotResult=getRoomID($gotResult);
    if($gotResult->error==1) return $gotResult;
    $gotResult=performActionAll($gotResult);
    return $gotResult;
  }catch(Exception $e)
  {
    return $gotResult;
  }
}
function getStatusAll($gotResult)
{
  try{
    $gotResult=verifyData($gotResult);
    if($gotResult->error==1) return $gotResult;
    $gotResult=getUserID($gotResult);
    if($gotResult->error==1) return $gotResult;
    $gotResult=getRoomID($gotResult);
    if($gotResult->error==1) return $gotResult;
    $gotResult=getDataActionAll($gotResult);
    return $gotResult;
  }catch(Exception $e)
  {
    return $gotResult;
  }
}
function checkDeviceValueToSet($gotResult){
  $value=$gotResult->value;
  $dvImg=$gotResult->dvImg;
  $sql="SELECT max_val FROM device WHERE image='$dvImg'";
  $result=mysqli_query($gotResult->con,$sql);
  if($result){
    $row=mysqli_fetch_array($result);
    $maxVal=$row['max_val'];
    if($maxVal<$value){
      $gotResult->error=1;
      $gotResult->errorMessage= "".$value." can't be set to ".$gotResult->deviceName.". Please specify value between 1 to ".$maxVal;
    }
    return $gotResult;
  }
  $gotResult->error=1;
  $gotResult->errorMessage="Server Error";
  return $gotResult;
}
function setDeviceValue($gotResult){
  try{
    $gotResult=verifyData($gotResult);
    if($gotResult->error==1) return $gotResult;
    $gotResult=getUserID($gotResult);
    if($gotResult->error==1) return $gotResult;
    $gotResult=getRoomID($gotResult);
    if($gotResult->error==1) return $gotResult;
    $gotResult->status=1;
    $gotResult=performAction($gotResult);
    if($gotResult->error==1) return $gotResult;
    $sql="SELECT id,device_image from room_device where uid='$gotResult->userID' and room_id='$gotResult->roomID' and device_name='$gotResult->deviceName'";
    $check=mysqli_query($gotResult->con,$sql);
    if($check && mysqli_num_rows($check)>0)
    {
      $row=mysqli_fetch_array($check);
      $did=$row['id'];
      $dvImg=$row['device_image'];
      $gotResult->dvImg=$dvImg;
      $gotResult=checkDeviceValueToSet($gotResult);
      if($gotResult->error==1) return $gotResult;
      $sql="UPDATE devicevalue SET value='$gotResult->value' WHERE did='$did'";
      $updatedRow=mysqli_query($gotResult->con,$sql);
      if($updatedRow){
        $gotResult->error=0;
        $gotResult->errorMessage="null";
        $gotResult->responseMessage="In ".$gotResult->roomName.", ".$gotResult->deviceName." is set to ".$gotResult->valueNo.".";
        return $gotResult;
      }
      $gotResult->error=1;
      $gotResult->errorMessage="Please set configuration of device or try again!";
      return $gotResult;
    }
  }catch(Exception $e)
  {
    return $gotResult;
  }
}

define('DB_HOST','localhost');
define('DB_NAME','home_automation');
define('DB_USER','root');
define('DB_PASSWORD','root');


if(isset($_REQUEST['email']) && isset($_REQUEST['deviceName']) && isset($_REQUEST['roomName']) && isset($_REQUEST['status']))
{
  $con=new mysqli(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
  if(mysqli_connect_error())
  {
    printf("Connection failed: %s\n",mysqli_connect_error());
    exit();
  }
  $email=$_REQUEST['email'];
  $deviceName=$_REQUEST['deviceName'];
  $roomName=$_REQUEST['roomName'];
  $status=$_REQUEST['status'];
  $gotResult->error=0;
  $gotResult->data="null";
  $gotResult->errorMessage="null";
  $gotResult->con=$con;
  $gotResult->email=$email;
  $gotResult->deviceName=$deviceName;
  $gotResult->roomName=$roomName;

  if($status==0 || $status==1)
  {
    $gotResult->status=$status;
    $gotResult=changeStatus($gotResult);
  }else if($status==2){
    $gotResult->status=$status;
    $gotResult=getStatus($gotResult);
  }else if($status==3 || $status==4){
    $gotResult->status=$status-3;
    $gotResult=changeStatusAll($gotResult);
  }else if($status==5){
    $gotResult=getStatusAll($gotResult);
  }else if($status==6 && isset($_REQUEST['valueNo'])){
    $valueNo=$_REQUEST['valueNo'];
    $gotResult->valueNo=$valueNo;
    $gotResult->value=$valueNo;
    $gotResult=setDeviceValue($gotResult);
  }
  else{
    $gotResult->error=1;
    $gotResult->data="null";
    $gotResult->errorMessage="Details are not correct";
  }
  $sendData = json_encode($gotResult);
  echo $sendData;
}
else{
  $gotResult->error=1;
  $gotResult->data="null";
  $gotResult->errorMessage="Details are not correct";
  $sendData = json_encode($gotResult);
  echo $sendData;
}
?>
