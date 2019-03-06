<?php
require_once('../config.php');
require_once('../product_data.php');
require_once('dealer_data.php');
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
function getProducts($gotData){
  $id=$gotData->d->id;
  $gotData->sql="SELECT * FROM product_serial WHERE dealer_id='$id'";
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
  $gotData->sql="SELECT * FROM product_serial WHERE dealer_id='$id' AND product_id='$productID'";
  $got=getProductSerialUsingSQL($gotData);
  if($got->error) return $got;
  $gotData->d=$got->d;
  return $gotData;
}
function checkDistributorEmail($gotData){
  $distributorEmail=$gotData->user->distributorEmail;
  $sql="SELECT * FROM dealer WHERE email='$distributorEmail'";
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
    $dealerID=$gotData->user->dealerID;
    $sql="SELECT * FROM product_serial WHERE product_id='$productID' AND dealer_id='$dealerID'";
    $check=mysqli_query($gotData->con,$sql);
    if($check){
      $gotData->user->notAssigned=mysqli_num_rows($check);
      return $gotData;
    }
    $gotData->error=true;
    $gotData->errorMessage="Try again!";
    return $gotData;
}
function assignProductSerial($gotData){
  $dealerID=$gotData->user->dealerID;
  $distributorEmail=$gotData->user->distributorEmail;
  $selectedProduct=$gotData->user->selectedProduct;
  $numProductSerials=$gotData->user->numProductSerials;
  $d=getDealerDataUsingEmail($gotData->con,$distributorEmail);
  if($d->error) return $d;
  $distributorID=$d->id;
  $gotData->user->distributorName=$d->name;
  $sql="SELECT * FROM product_serial WHERE product_id='$selectedProduct' and dealer_id='$dealerID' LIMIT $numProductSerials";
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
      $gotData->user->failed=(object) null;
      while($track<$j){
        $id=$serialIDs[$track];
        $sql="UPDATE product_serial SET dealer_id='$distributorID' where id='$id'";
        $check=mysqli_query($gotData->con,$sql);
        if(!$check){
          $gotData->user->failed->error=true;
          $gotData->user->failed->productFailed[$j] = (object) null;
          $gotData->user->failed->productFailed[$j] = $productSerial;
          $gotData->user->failed->totalRows=$j;
          $j++;
        }
        $track++;
      }
      $gotData->user->location="#!dealer_distributor/distributor";
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
  }else if($action==3 && isset($_REQUEST['productID']) && isset($_REQUEST['dealerID'])){
    $gotData->con=$con;
    $gotData->user=(object) null;
    $gotData->user->productID=$_REQUEST['productID'];
    $gotData->user->dealerID=$_REQUEST['dealerID'];
    $gotData=getDealerProductSerialCount($gotData);
    $gotData->con=(object) null;
    echo json_encode($gotData);
    exit();
  }else if($action==4 && isset($_REQUEST['distributorEmail']) && isset($_REQUEST['selectedProduct']) && isset($_REQUEST['numProductSerials']) && isset($_REQUEST['dealerID'])){
    $gotData->con=$con;
    $gotData->user=(object) null;
    $gotData->user->distributorEmail=$_REQUEST['distributorEmail'];
    $gotData->user->selectedProduct=$_REQUEST['selectedProduct'];
    $gotData->user->numProductSerials=$_REQUEST['numProductSerials'];
    $gotData->user->dealerID=$_REQUEST['dealerID'];
    $gotData=assignProductSerial($gotData);
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
