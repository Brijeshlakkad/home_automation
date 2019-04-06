<?php
include_once("verify_data.php");
include_once("config.php");
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
function shouldRun($nowDate,$date,$frequency){
  $today = Date("D",$nowDate);
  $now = Date("d-m-Y",$nowDate);
  $startTime = Date("d-m-Y",$date);
  if($frequency=='0'){
    return false;
  }
  if($frequency=="ONCE"){
    if($today==$frequency){
      if($startTime==$now){
        return false;
      }
    }
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
  $gotData->errorMessage="Error in time.";
  return $gotData;
}
function setSchedule($gotData){
  $email=$gotData->email;
  $deviceName=$gotData->deviceName;
  $roomName=$gotData->roomName;
  $afterStatus=$gotData->afterStatus;
  $userID=$gotData->userID;
  $roomID=$gotData->roomID;
  $frequency=$gotData->frequency;
  $gotData->frequency=strtoupper(Date("D",strtotime($frequency)));
  $frequency=$gotData->frequency;
  $startTime=$gotData->startTime;
  $endTime=$gotData->endTime;
  $nowDate=strtotime("now");
  $date1=strtotime($startTime);
  $date2=strtotime($endTime);
  if($date1<$nowDate){
    $gotData->error=true;
    $gotData->errorMessage="Start time is not valid.";
    return $gotData;
  }
  else if($date1>$date2){
    $gotData->error=true;
    $gotData->errorMessage="End Time should not be before start time.";
    return $gotData;
  }
  else if(($date2-$date1)<60){
    $gotData->error=true;
    $gotData->errorMessage="Period should be more than 60 seconds.";
    return $gotData;
  }
  $updatedTime = Date("Y-m-d H:i:s",strtotime("now"));
  $sql="UPDATE room_device SET from_scheduled_time='$startTime', to_scheduled_time='$endTime', frequency='$frequency', after_status='$afterStatus', `date`='$updatedTime' WHERE device_name='$deviceName' AND room_id='$roomID' AND uid='$userID'";
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
  $email=$gotData->email;
  $deviceName=$gotData->deviceName;
  $roomName=$gotData->roomName;
  $userID=$gotData->userID;
  $roomID=$gotData->roomID;
  $sql="SELECT * FROM room_device WHERE device_name='$deviceName' AND room_id='$roomID' AND uid='$userID'";
  $result=mysqli_query($gotData->con,$sql);
  if($result){
    if(mysqli_num_rows($result)==1){
      $row=mysqli_fetch_array($result);
      $nowDate=strtotime("now");
      $date1=strtotime($row['from_scheduled_time']);
      $date2=strtotime($row['to_scheduled_time']);
      $gotData->scheduleInfo=(object) null;
      if(!shouldRun($nowDate,$date1,$row['frequency'])){
        $gotData->isScheduled=false;
        return $gotData;
      }
      else{
        $gotData->isScheduled=true;
        $startTime=Date("h:i:s A",$date1);
        $gotData->scheduleInfo->startTime=$startTime;
        $endTime=Date("h:i:s A",$date2);
        $gotData->scheduleInfo->deviceID=$row['id'];
        $gotData->scheduleInfo->roomName=$roomName;
        $gotData->scheduleInfo->deviceName=$deviceName;
        $gotData->scheduleInfo->endTime=$endTime;
        $gotData->scheduleInfo->createdDate=Date("d-m-Y h:i:s A",strtotime($row['date']));
        $gotData->scheduleInfo->repetition=strtoupper(Date("l",strtotime($row['frequency'])));
        $gotData->scheduleInfo->afterStatus=$row['after_status'];
        return $gotData;
      }
    }
  }
  $gotData->error=true;
  $gotData->errorMessage="You do not have device named ".$deviceName;
  return $gotData;
}
function deleteSchedulingForDevice($gotData){
  $email=$gotData->email;
  $deviceName=$gotData->deviceName;
  $roomName=$gotData->roomName;
  $userID=$gotData->userID;
  $roomID=$gotData->roomID;
  $scheduledTime=Date("Y-m-d H:i:s",strtotime("22 June 1998"));
  $updatedTime = Date("Y-m-d H:i:s",strtotime("now"));
  $sql="UPDATE room_device SET from_scheduled_time='$scheduledTime', to_scheduled_time='$scheduledTime', frequency='0', `date`='$updatedTime' WHERE device_name='$deviceName' AND room_id='$roomID' AND uid='$userID'";
  $result=mysqli_query($gotData->con,$sql);
  if($result){
    $gotData->data=$deviceName." has been off from scheduling.";
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
  $sql="SELECT room_device.id as `deviceID`, room_device.device_name as `deviceName`, room_device.from_scheduled_time as `startTime`, room_device.to_scheduled_time as `endTime`, room_device.frequency as `repetition`, room_device.after_status as `afterStatus`, `room_device`.`date` as `createdDate`, room.roomname as `roomName`
        FROM room_device LEFT JOIN room ON room.id=room_device.room_id WHERE room_device.uid='$userID'";
  $result=mysqli_query($gotData->con,$sql);
  if($result){
    if(mysqli_num_rows($result)>0){
      $i=0;
      while($row=mysqli_fetch_array($result)){
        $nowDate=strtotime(Date("Y-m-d H:i:s"));
        $date1=strtotime($row['startTime']);
        $date2=strtotime($row['endTime']);
        if(!shouldRun($nowDate,$date1,$row['repetition'])){
          continue;
        }
        $gotData->scheduledDevice[$i]=(object) null;
        $gotData->scheduledDevice[$i]->deviceID=$row['deviceID'];
        $gotData->scheduledDevice[$i]->startTime=Date("h:i:s A",$date1);
        $gotData->scheduledDevice[$i]->endTime=Date("h:i:s A",$date2);
        $gotData->scheduledDevice[$i]->createdDate=Date("d-m-Y h:i:s A",strtotime($row['createdDate']));
        $gotData->scheduledDevice[$i]->repetition=strtoupper(Date("l",strtotime($row['repetition'])));
        $gotData->scheduledDevice[$i]->deviceName=ucfirst($row['deviceName']);
        $gotData->scheduledDevice[$i]->roomName=$row['roomName'];
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
    $gotData->frequency=$_REQUEST['repetition'];
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
  }else if($action==3 && isset($_REQUEST['email']) && isset($_REQUEST['deviceName']) && isset($_REQUEST['roomName'])){
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
  }else if($action==4 && isset($_REQUEST['email'])){
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
