<?php
include_once("verify_data.php");
include_once("config.php");
include_once("device_data.php");
date_default_timezone_set("Asia/Kolkata");
function getNextDay($endTime){
  $endTimeNext=strtotime("+1 day");
  $endDate=Date("Y-m-d",$endTimeNext)." ".Date("H:i:s",strtotime($endTime));
  $date2=strtotime($endDate);
  $endTime=Date("Y-m-d H:i:s",$date2);
  return $endTime;
}
function shouldRun($createdTime,$repetition,$runTimes){  //checks if schedule should run or not
  if($repetition=="ONCE" && $runTimes!=0){
    return false;
  }
  return true;
}
function decideMeridiem($hour){ // decides nearest meridiem if meridiem is not given
  $currentM=Date("A");
  $date = Date("h");
  if($currentM=="AM"){
    $oppositeM="PM";
  }else{
    $oppositeM="AM";
  }
  $date=(int)$date;
  $hour=(int)$hour;
  if($hour==12){
    $hour=0;
  }
  if($date>$hour){
    return $oppositeM;
  }else{
    return $currentM;
  }
  // $date1=date_create(date("Y-m-d h:i A",strtotime($hour.":00 ".$currentM)));
  // $date2=date_create(date("Y-m-d h:i A",strtotime($hour.":00 ".$oppositeM)));
  // $date=date_create(date("Y-m-d h:i A",strtotime("1:00 PM")));
  // $diff1=date_diff($date,$date1);
  // $diff2=date_diff($date,$date2);
  // echo json_encode($diff1);
  // echo "\n";
  // echo json_encode($diff2);
  // echo "\n";
  // if($diff1->h > $diff2->h){
  //   echo $currentM;
  //   return $currentM;
  // }else{
  //   echo $oppositeM;
  //   return $oppositeM;
  // }
}
function checkConflictTime($con,$deviceID,$startTime,$endTime,$repetition1){ // check if given start and end time conflicts with other schedules
  $gotData=(object) null;
  $gotData->error=false;
  $sql="SELECT * FROM schedule_device WHERE device_id='$deviceID'";
  $result=mysqli_query($con,$sql);
  if($result){
    $gotData->isConflicting=false;
    while($row=mysqli_fetch_array($result)){
      $sTime=strtotime($row['start_time']);
      $eTime=strtotime($row['end_time']);
      $startTime1 = strtotime($startTime);
      $endTime1 = strtotime($endTime);
      if($sTime>=$eTime){
        $eTime=strtotime(getNextDay($row['end_time']));
      }
      $repetition=$row['repetition'];
      if($repetition1==$repetition || $repetition=="DAILY"){
        if(($startTime1>=$sTime && $startTime1<=$eTime)||($endTime1>=$sTime && $endTime1<=$eTime)){
          $gotData->isConflicting=true;
          $gotData->conflictDate=$row['created_time'];
          return $gotData;
        }
      }
    }
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="Error in device!";
  return $gotData;
}
function setSchedule($gotData){ // set schedule for given device
  $repetitionNotDay=array('DAILY','ONCE');
  $email=$gotData->email;
  $deviceName=$gotData->deviceName;
  $roomName=$gotData->roomName;
  $afterStatus=$gotData->afterStatus;
  $userID=$gotData->userID;
  $roomID=$gotData->roomID;
  $repetition=$gotData->repetition;
  if(!in_array(strtoupper($repetition),$repetitionNotDay)){
    $gotData->repetition=strtoupper(Date("D",strtotime($repetition)));
  }
  $repetition=$gotData->repetition;
  $startTime=$gotData->startTime;
  $endTime=$gotData->endTime;
  $start=strtotime($startTime);
  $end=strtotime($endTime);
  $startTime = Date("H:i:s",$start);
  $endTime = Date("H:i:s",$end);
  $device=getDeviceDataUsingRoomID($gotData->con,$deviceName,$roomID,$userID);
  if($device->error) return $device;
  $deviceID=$device->dvID;
  $got=checkConflictTime($gotData->con,$deviceID,$startTime,$endTime,$repetition);
  if($got->error) return $got;
  if($got->isConflicting){
    $conflictDate=Date("d-m-Y H:i:s",strtotime($got->conflictDate));
    $gotData->error=true;
    $gotData->errorMessage="This schedule is conflicting with schedule created at ".$conflictDate.".";
    return $gotData;
  }
  $sql="INSERT INTO schedule_device(device_id,start_time,end_time,after_status,repetition) VALUE('$deviceID','$startTime','$endTime','$afterStatus','$repetition')";
  $result=mysqli_query($gotData->con,$sql);
  if($result){
    $gotData->data="Ok";
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="Try again!";
  return $gotData;
}
function getSchedulingForDevice($gotData){ // get scheduling list for device
  $repetitionNotDay=array('DAILY','ONCE');
  $email=$gotData->email;
  $deviceName=$gotData->deviceName;
  $roomName=$gotData->roomName;
  $userID=$gotData->userID;
  $roomID=$gotData->roomID;
  $device=getDeviceDataUsingRoomID($gotData->con,$deviceName,$roomID,$userID);
  if($device->error) return $device;
  $deviceID=$device->dvID;
  $sql="SELECT id as `scheduleID`, device_id as `deviceID`, start_time as `startTime`, end_time as `endTime`,
        after_status as `afterStatus`, repetition as `repetition`, run_times as `runTimes`,
        created_time as `createdTime`
        FROM`schedule_device` WHERE `device_id`='$deviceID'";
  $result=mysqli_query($gotData->con,$sql);
  if($result){
    $gotData->totalRows=mysqli_num_rows($result);
    if($gotData->totalRows==0){
      return $gotData;
    }else{
      $i=0;
      while($row=mysqli_fetch_array($result)){
        $nowDate=strtotime("now");
        $date1=strtotime($row['startTime']);
        $date2=strtotime($row['endTime']);
        if(!shouldRun($row['createdTime'],$row['repetition'],$row['runTimes'])){
            continue;
        }
        else{
          $gotData->scheduleInfo[$i]=(object) null;
          $startTime=Date("h:i:s A",$date1);
          $gotData->scheduleInfo[$i]->startTime=$startTime;
          $endTime=Date("h:i:s A",$date2);
          $gotData->scheduleInfo[$i]->scheduleID=$row['scheduleID'];
          $gotData->scheduleInfo[$i]->deviceID=$row['deviceID'];
          $gotData->scheduleInfo[$i]->roomName=$roomName;
          $gotData->scheduleInfo[$i]->deviceName=$deviceName;
          $gotData->scheduleInfo[$i]->endTime=$endTime;
          $gotData->scheduleInfo[$i]->runTimes=$row['runTimes'];
          $gotData->scheduleInfo[$i]->createdDate=Date("d-m-Y h:i:s A",strtotime($row['createdTime']));
          if(!in_array($row['repetition'],$repetitionNotDay)){
            $gotData->scheduleInfo[$i]->repetition=strtoupper(Date("l",strtotime($row['repetition'])));
          }else{
            $gotData->scheduleInfo[$i]->repetition=$row['repetition'];
          }
          $gotData->scheduleInfo[$i]->afterStatus=$row['afterStatus'];
          $i++;
        }
      }
      $gotData->totalRows=$i++;
      return $gotData;
    }
  }
  $gotData->error=true;
  $gotData->errorMessage="You do not have device named ".$deviceName;
  return $gotData;
}
function deleteScheduling($gotData){ // removes specific scheduling
  $email=$gotData->email;
  $scheduleID=$gotData->scheduleID;
  $sql="DELETE FROM schedule_device WHERE id='$scheduleID'";
  $result=mysqli_query($gotData->con,$sql);
  if($result){
    $gotData->data="Removed successfully";
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="You do not any devices in room ".$roomName;
  return $gotData;
}
function deleteSchedulingForDevice($gotData){ // removes all scheduling for device
  $email=$gotData->email;
  $deviceName=$gotData->deviceName;
  $roomName=$gotData->roomName;
  $userID=$gotData->userID;
  $roomID=$gotData->roomID;
  if(!hasOwnerShipUsingRoomID($gotData->con,$deviceName,$roomID,$userID)){
    $gotData->error=true;
    $gotData->errorMessage="You do not have permission to remove all scheduling at once.";
    return $gotData;
  }
  $sql="DELETE schedule_device FROM schedule_device INNER JOIN room_device ON room_device.id=schedule_device.device_id WHERE room_device.device_name='$deviceName' AND room_device.room_id='$roomID' AND room_device.uid='$userID'";
  $result=mysqli_query($gotData->con,$sql);
  if($result){
    $gotData->data=$deviceName." has been off from scheduling.";
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="You do not any devices in room ".$roomName;
  return $gotData;
}
function deleteSchedulingForRoom($gotData){ // removes all device scheduling for room
  $email=$gotData->email;
  $roomName=$gotData->roomName;
  $userID=$gotData->userID;
  $roomID=$gotData->roomID;
  $sql="DELETE schedule_device FROM schedule_device INNER JOIN room_device ON room_device.id=schedule_device.device_id WHERE room_device.room_id='$roomID' AND room_device.uid='$userID'";
  $result=mysqli_query($gotData->con,$sql);
  if($result){
    $gotData->data="In ".$roomName.", all devices have been off from scheduling.";
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="You do not any devices in room ".$roomName;
  return $gotData;
}
function hasOwnerShip($con,$hwSeries,$userID){ // checks if user owns hardware series
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
function getScheduling($gotData){ // get all scheduling details for all devices of user
  $email=$gotData->email;
  $userID=$gotData->userID;
   // GROUP BY room.name
  $roomSQL="SELECT * FROM room WHERE uid='$userID'";
  $roomResult=mysqli_query($gotData->con,$roomSQL);
  if($roomResult){
    $dvIndex=0;
    while($roomRow=mysqli_fetch_array($roomResult)){
      $roomID=$roomRow['id'];
      $roomName=$roomRow['roomname'];
      $hwSeriesList=getHardwareListUsingRoomID($gotData->con,$roomID,$userID);
      // echo "<br />Room:".$roomName."->".json_encode($hwSeriesList)."<br/>";
      for($i=0;$i<count($hwSeriesList);$i++){
        $hwSeries=$hwSeriesList[$i];
        if(hasOwnerShip($gotData->con,$hwSeries,$userID)){
          $deviceSQL="SELECT room_device.id as `dvID`, room_device.device_name as `deviceName` FROM room_device INNER JOIN hardware ON hardware.id=room_device.hw_id WHERE room_device.room_id='$roomID' AND room_device.uid='$userID' AND hardware.series='$hwSeries'";
        }
        else{
          $deviceSQL="SELECT room_device.id as `dvID`, room_device.device_name as `deviceName`
                FROM room_device
                INNER JOIN hardware ON hardware.id=room_device.hw_id
                INNER JOIN allowed_user ON allowed_user.serial_no=hardware.series
                INNER JOIN product_serial ON product_serial.serial_no=allowed_user.serial_no
                INNER JOIN sold_product ON sold_product.serial_id=product_serial.id
                WHERE allowed_user.member_id='$userID' AND allowed_user.serial_no='$hwSeries'";
        }
        $deviceResult=mysqli_query($gotData->con,$deviceSQL);
        if($deviceResult){
          while($deviceRow=mysqli_fetch_array($deviceResult)){
            $deviceID=$deviceRow['dvID'];
            $deviceName=$deviceRow['deviceName'];
            $scheduleSQL="SELECT id as `scheduleID`, device_id as `deviceID`, start_time as `startTime`, end_time as `endTime`,
                  after_status as `afterStatus`, repetition as `repetition`, run_times as `runTimes`,
                  created_time as `createdTime`
                  FROM`schedule_device` WHERE `device_id`='$deviceID'";
            $scheduleResult=mysqli_query($gotData->con,$scheduleSQL);
            if($scheduleResult){
                while($scheduleRow=mysqli_fetch_array($scheduleResult)){
                  $nowDate=strtotime(Date("Y-m-d H:i:s"));
                  $date1=strtotime($scheduleRow['startTime']);
                  $date2=strtotime($scheduleRow['endTime']);
                  if(!shouldRun($scheduleRow['createdTime'],$scheduleRow['repetition'],$scheduleRow['runTimes'])){
                    continue;
                  }
                  $gotData->scheduledDevice[$dvIndex]=(object) null;
                  $gotData->scheduledDevice[$dvIndex]->scheduleID=$scheduleRow['scheduleID'];
                  $gotData->scheduledDevice[$dvIndex]->deviceID=$scheduleRow['deviceID'];
                  $gotData->scheduledDevice[$dvIndex]->startTime=Date("h:i:s A",$date1);
                  $gotData->scheduledDevice[$dvIndex]->endTime=Date("h:i:s A",$date2);
                  $gotData->scheduledDevice[$dvIndex]->createdDate=Date("d-m-Y h:i:s A",strtotime($scheduleRow['createdTime']));
                  $gotData->scheduledDevice[$dvIndex]->repetition=strtoupper(Date("l",strtotime($scheduleRow['repetition'])));
                  $gotData->scheduledDevice[$dvIndex]->deviceName=ucfirst($deviceName);
                  $gotData->scheduledDevice[$dvIndex]->roomName=$roomName;
                  $gotData->scheduledDevice[$dvIndex]->runTimes=$scheduleRow['runTimes'];
                  $gotData->scheduledDevice[$dvIndex]->afterStatus=$scheduleRow['afterStatus'];
                  $dvIndex++;
                }
            }
          }
        }
      }
    }
    $gotData->totalRows=$dvIndex;
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="You do not have any devices scheduled";
  return $gotData;
}
$gotData=(object) null;
$gotData->error=false;
if(isset($_REQUEST['action'])){
  $action=$_REQUEST['action'];
  if($action==1 && isset($_REQUEST['deviceName']) && isset($_REQUEST['roomName']) && isset($_REQUEST['email']) && isset($_REQUEST['startTime']) && isset($_REQUEST['endTime']) && isset($_REQUEST['afterStatus']) && isset($_REQUEST['repetition'])){
    $gotData->con=$con;
    $gotData->email=$_REQUEST['email'];
    $gotData->deviceName=str_replace(' ', '', $_REQUEST['deviceName']);
    $gotData->roomName=str_replace(' ', '', $_REQUEST['roomName']);
    $gotData=verifyData($gotData);
    if($gotData->error==true){
      $gotData->con=(object) null;
      echo json_encode($gotData);
      exit();
    }
    $gotData->startTime=$_REQUEST['startTime'];
    $gotData->endTime=$_REQUEST['endTime'];
    $gotData->afterStatus=$_REQUEST['afterStatus'];
    $gotData->repetition=$_REQUEST['repetition'];
    $gotData=setSchedule($gotData);
    $gotData->con=(object) null;
    echo json_encode($gotData);
    exit();
  }else if($action==2 && isset($_REQUEST['email']) && isset($_REQUEST['deviceName']) && isset($_REQUEST['roomName'])){
      $gotData->con=$con;
      $gotData->email=$_REQUEST['email'];
      $gotData->deviceName=str_replace(' ', '', $_REQUEST['deviceName']);
      $gotData->roomName=str_replace(' ', '', $_REQUEST['roomName']);
      $gotData=verifyData($gotData);
      if($gotData->error==true){
        $gotData->con=(object) null;
        echo json_encode($gotData);
        exit();
      }
      $gotData=getSchedulingForDevice($gotData);
      $gotData->con=(object) null;
      echo json_encode($gotData);
      exit();
  }else if($action==3 && isset($_REQUEST['email'])){
      $gotData->con=$con;
      $gotData->email=$_REQUEST['email'];
      $gotData=getUserID($gotData);
      if($gotData->error==1){
        $gotData->con=(object) null;
        echo json_encode($gotData);
        exit();
      }
      $gotData=getScheduling($gotData);
      $gotData->con=(object) null;
      echo json_encode($gotData);
      exit();
  }else if($action==4 && isset($_REQUEST['email']) && isset($_REQUEST['scheduleID'])){
      $gotData->con=$con;
      $gotData->email=$_REQUEST['email'];
      $gotData->scheduleID=$_REQUEST['scheduleID'];
      $gotData=getUserID($gotData);
      if($gotData->error==1){
        $gotData->con=(object) null;
        echo json_encode($gotData);
        exit();
      }
      $gotData=deleteScheduling($gotData);
      $gotData->con=(object) null;
      echo json_encode($gotData);
      exit();
  }else if($action==5 && isset($_REQUEST['email']) && isset($_REQUEST['deviceName']) && isset($_REQUEST['roomName'])){
      $gotData->con=$con;
      $gotData->email=$_REQUEST['email'];
      $gotData->deviceName=str_replace(' ', '', $_REQUEST['deviceName']);
      $gotData->roomName=str_replace(' ', '', $_REQUEST['roomName']);
      $gotData=verifyData($gotData);
      if($gotData->error==true){
        $gotData->con=(object) null;
        echo json_encode($gotData);
        exit();
      }
      $gotData=deleteSchedulingForDevice($gotData);
      $gotData->con=(object) null;
      echo json_encode($gotData);
      exit();
  }else if($action==6 && isset($_REQUEST['email']) && isset($_REQUEST['roomName'])){
      $gotData->con=$con;
      $gotData->email=$_REQUEST['email'];
      $gotData->roomName=str_replace(' ', '', $_REQUEST['roomName']);
      $gotData=getUserID($gotData);
      if($gotData->error==1){
        $gotData->con=(object) null;
        echo json_encode($gotData);
        exit();
      }
      $gotData=getRoomID($gotData);
      if($gotData->error==1){
        $gotData->con=(object) null;
        echo json_encode($gotData);
        exit();
      }
      $gotData=deleteSchedulingForRoom($gotData);
      $gotData->con=(object) null;
      echo json_encode($gotData);
      exit();
  }else{
    $gotData->error=true;
    $gotData->errorMessage="Try again!";
    echo json_encode($gotData);
    exit();
  }
}else{
  $gotData->error=true;
  $gotData->errorMessage="Try again!";
  echo json_encode($gotData);
  exit();
}
?>
