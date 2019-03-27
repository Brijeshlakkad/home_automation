<?php
require_once('../config.php');
require_once('../product_data.php');
require_once('../user_data.php');
require_once('dealer_data.php');
require_once('../assigned_user_data.php');
require_once('../product_serial_data.php');
function getUniqueProductIDs($productSerial){
  $newArr=[];
  $k=0;
  for($i=0;$i<count($productSerial);$i++){
    if(!in_array($productSerial[$i]->productID,$newArr)){
      $newArr[$k]=$productSerial[$i]->productID;
      $k++;
    }
  }
  return $newArr;
}
function createAssignedUser($con,$serialID,$userID,$userType){
  $sql="INSERT INTO `assigned_user`(`serial_id`,`user_id`,`user_type`) VALUES('$serialID','$userID','$userType')";
  $result=mysqli_query($con,$sql);
  if($result){
    $a=getAssignedUserDataUsingUserID($con,$serialID,$userID);
    return $a->id;
  }
  return -99;
}
function createSoldProduct($con,$serialID,$soldEmail){
  $sql="INSERT INTO `sold_product`(`serial_id`,`customer_email`) VALUES('$serialID','$soldEmail')";
  $result=mysqli_query($con,$sql);
  if($result){
    $a=getSoldUserDataUsingUserID($con,$serialID,$soldEmail);
    return $a->id;
  }
  return -99;
}
function getProducts($gotData){
  $id=$gotData->d->id;
  $d=getDealerDataUsingID($gotData->con,$id);
  if($d->error) return $d;
  $userType=$d->type;
  $gotData->sql="SELECT product_serial.serial_no as serial_no, product_serial.product_id as product_id, assigned_user.user_id as dealer_id  FROM product_serial INNER JOIN assigned_user ON assigned_user.serial_id=product_serial.id WHERE assigned_user.user_id='$id'";
  $got=getProductSerialUsingSQL($gotData);
  if($got->error) return $got;
  $k=0;
  $gotData->d->productSerial=getUniqueProductIDs($gotData->d->productSerial);
  for($i=0;$i<count($gotData->d->productSerial);$i++){
    $productID=$gotData->d->productSerial[$i];
    $sql="SELECT * FROM product WHERE id='$productID'";
    $result=mysqli_query($gotData->con,$sql);
    if($result){
      $row=mysqli_fetch_array($result);
      $gotData->d->product[$k]=(object) null;
      $gotData->d->product[$k]->id=$row['id'];
      $gotData->d->product[$k]->name=$row['name'];
      $gotData->d->product[$k]->s_rate=$row['s_rate'];
      $gotData->d->product[$k]->p_rate=$row['p_rate'];
      $gotData->d->product[$k]->description=$row['description'];
      $gotData->d->product[$k]->taxation=$row['taxation'];
      $gotData->d->product[$k]->product_code=$row['product_code'];
      $gotData->d->product[$k]->hsncode=$row['hsncode'];
      $gotData->d->product[$k]->qty_name=$row['qty_name'];
      $k++;
    }
  }
  $gotData->d->productRows=$k;
  return $gotData;
}
function getProductSerialUsingSQL($gotData){
  $result=mysqli_query($gotData->con,$gotData->sql);
  if($result){
    $gotData->d->productSerialRows=mysqli_num_rows($result);
    if($gotData->d->productSerialRows>0){
      $i=0;
      while($row=mysqli_fetch_array($result)){
        $gotData->d->productSerial[$i]=(object) null;
        $gotData->d->productSerial[$i]->serialNo=$row['serial_no'];
        $gotData->d->productSerial[$i]->productID=$row['product_id'];
        $gotData->d->productSerial[$i]->dealerID=$row['dealer_id'];
        $i++;
      }
    }
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="Try again!";
  return $gotData;
}
function getProductSerialsUsingIDAndName($gotData){
  $id=$gotData->d->id;
  $productName=$gotData->d->productName;
  $p=getProductDataUsingName($gotData->con,$productName);
  if($p->error) return $p;
  $productID=$p->id;
  $d=getDealerDataUsingID($gotData->con,$id);
  if($d->error) return $d;
  $userType=$d->type;
  $gotData->sql="SELECT product_serial.serial_no as serial_no, product_serial.product_id as product_id, assigned_user.user_id as dealer_id  FROM product_serial INNER JOIN assigned_user ON assigned_user.serial_id=product_serial.id WHERE assigned_user.user_id='$id' AND product_serial.product_id='$productID'";
  $got=getProductSerialUsingSQL($gotData);
  if($got->error) return $got;
  $gotData->d=$got->d;
  return $gotData;
}
function checkDistributorEmail($gotData){
  $distributorEmail=$gotData->user->distributorEmail;
  $sql="SELECT * FROM dealer WHERE email='$distributorEmail' AND type='distributor'";
  $check=mysqli_query($gotData->con,$sql);
  if($check){
    if(mysqli_num_rows($check)==1){
      $gotData->user->distributorEmailExists=true;
      $d=getDealerDataUsingEmail($gotData->con,$distributorEmail);
      if($d->error) return $d;
      $gotData->user->distributorName=$d->name;
    }else{
      $gotData->user->distributorEmailExists=false;
    }
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="Try again!";
  return $gotData;
}
function getDealerProductSerialCount($gotData){
    $productID=$gotData->user->productID;
    $id=$gotData->user->id;
    $d=getDealerDataUsingID($gotData->con,$id);
    if($d->error) return $d;
    $userType=$d->type;
    if($userType=="dealer"){
      $sql="SELECT product_serial.id FROM product_serial INNER JOIN assigned_user ON assigned_user.serial_id=product_serial.id WHERE product_serial.product_id='$productID' AND assigned_user.user_id='$id' AND assigned_user.user_type='$userType' AND product_serial.assigned_distributor IS NULL";
    }else{
      $sql="SELECT product_serial.id FROM product_serial INNER JOIN assigned_user ON assigned_user.serial_id=product_serial.id WHERE product_serial.product_id='$productID' AND product_serial.assigned_dealer!='' AND assigned_user.user_id='$id' AND assigned_user.user_type='$userType' AND product_serial.sold_product_id IS NULL";
    }
    $check=mysqli_query($gotData->con,$sql);
    if($check){
      $gotData->user->notAssigned=mysqli_num_rows($check);
      return $gotData;
    }
    $gotData->error=true;
    $gotData->errorMessage="Try again!";
    return $gotData;
}
function checkCustomerEmail($gotData){
  $customerEmail=$gotData->user->customerEmail;
  $gotData->user->customerEmailExists=false;
  $u=getUserDataUsingEmail($gotData->con,$customerEmail);
  if($u->error==false){
    $gotData->user->customerEmailExists=true;
    $gotData->user->customer=$u;
  }
  return $gotData;
}
function sellProduct($gotData){
  $soldToEmail=$gotData->user->soldToEmail;
  $productID=$gotData->user->productID;
  $numProductSerials=$gotData->user->numProductSerials;
  $userID=$gotData->user->userID;
  $userType=$gotData->user->userType;
  if($userType=="dealer"){
    $d=getDealerDataUsingEmail($gotData->con,$soldToEmail);
    if($d->error) return $d;
    $distributorID=$d->id;
    $gotData->user->distributorName=$d->name;
    $sql="SELECT product_serial.id as `id` FROM product_serial INNER JOIN assigned_user ON product_serial.id=assigned_user.serial_id WHERE product_serial.product_id='$productID' AND assigned_user.user_id='$userID' AND assigned_user.user_type='$userType' AND product_serial.assigned_distributor IS NULL LIMIT $numProductSerials";
  }else{
    $sql="SELECT product_serial.id as `id` FROM product_serial INNER JOIN assigned_user ON product_serial.id=assigned_user.serial_id WHERE product_serial.product_id='$productID' AND assigned_user.user_id='$userID' AND assigned_user.user_type='$userType' AND product_serial.assigned_dealer!='' AND product_serial.sold_product_id IS NULL LIMIT $numProductSerials";
  }
  $check=mysqli_query($gotData->con,$sql);
  $serialIDs=[];
  if($check){
    if(mysqli_num_rows($check)==$numProductSerials){
      $j=0;
      while($row=mysqli_fetch_array($check)){
        $serialIDs[$j]=$row['id'];
        $j++;
      }
      $track=0;
      $k=0;
      $gotData->user->failed=(object) null;
      while($track<$j){
        $id=$serialIDs[$track];
        if($userType=="dealer"){
          $assignedID=createAssignedUser($gotData->con,$id,$distributorID,'distributor');
          $sql="UPDATE product_serial SET assigned_distributor='$assignedID' where id='$id'";
        }else{
          $assignedID=createSoldProduct($gotData->con,$id,$soldToEmail);
          $sql="UPDATE product_serial SET sold_product_id='$assignedID' where id='$id'";
        }
        $check=mysqli_query($gotData->con,$sql);
        if(!$check){
          $gotData->user->failed->error=true;
          $gotData->user->failed->productFailed[$k] = (object) null;
          $gotData->user->failed->productFailed[$k] = $productSerial;
          $gotData->user->failed->totalRows=$k;
          $k++;
        }
        $track++;
      }
      $assigned=$numProductSerials-$k;
      if($userType=="dealer"){
        $gotData->user->location="#!dealer_distributor/distributor";
      }else{
        $gotData->user->location="#!dealer_distributor/customer";
      }
      $gotData->responseMessage=$assigned." Product Serials selled to ".$soldToEmail;
    }else{
      $gotData->error=true;
      $gotData->errorMessage=$numProductSerials." product serials can't be assigned.";
    }
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="Try again!";
  return $gotData;
}
function getProductSoldList($gotData){
  $id=$gotData->user->id;
  $type=$gotData->user->type;
  if($type=="dealer"){
    $sql="SELECT product_serial.product_id as `product_id`, product_serial.serial_no as `serial_no`, distributor.user_id as `distributor_id` FROM product_serial INNER JOIN assigned_user as dealer ON dealer.id=product_serial.assigned_dealer INNER JOIN assigned_user as distributor ON product_serial.assigned_distributor=distributor.id WHERE dealer.user_id='$id' AND product_serial.assigned_distributor!=''";
  }else{
    $sql="SELECT product_serial.product_id as `product_id`, product_serial.serial_no as `serial_no`, sold_product.customer_email as `customer_email` FROM product_serial INNER JOIN assigned_user as distributor ON distributor.id=product_serial.assigned_distributor INNER JOIN sold_product ON product_serial.sold_product_id=sold_product.id WHERE distributor.user_id='$id' AND product_serial.assigned_distributor!=''";
  }
  $result=mysqli_query($gotData->con,$sql);
  if($result){
    $gotData->user->totalRows=mysqli_num_rows($result);
    if($gotData->user->totalRows>0){
      $i=0;
      while($row=mysqli_fetch_array($result)){
        $p=getProductDataUsingID($gotData->con,$row['product_id']);
        $gotData->user->productSold[$i]=(object) null;
        $gotData->user->productSold[$i]->productName=$p->name;
        $gotData->user->productSold[$i]->serialNo=$row['serial_no'];
        if($type=="dealer"){
          $d=getDealerDataUsingID($gotData->con,$row['distributor_id']);
          $distributorEmail=$d->email;
          $gotData->user->productSold[$i]->soldToEmail=$distributorEmail;
        }else{
          $gotData->user->productSold[$i]->soldToEmail=$row['customer_email'];
        }
        $i++;
      }
    }
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="Try again!";
  return $gotData;
}
function changeSerialCustomerEmail($gotData){
  $userID=$gotData->user->userID;
  $serialNo=$gotData->user->serialNo;
  $customerEmail=$gotData->user->customerEmail;
  $oldCustomerEmail=$gotData->user->oldCustomerEmail;
  $dis=getDealerDataUsingEmail($gotData->con,$userID);
  if($dis->error) return $dis;
  if($dis->type!='distributor'){
    $gotData->error=true;
    $gotData->errorMessage="You don't have the rigths to modify.";
    return $gotData;
  }
  $gotData=checkCustomerEmail($gotData);
  if($gotData->error) return $gotData;
  if($gotData->user->customerEmailExists){
    $sql="UPDATE product_serial SET customer_email='$customerEmail' WHERE serial_no='$serialNo' AND customer_email='$oldCustomerEmail' AND distributor_id='$userID'";
    $result=mysqli_query($gotData->con,$sql);
    if($result){
      $gotData->responseMessage="Customer email has been changed!";
      return $gotData;
    }
    $gotData->error=true;
    $gotData->errorMessage="Something went wrong!";
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage=$customerEmail." email is not exists with records";
  return $gotData;
}
function checkProductSerialExists($gotData){
  $userID=$gotData->user->userID;
  $userType=$gotData->user->userType;
  $productSerial=$gotData->user->productSerial;
  $productID=$gotData->user->productID;
  if($userType=="dealer"){
    $sql="SELECT product_serial.id FROM product_serial INNER JOIN assigned_user ON assigned_user.id=product_serial.assigned_dealer WHERE product_serial.product_id='$productID' AND assigned_user.user_id='$userID' AND product_serial.serial_no='$productSerial' AND product_serial.assigned_distributor IS NULL AND product_serial.sold_product_id IS NULL";
  }else{
    $sql="SELECT product_serial.id FROM product_serial INNER JOIN assigned_user ON assigned_user.id=product_serial.assigned_distributor WHERE product_serial.product_id='$productID' AND product_serial.assigned_dealer!='' AND assigned_user.user_id='$userID' AND product_serial.serial_no='$productSerial' AND product_serial.sold_product_id IS NULL";
  }
  $result=mysqli_query($gotData->con,$sql);
  if($result){
    $gotData->user->productSerialExists=false;
    if(mysqli_num_rows($result)==1){
      $gotData->user->productSerialExists=true;
    }
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="Try Again!";
  return $gotData;
}
function sellProductUsingSerial($gotData){
  $soldToEmail=$gotData->user->soldToEmail;
  $productID=$gotData->user->productID;
  $productSerial=$gotData->user->productSerial;
  $userID=$gotData->user->userID;
  $userType=$gotData->user->userType;
  $ps=getProductSerialDataUsingSerialNo($gotData->con,$productSerial);
  if($ps->error) return $ps;
  $serialID=$ps->id;
  if($userType=="dealer"){
    $d=getDealerDataUsingEmail($gotData->con,$soldToEmail);
    if($d->error) return $d;
    $distributorID=$d->id;
    $gotData->user->distributorName=$d->name;
    $assignedID=createAssignedUser($gotData->con,$serialID,$distributorID,'distributor');
    $sql="UPDATE product_serial SET assigned_distributor='$assignedID' WHERE product_id='$productID' AND serial_no='$productSerial' AND assigned_distributor IS NULL";
  }else{
    $assignedID=createSoldProduct($gotData->con,$serialID,$soldToEmail);
    $sql="UPDATE product_serial SET sold_product_id='$assignedID' WHERE product_id='$productID' AND serial_no='$productSerial' AND sold_product_id IS NULL";
  }
  $check=mysqli_query($gotData->con,$sql);
  if($check){
    if($userType=="dealer"){
      $gotData->user->location="#!dealer_distributor/distributor";
    }else{
      $gotData->user->location="#!dealer_distributor/customer";
    }
    $gotData->responseMessage=$productSerial." product serial selled!";
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="Try again!";
  return $gotData;
}
$gotData=(object) null;
$gotData->error=false;
$gotData->errorMessage=null;
if(isset($_REQUEST['action'])){
  $action=$_REQUEST['action'];
  if($action==0 && isset($_REQUEST['id'])){
    $gotData->con=$con;
    $gotData->d=(object) null;
    $gotData->d->id=$_REQUEST['id'];
    $gotData=getProducts($gotData);
    $gotData->con=(object) null;
    $gotData->sql=(object) null;
    echo json_encode($gotData);
    exit();
  }
  else if($action==1 && isset($_REQUEST['id']) && isset($_REQUEST['productName'])){
    $gotData->con=$con;
    $gotData->d=(object) null;
    $gotData->d->id=$_REQUEST['id'];
    $gotData->d->productName=ucfirst($_REQUEST['productName']);
    $gotData=getProductSerialsUsingIDAndName($gotData);
    $gotData->con=(object) null;
    $gotData->sql=(object) null;
    echo json_encode($gotData);
    exit();
  }else if($action==2 && isset($_REQUEST['distributorEmail'])){
    $gotData->con=$con;
    $gotData->user=(object) null;
    $gotData->user->distributorEmail=$_REQUEST['distributorEmail'];
    $gotData=checkDistributorEmail($gotData);
    $gotData->con=(object) null;
    echo json_encode($gotData);
    exit();
  }else if($action==3 && isset($_REQUEST['productID']) && isset($_REQUEST['id'])){
    $gotData->con=$con;
    $gotData->user=(object) null;
    $gotData->user->productID=$_REQUEST['productID'];
    $gotData->user->id=$_REQUEST['id'];
    $gotData=getDealerProductSerialCount($gotData);
    $gotData->con=(object) null;
    echo json_encode($gotData);
    exit();
  }else if($action==4 && isset($_REQUEST['customerEmail'])){
    $gotData->con=$con;
    $gotData->user=(object) null;
    $gotData->user->customerEmail=$_REQUEST['customerEmail'];
    $gotData=checkCustomerEmail($gotData);
    $gotData->con=(object) null;
    echo json_encode($gotData);
    exit();
  }else if($action==5 && isset($_REQUEST['soldToEmail']) && isset($_REQUEST['productID']) && isset($_REQUEST['numProductSerials']) && isset($_REQUEST['userID']) && isset($_REQUEST['userType'])){
    $gotData->con=$con;
    $gotData->user=(object) null;
    $gotData->user->soldToEmail=$_REQUEST['soldToEmail'];
    $gotData->user->productID=$_REQUEST['productID'];
    $gotData->user->numProductSerials=$_REQUEST['numProductSerials'];
    $gotData->user->userID=$_REQUEST['userID'];
    $gotData->user->userType=$_REQUEST['userType'];
    $gotData=sellProduct($gotData);
    $gotData->con=(object) null;
    echo json_encode($gotData);
    exit();
  }else if($action==8 && isset($_REQUEST['id']) && isset($_REQUEST['type'])){
    $gotData->con=$con;
    $gotData->user=(object) null;
    $gotData->user->id=$_REQUEST['id'];
    $gotData->user->type=$_REQUEST['type'];
    $gotData=getProductSoldList($gotData);
    $gotData->con=(object) null;
    echo json_encode($gotData);
    exit();
  }else if($action==9 && isset($_REQUEST['userID']) && isset($_REQUEST['serialNo']) && isset($_REQUEST['customerEmail']) && isset($_REQUEST['oldCustomerEmail'])){
    $gotData->con=$con;
    $gotData->user=(object) null;
    $gotData->user->userID=$_REQUEST['userID'];
    $gotData->user->serialNo=$_REQUEST['serialNo'];
    $gotData->user->customerEmail=$_REQUEST['customerEmail'];
    $gotData->user->oldCustomerEmail=$_REQUEST['oldCustomerEmail'];
    $gotData=changeSerialCustomerEmail($gotData);
    $gotData->con=(object) null;
    echo json_encode($gotData);
    exit();
  }else if($action==10 && isset($_REQUEST['userID']) && isset($_REQUEST['userType']) && isset($_REQUEST['productID']) && isset($_REQUEST['productSerial'])){
    $gotData->con=$con;
    $gotData->user=(object) null;
    $gotData->user->userID=$_REQUEST['userID'];
    $gotData->user->userType=$_REQUEST['userType'];
    $gotData->user->productSerial=$_REQUEST['productSerial'];
    $gotData->user->productID=$_REQUEST['productID'];
    $gotData=checkProductSerialExists($gotData);
    $gotData->con=(object) null;
    echo json_encode($gotData);
    exit();
  }else if($action==11 && isset($_REQUEST['soldToEmail']) && isset($_REQUEST['productID']) && isset($_REQUEST['productSerial']) && isset($_REQUEST['userID']) && isset($_REQUEST['userType'])){
    $gotData->con=$con;
    $gotData->user=(object) null;
    $gotData->user->soldToEmail=$_REQUEST['soldToEmail'];
    $gotData->user->productID=$_REQUEST['productID'];
    $gotData->user->productSerial=$_REQUEST['productSerial'];
    $gotData->user->userID=$_REQUEST['userID'];
    $gotData->user->userType=$_REQUEST['userType'];
    $gotData=sellProductUsingSerial($gotData);
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
