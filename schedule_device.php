<?php
include_once("verify_data.php");
include_once("config.php");
include_once("device_data.php");
date_default_timezone_set("Asia/Kolkata");
function checkFrequencyExists($con,$userID){
  $sql="SELECT * FROM frequency WHERE uid='$userID'";
  $result=mysqli_query($con,$sql);
  if($result){
    if(mysqli_num_rows($result)==1){
      return true;
    }
  }
  return false;
}
function getNextDay($endTime){
  $endTimeNext=strtotime("+1 day");
  $endDate=Date("Y-m-d",$endTimeNext)." ".Date("H:i:s",strtotime($endTime));
  $date2=strtotime($endDate);
  $endTime=Date("Y-m-d H:i:s",$date2);
  return $endTime;
}
function processFrequency($gotData){
  $frequency=$gotData->frequency;
  $frequencyArray=["weekly","all days","all","whole week","week"];
  if(in_array(strtolower($frequency),$frequencyArray)){
    $gotData->frequency="WEEKELY";
  }else{
    $day=Date("D",strtotime($frequency));
    $gotData->frequency=strtoupper($day);
  }
  return $gotData;
}
function shouldRun($createdTime,$repetition,$runTimes){
  if($repetition=="ONCE" && $runTimes!=0){
    return false;
  }
  return true;
}
function setFrequency($gotData){
  $gotData=processFrequency($gotData);
  if($gotData->error) return $gotData;
  $userID=$gotData->userID;
  $frequency=$gotData->frequency;
  if(checkFrequencyExists($gotData->con,$userID)){
    $sql="UPDATE frequency SET value='$frequency' WHERE uid='$userID'";
  }else{
    $sql="INSERT INTO frequency(value,uid) VALUES ('$frequency', '$userID')";
  }
  $result=mysqli_query($gotData->con,$sql);
  if($result){
    $gotData->data="Frequency set successfully.";
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="Error in setting frequency.";
  return $gotData;
}
function getFrequency($gotData){
  $userID=$gotData->userID;
  $con=$gotData->con;
  $sql="SELECT * FROM frequency WHERE uid='$userID'";
  $result=mysqli_query($con,$sql);
  if($result){
    if(checkFrequencyExists($con,$userID)){
      $row=mysqli_fetch_array($result);
      $frequency=$row['value'];
      $gotData->frequency=Date("l",strtotime($frequency));
      return $gotData;
    }
    $gotData->frequency = 0;
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="Error in setting frequency.";
  return $gotData;
}
function decideMeridiem($hour){
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
function processScheduledTime($scheduledTime){
  $gotData = (object) null;
  $gotData->error=0;
  $scheduledTime=str_replace("and"," ",$scheduledTime);
  $endArray1=["hours","hour","minutes","minute","seconds","second"];
  $end=null;
  for($i=0;$i<count($endArray1);$i++){
    if(substr($scheduledTime, -strlen($endArray1[$i])) === $endArray1[$i]){
      $end=$endArray1[$i];
      break;
    }
  }
  if($end!=null){
    $gotData->scheduledTime=Date("Y-m-d H:i:s",strtotime("+".$scheduledTime));
    return $gotData;
  }else{
    $endArray2=["AM","PM","a.m.","p.m.","am","pm"];
    $end=null;
    for($i=0;$i<count($endArray2);$i++){
      if(substr($scheduledTime, -strlen($endArray2[$i])) === $endArray2[$i]){
        $end=$endArray2[$i];
        break;
      }
    }
    if($end==null){
      $time=$scheduledTime;
      $minute=(string)(int)$time%100;
      $hour=(int)((int)$time/100);
      if($minute==0){
        $minute="00";
      }
      if($hour>12){
        $end="";
      }else{
        $end=decideMeridiem($hour);
      }
      $hour=(string)$hour;
    }
    else{
      $time=substr($scheduledTime,0,strpos($scheduledTime,$end));
      $minute=(string)(int)$time%100;
      $hour=(int)((int)$time/100);
      if($minute==0){
        $minute="00";
      }
      if($hour>12){
        $end="";
      }
      $hour=(string)$hour;
    }
    $gotData->scheduledTime=Date("Y-m-d H:i:s",strtotime($hour.":".$minute."".$end));
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="Error in time!";
  return $gotData;
}
function checkConflictTime($con,$deviceID,$startTime,$endTime,$repetition1){
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
function setSchedule($gotData){
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
function getSchedulingForDevice($gotData){
  $repetitionNotDay=array('DAILY','ONCE');
  $email=$gotData->email;
  $deviceName=$gotData->deviceName;
  $roomName=$gotData->roomName;
  $userID=$gotData->userID;
  $roomID=$gotData->roomID;
  $sql="SELECT `schedule_device`.id as `scheduleID`, `schedule_device`.device_id as `deviceID`, `schedule_device`.start_time as `startTime`, `schedule_device`.end_time as `endTime`,
        `schedule_device`.after_status as `afterStatus`, `schedule_device`.repetition as `repetition`, `schedule_device`.run_times as `runTimes`,
        `schedule_device`.created_time as `createdTime`
        FROM `room_device` INNER JOIN `schedule_device` ON room_device.id=schedule_device.device_id WHERE `room_device`.`device_name`='$deviceName' AND `room_device`.`room_id`='$roomID' AND `room_device`.`uid`='$userID'";
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
function deleteScheduling($gotData){
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
function deleteSchedulingForDevice($gotData){
  $email=$gotData->email;
  $deviceName=$gotData->deviceName;
  $roomName=$gotData->roomName;
  $userID=$gotData->userID;
  $roomID=$gotData->roomID;
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
function deleteSchedulingForRoom($gotData){
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
function getScheduling($gotData){
  $email=$gotData->email;
  $userID=$gotData->userID;
   // GROUP BY room.name
  $sql="SELECT `schedule_device`.id as `scheduleID`, `schedule_device`.device_id as `deviceID`, `schedule_device`.start_time as `startTime`, `schedule_device`.end_time as `endTime`,
        `schedule_device`.after_status as `afterStatus`, `schedule_device`.repetition as `repetition`, `schedule_device`.run_times as `runTimes`,
        `schedule_device`.created_time as `createdTime`, room.roomname as`roomName`, room_device.device_name as `deviceName`
        FROM room_device INNER JOIN `schedule_device` ON room_device.id=schedule_device.device_id LEFT JOIN room ON room.id=room_device.room_id WHERE room_device.uid='$userID'";
  $result=mysqli_query($gotData->con,$sql);
  if($result){
    if(mysqli_num_rows($result)>0){
      $i=0;
      while($row=mysqli_fetch_array($result)){
        $nowDate=strtotime(Date("Y-m-d H:i:s"));
        $date1=strtotime($row['startTime']);
        $date2=strtotime($row['endTime']);
        if(!shouldRun($row['createdTime'],$row['repetition'],$row['runTimes'])){
          continue;
        }
        $gotData->scheduledDevice[$i]=(object) null;
        $gotData->scheduledDevice[$i]->scheduleID=$row['scheduleID'];
        $gotData->scheduledDevice[$i]->deviceID=$row['deviceID'];
        $gotData->scheduledDevice[$i]->startTime=Date("h:i:s A",$date1);
        $gotData->scheduledDevice[$i]->endTime=Date("h:i:s A",$date2);
        $gotData->scheduledDevice[$i]->createdDate=Date("d-m-Y h:i:s A",strtotime($row['createdTime']));
        $gotData->scheduledDevice[$i]->repetition=strtoupper(Date("l",strtotime($row['repetition'])));
        $gotData->scheduledDevice[$i]->deviceName=ucfirst($row['deviceName']);
        $gotData->scheduledDevice[$i]->roomName=$row['roomName'];
        $gotData->scheduledDevice[$i]->runTimes=$row['runTimes'];
        $gotData->scheduledDevice[$i]->afterStatus=$row['afterStatus'];
        $i++;
      }
      $gotData->totalRows=$i;
      return $gotData;
    }
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
