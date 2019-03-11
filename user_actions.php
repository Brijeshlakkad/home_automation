<?php
require_once("config.php");
require_once("user_data.php");
function getSubscriptionDetails($gotData){
  $userID=$gotData->user->userID;
  $sql="SELECT * FROM hardware WHERE uid='$userID'";
  $result=mysqli_query($gotData->con,$sql);
  if($result){
    $gotData->user->totalRows=mysqli_num_rows($result);
    if($gotData->user->totalRows>0){
      $i=0;
      while($rowHw=mysqli_fetch_array($result)){
        $hardwareID=$rowHw['id'];
        $sql="SELECT * FROM amc WHERE serial_no_id='$hardwareID'";
        $resultAMC=mysqli_query($gotData->con,$sql);
        if($resultAMC && mysqli_num_rows($resultAMC)==1){
            $row=mysqli_fetch_array($resultAMC);
            $gotData->user->row[$i]=(object) null;
            $gotData->user->row[$i]->seriesNo=$row['serial_no_id'];
            $date = new DateTime($row['date']);
            $gotData->user->row[$i]->date=$date->format('d-m-Y');
            $timestamp=strtotime($row['date']);
            $gotData->user->row[$i]->leftTime=date('Y-m-d', strtotime('+1 years',$timestamp));
            $date1=date_create(date('Y-m-d',time()));
            $date2=date_create($gotData->user->row[$i]->leftTime);
            $diff=date_diff($date1,$date2);
            $gotData->user->row[$i]->diff=$diff->format("%R%a");
            $gotData->user->row[$i]->state=1;
            if(((int)$diff->format("%R%a"))<0){
              $gotData->user->row[$i]->state=-1;
            }
        }
        $i++;
      }
    }
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="Please, try again after few minutes!";
  return $gotData;
}
$gotData = (object) null;
$gotData->user=(object) null;
$gotData->error=false;
$gotData->errorMessage=null;
if(isset($_REQUEST['action'])){
  $action=$_REQUEST['action'];
  if($action==1 && isset($_REQUEST['userID']))
  {
    $gotData->con=$con;
    $gotData->user->userID=$_REQUEST['userID'];
    $gotData=getSubscriptionDetails($gotData);
    echo json_encode($gotData);
    exit();
  }
  else{
      $gotData->error=true;
      $gotData->errorMessage="Please, try again after few minutes!";
      echo json_encode($gotData);
      exit();
  }
}else{
  $gotData->error=true;
  $gotData->errorMessage="Please, try again after few minutes!";
  echo json_encode($gotData);
  exit();
}
?>
