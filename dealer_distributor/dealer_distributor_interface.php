<?php
require_once('../config.php');
require_once('../product_data.php');
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
