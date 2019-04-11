<?php
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
function shouldRun($startDate,$endDate,$nowDate,$repetition){
  $toDay = Date("D",strtotime($nowDate));
  if($repetition=='0'){
    return false;
  }
  else if($repetition=="ONCE"){
    $startDate2=$startDate;
    $endDate2=$endDate;
    if(strtoupper($toDay)==strtoupper($repetition)){
      if((strtotime($startDate2)<=strtotime($nowDate)) && (strtotime($endDate2)>=strtotime($nowDate)))
      {
        return true;
      }
    }
  }
  else{
    $startTime=Date("H:i",strtotime($startDate));
    $endTime=Date("H:i",strtotime($endDate));
    $nowTime=Date("H:i",strtotime($nowDate));
    $startDate2=getNowDate($startTime);
    $endDate2=getNowDate($endTime);
    if(strtotime($startDate2)>strtotime($endDate2)){
      $endDate2=getNextDay($endTime);
    }
    if($repetition=="WEEKLY"){
      if((strtotime($startDate2)<=strtotime($nowDate)) && (strtotime($endDate2)>=strtotime($nowDate)))
      {
        return true;
      }
    }
    else{
      if(strtoupper($toDay)==strtoupper($repetition)){
        if((strtotime($startDate2)<=strtotime($nowDate)) && (strtotime($endDate2)>=strtotime($nowDate)))
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
  $sql="SELECT * FROM room_device WHERE id='$deviceID'";
  $result=mysqli_query($con,$sql);
  if($result){
    $row=mysqli_fetch_array($result);
    $startDate=$row['from_scheduled_time'];
    $endDate=$row['to_scheduled_time'];
    $status=$row['status'];
    $afterStatus=$row['after_status'];
    $repetition=$row['frequency'];
    $nowDate=Date("Y-m-d H:i:s",strtotime("now"));
    $gotData->isScheduleRunning=false;
    $gotData->status=$status;
    if(shouldRun($startDate,$endDate,$nowDate,$repetition)){
      $gotData->isScheduleRunning=true;
      $gotData->afterStatus=$afterStatus;
    }
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="Device does not exists anymore";
  return $gotData;
}
// echo getDeviceStatusUsingScheduling($con,"35");
?>
