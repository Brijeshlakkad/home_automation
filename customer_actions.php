<?php
require_once("config.php");
require_once("user_data.php");
function getSubscriptionDetails($gotData){  // subscription details of customer using userID
  $userID=$gotData->user->userID;
  $user=getUserDataUsingID($gotData->con,$userID);
  if($user->error) return $user;
  $email = $user->email;
  $sql="SELECT * FROM product_serial INNER JOIN amc ON amc.serial_no=product_serial.serial_no INNER JOIN sold_product ON sold_product.id=product_serial.sold_product_id WHERE sold_product.customer_email='$email'";
  $result=mysqli_query($gotData->con,$sql);
  if($result){
    $gotData->user->totalRows=mysqli_num_rows($result);
    if($gotData->user->totalRows>0){
      $i=0;
      while($row=mysqli_fetch_array($result)){
        $gotData->user->row[$i]=(object) null;
        $gotData->user->row[$i]->serialNo=$row['serial_no'];
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
        $i++;
      }
    }
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="Please, try again after few minutes!";
  return $gotData;
}
function checkMemberAlreadyExists($con,$userID,$memberID,$seriesNo){
  // check control member already exists in user's list
  $sql="SELECT * FROM allowed_user WHERE uid='$userID' AND member_id='$memberID' AND serial_no='$seriesNo'";
  $result=mysqli_query($con,$sql);
  if($result){
    if(mysqli_num_rows($result)==0){
      return false;
    }
  }
  return true;
}
function checkMemberEmail($con,$memberEmail){
  // check control member email is registered with our server
  $sql="SELECT * FROM user WHERE email='$memberEmail'";
  $result=mysqli_query($con,$sql);
  if($result){
    if(mysqli_num_rows($result)==1){
      return true;
    }
  }
  return false;
}
function getHwSeriesList($gotData){
  // customer created own hardware to show list in control member
  $userID=$gotData->user->userID;
  $hwSeries=$gotData->user->hwSeries;
  $user=getUserDataUsingID($gotData->con,$userID);
  if($user->error) return $user;
  $email=$user->email;
  $hwSeriesList=[];
  if($hwSeries==-99){
    $sql="SELECT product_serial.serial_no as 'hwSeries' FROM hardware INNER JOIN product_serial ON product_serial.serial_no=hardware.series INNER JOIN sold_product ON sold_product.serial_id=product_serial.id WHERE sold_product.customer_email='$email' AND hardware.uid='$userID'";
    $result=mysqli_query($gotData->con,$sql);
    if($result){
      $i=0;
      while($row=mysqli_fetch_array($result)){
        $hwSeriesList[$i]=$row['series'];
        $i++;
      }
      if($i==0){
        $gotData->error=true;
        $gotData->errorMessage="Hardware series are not detected";
        return $gotData;
      }
      $gotData->hwSeriesList=$hwSeriesList;
      return $gotData;
    }
  }else{
    $hwSeriesList[0]=$hwSeries;
    $gotData->hwSeriesList=$hwSeriesList;
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="Hardware series are not detected";
  return $gotData;
}
function saveMemberList($gotData){
  // save control members in user's control member list
  $userID=$gotData->user->userID;
  $memberList=$gotData->user->memberList;
  $hwSeries=$gotData->user->hwSeries;
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
          $got=getHwSeriesList($gotData);
          $hwSeriesList=$got->hwSeriesList;
          for($i=0;$i<count($hwSeriesList);$i++){
            $seriesNo=$hwSeriesList[$i];
            if(!checkMemberAlreadyExists($gotData->con,$userID,$memberID,$seriesNo)){
              $sql="INSERT INTO allowed_user(uid,member_id,serial_no) VALUES('$userID','$memberID','$seriesNo')";
              $result=mysqli_query($gotData->con,$sql);
              if(!$result){
                continue;
              }
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
  // get user's control member list
  $userID=$gotData->user->userID;
  $sql="SELECT allowed_user.member_id as `member_id`, hardware.name as `hwName` FROM allowed_user INNER JOIN hardware ON hardware.series=allowed_user.serial_no WHERE allowed_user.uid='$userID' AND hardware.uid='$userID'";
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
          $gotData->user->memberList[$i]->hwName=$row['hwName'];
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
  // remove hardware from child in heirarchy to prevent access after removing that user from control member list
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
  // remove control member
  $userID=$gotData->user->userID;
  $memberEmail=$gotData->user->memberEmail;
  $hwName=$gotData->user->hwName;
  $member=getUserDataUsingEmail($gotData->con,$memberEmail);
  if(!$member->error){
    $memberID=$member->id;
    $got=removeChildUserHardware($gotData->con,$userID,$memberID);
    if($got->error) return $got;
    $sql="DELETE allowed_user FROM allowed_user INNER JOIN hardware ON hardware.series=allowed_user.serial_no WHERE allowed_user.uid='$userID' AND allowed_user.member_id='$memberID' AND hardware.name='$hwName'";
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
function getHardwareList($gotData){
  // get hardware list of user to use in saveMemberList function
  $email=$gotData->user->email;
  $user=getUserDataUsingEmail($gotData->con,$email);
  if($user->error) return $user;
  $userID=$user->id;
  $sql="SELECT hardware.name as 'hwName', product_serial.serial_no as 'hwSeries' FROM hardware INNER JOIN product_serial ON product_serial.serial_no=hardware.series INNER JOIN sold_product ON sold_product.serial_id=product_serial.id WHERE sold_product.customer_email='$email' AND hardware.uid='$userID'";
  $result=mysqli_query($gotData->con,$sql);
  if($result){
    $i=0;
    while($row=mysqli_fetch_array($result)){
      $gotData->user->hwList[$i]=(object) null;
      $gotData->user->hwList[$i]->hwName=$row['hwName'];
      $gotData->user->hwList[$i]->hwSeries=$row['hwSeries'];
      $i++;
    }
    $gotData->user->totalRows=mysqli_num_rows($result);
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
  else if($action==2 && isset($_REQUEST['userID']) && isset($_REQUEST['memberModelList']) && isset($_REQUEST['hwSeries']))
  {
    $gotData->con=$con;
    $gotData->user->userID=$_REQUEST['userID'];
    $gotData->user->hwSeries=$_REQUEST['hwSeries'];
    $gotData->user->memberList=json_decode($_REQUEST['memberModelList']);
    $gotData=saveMemberList($gotData);
    $gotData->con=(object) null;
    echo json_encode($gotData);
    exit();
  }
  else if($action==3 && isset($_REQUEST['userID']) && isset($_REQUEST['memberEmail']) && isset($_REQUEST['hwName']))
  {
    $gotData->con=$con;
    $gotData->user->userID=$_REQUEST['userID'];
    $gotData->user->memberEmail=$_REQUEST['memberEmail'];
    $gotData->user->hwName=$_REQUEST['hwName'];
    $gotData=removeMember($gotData);
    $gotData->con=(object) null;
    echo json_encode($gotData);
    exit();
  }
  else if($action==4 && isset($_REQUEST['email']))
  {
    $gotData->con=$con;
    $gotData->user->email=$_REQUEST['email'];
    $gotData=getHardwareList($gotData);
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
