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
        $hwID=$rowHw['id'];
        $hwName=$rowHw['name'];
        $sql="SELECT * FROM amc WHERE serial_no_id='$hwID'";
        $resultAMC=mysqli_query($gotData->con,$sql);
        if($resultAMC && mysqli_num_rows($resultAMC)==1){
            $row=mysqli_fetch_array($resultAMC);
            $gotData->user->row[$i]=(object) null;
            $gotData->user->row[$i]->hwName=$hwName;
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
function checkMemberAlreadyExists($con,$userID,$memberID){
  $sql="SELECT * FROM allowed_user WHERE uid='$userID' AND member_id='$memberID'";
  $result=mysqli_query($con,$sql);
  if($result){
    if(mysqli_num_rows($result)==0){
      return false;
    }
  }
  return true;
}
function checkMemberEmail($con,$memberEmail){
  $sql="SELECT * FROM user WHERE email='$memberEmail'";
  $result=mysqli_query($con,$sql);
  if($result){
    if(mysqli_num_rows($result)==1){
      return true;
    }
  }
  return false;
}
function saveMemberList($gotData){
  $userID=$gotData->user->userID;
  $memberList=$gotData->user->memberList;
  $gotData->user->failedList=(object) null;
  $gotData->user->existingList=(object) null;
  $j=0;
  $k=0;
  $gotData->user->failed=(object) null;
  $gotData->user->failed->error=false;
  $gotData->user->notExists=(object) null;
  $gotData->user->notExists->error=false;
  if(count($memberList)>0){
    for($i=0;$i<count($memberList);$i++){
      if(checkMemberEmail($gotData->con,$memberList[$i])){
        $member=getUserDataUsingEmail($gotData->con,$memberList[$i]);
        if(!$member->error){
          $memberID=$member->id;
          if(!checkMemberAlreadyExists($gotData->con,$userID,$memberID)){
            $sql="INSERT INTO allowed_user(uid,member_id) VALUES('$userID','$memberID')";
            $result=mysqli_query($gotData->con,$sql);
            if(!$result){
              continue;
            }
          }
        }else{
          $gotData->user->failed->error=true;
          $gotData->user->failed->errorMessage="Some members was not able to insert";
          $gotData->user->failedList[$j]=$memberList[$i];
          $j++;
        }
      }else{
        $gotData->user->notExists->error=true;
        $gotData->user->notExists->errorMessage="Member does not exists";
        $gotData->user->notExistsList[$k]=(object) null;
        $gotData->user->notExistsList[$k]=$memberList[$i];
        $k++;
      }
    }
    if(!$gotData->user->failed->error && !$gotData->user->failed->error){
      $gotData->responseMessage="Members saved successfully";
    }
  }else{
    $gotData->error=true;
    $gotData->errorMessage="Member List is empty";
  }
  return $gotData;
}
function getMemberList($gotData){
  $userID=$gotData->user->userID;
  $sql="SELECT * FROM allowed_user WHERE uid='$userID'";
  $result=mysqli_query($gotData->con,$sql);
  if($result){
    $gotData->user->memberRows=mysqli_num_rows($result);
    if($gotData->user->memberRows>0){
      $i=0;
      while($row=mysqli_fetch_array($result)){
        $memberID=$row['member_id'];
        $member=getUserDataUsingID($gotData->con,$memberID);
        $memberList=[];
        if(!$member->error){
          $gotData->user->memberList[$i] =(object) null;
          $gotData->user->memberList[$i]->email=$member->email;
          $gotData->user->memberList[$i]->name=$member->name;
          $i+=1;
        }
      }
    }
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="Please, try again after few minutes!";
  return $gotData;
}
function removeChildUserHardware($con,$userID,$memberID){
  $gotData=(object) null;
  $gotData->error=false;
  $u=getUserDataUsingID($con,$userID);
  $userEmail=$u->email;
  $sql="SELECT product_serial.serial_no as `serial_no` FROM product_serial INNER JOIN sold_product ON product_serial.id=sold_product.serial_id WHERE sold_product.customer_email='$userEmail'";
  $result=mysqli_query($con,$sql);
  if($result){
    while($row=mysqli_fetch_array($result)){
      $hwSeries=$row['serial_no'];
      $sql="DELETE FROM hardware WHERE uid='$memberID' AND series='$hwSeries'";
      $check=mysqli_query($con,$sql);
      if($check){
      }
    }
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="Please, try again after few minutes!";
  return $gotData;
}
function removeMember($gotData){
  $userID=$gotData->user->userID;
  $memberEmail=$gotData->user->memberEmail;
  $member=getUserDataUsingEmail($gotData->con,$memberEmail);
  if(!$member->error){
    $memberID=$member->id;
    $got=removeChildUserHardware($gotData->con,$userID,$memberID);
    if($got->error) return $got;
    $sql="DELETE FROM allowed_user WHERE uid='$userID' AND member_id='$memberID'";
    $result=mysqli_query($gotData->con,$sql);
    if($result){
      $gotData->responseMessage=$memberEmail." removed successfully";
      return $gotData;
    }
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
  if($action==0 && isset($_REQUEST['userID']))
  {
    $gotData->con=$con;
    $gotData->user->userID=$_REQUEST['userID'];
    $gotData=getMemberList($gotData);
    $gotData->con=(object) null;
    echo json_encode($gotData);
    exit();
  }
  else if($action==1 && isset($_REQUEST['userID']))
  {
    $gotData->con=$con;
    $gotData->user->userID=$_REQUEST['userID'];
    $gotData=getSubscriptionDetails($gotData);
    $gotData->con=(object) null;
    echo json_encode($gotData);
    exit();
  }
  else if($action==2 && isset($_REQUEST['userID']) && isset($_REQUEST['memberModelList']))
  {
    $gotData->con=$con;
    $gotData->user->userID=$_REQUEST['userID'];
    $gotData->user->memberList=json_decode($_REQUEST['memberModelList']);
    $gotData=saveMemberList($gotData);
    $gotData->con=(object) null;
    echo json_encode($gotData);
    exit();
  }
  else if($action==3 && isset($_REQUEST['userID']) && isset($_REQUEST['memberEmail']))
  {
    $gotData->con=$con;
    $gotData->user->userID=$_REQUEST['userID'];
    $gotData->user->memberEmail=$_REQUEST['memberEmail'];
    $gotData=removeMember($gotData);
    $gotData->con=(object) null;
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
