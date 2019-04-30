<?php
// require_once("config.php");
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
date_default_timezone_set("Asia/Kolkata");
function getNextDay($endTime){
  $endTimeNext=strtotime("+1 day");
  $endDate=Date("Y-m-d",$endTimeNext)." ".Date("H:i:s",strtotime($endTime));
  $date2=strtotime($endDate);
  $endTime=Date("Y-m-d H:i:s",$date2);
  return $endTime;
}
function getNowDate($time){
  $date=Date("Y-m-d");
  $dateTime=Date("Y-m-d H:i:s",strtotime($date." ".$time));
  return $dateTime;
}
function shouldRun($startTime,$endTime,$nowDate,$repetition,$runTimes){
  $toDay = Date("D",strtotime($nowDate));
  if($repetition=="ONCE" && $runTimes==0){
    if((strtotime($startTime)<=strtotime($nowDate)) && (strtotime($endTime)>=strtotime($nowDate)))
    {
      return true;
    }
  }
  else{
    $startDate=Date("H:i:s",strtotime($startTime));
    $endDate=Date("H:i:s",strtotime($endTime));
    $nowTime=Date("H:i:s",strtotime($nowDate));
    if(strtotime($startDate)>=strtotime($endDate)){
      $endDate=getNextDay($endTime);
    }
    if($repetition=="DAILY"){
      if((strtotime($startDate)<=strtotime($nowDate)) && (strtotime($endDate)>=strtotime($nowDate)))
      {
        return true;
      }
    }
    else{
      if(strtoupper($toDay)==strtoupper($repetition)){
        if((strtotime($startDate)<=strtotime($nowDate)) && (strtotime($endDate)>=strtotime($nowDate)))
        {
          return true;
        }
      }
    }
  }
  // echo $startDate."BB".$startDate2."<br/>";
  // echo $endDate."BB".$endDate2."<br/>";
  // echo $nowDate."<br/>".$toDay."<br />";
  return false;
}
function getDeviceStatusUsingScheduling($con,$deviceID){
  $gotData=(object) null;
  $gotData->error=false;
  $sql="SELECT `schedule_device`.id as `scheduleID`, `schedule_device`.device_id as `deviceID`, `schedule_device`.start_time as `startTime`, `schedule_device`.end_time as `endTime`,
        `schedule_device`.after_status as `afterStatus`, `schedule_device`.repetition as `repetition`, `schedule_device`.run_times as `runTimes`,
        `schedule_device`.created_time as `createdTime`, `room_device`.status as `status`
         FROM room_device LEFT JOIN schedule_device ON room_device.id=schedule_device.device_id WHERE room_device.id='$deviceID'";
  $result=mysqli_query($con,$sql);
  if($result){
    while($row=mysqli_fetch_array($result)){
      $startTime=$row['startTime'];
      $endTime=$row['endTime'];
      $status=$row['status'];
      $afterStatus=$row['afterStatus'];
      $repetition=$row['repetition'];
      $runTimes=$row['runTimes'];
      $nowDate=Date("Y-m-d H:i:s",strtotime("now"));
      $gotData->isScheduleRunning=false;
      $gotData->status=$status;
      if(shouldRun($startTime,$endTime,$nowDate,$repetition,$runTimes)){
        $gotData->isScheduleRunning=true;
        $gotData->scheduleID=$row['scheduleID'];
        $gotData->afterStatus=$afterStatus;
        return $gotData;
      }
    }
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="Device does not exists anymore";
  return $gotData;
}
// echo json_encode(getDeviceStatusUsingScheduling($con,"34"));
?>
