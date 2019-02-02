<?php
include_once('../config.php');
function createProduct($gotData){
  $name=$gotData->product->name;
  $s_rate=$gotData->product->s_rate;
  $p_rate=$gotData->product->p_rate;
  $description=$gotData->product->description;
  $taxation=$gotData->product->taxation;
  $hsn_code=$gotData->product->hsn_code;
  $qty_name=$gotData->product->qty_name;
  $sql="INSERT INTO product(name,s_rate,p_rate,description,taxation,hsncode,qty_name) VALUES('$name','$s_rate','$p_rate','$description','$taxation','$hsn_code','$qty_name')";
  $check=mysqli_query($gotData->con,$sql);
  if($check){
    $gotData->product->id=mysqli_insert_id($gotData->con);
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="Try again!";
  return $gotData;
}
function checkProductName($gotData){
  $productName=$gotData->product->productName;
  $sql="SELECT * FROM product WHERE name='$productName'";
  $check=mysqli_query($gotData->con,$sql);
  if($check){
    if(mysqli_num_rows($check)==0){
      $gotData->product->productNameExists=false;
      return $gotData;
    }
    $gotData->product->productNameExists=true;
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="Try again!";
  return $gotData;
}
if(isset($_REQUEST['action'])){
  $action=$_REQUEST['action'];
  $gotData=(object) null;
  $gotData->error=false;
  $gotData->errorMessage=null;
  $gotData->product=(object) null;
  if($action==0 && isset($_REQUEST['name']) && isset($_REQUEST['s_rate']) && isset($_REQUEST['p_rate']) && isset($_REQUEST['description']) && isset($_REQUEST['taxation']) && isset($_REQUEST['hsncode']) && isset($_REQUEST['qty_name'])){
    $gotData->con=$con;
    $gotData->product->name=$_REQUEST['name'];
    $gotData->product->s_rate=$_REQUEST['s_rate'];
    $gotData->product->p_rate=$_REQUEST['p_rate'];
    $gotData->product->description=$_REQUEST['description'];
    $gotData->product->taxation=$_REQUEST['taxation'];
    $gotData->product->hsn_code=$_REQUEST['hsncode'];
    $gotData->product->qty_name=$_REQUEST['qty_name'];
    $gotData=createProduct($gotData);
    $gotData->con=(object) null;
    echo json_encode($gotData);
  }if($action==4 && isset($_REQUEST['productName'])){
    $gotData->con=$con;
    $gotData->product->productName=$_REQUEST['productName'];
    $gotData=checkProductName($gotData);
    $gotData->con=(object) null;
    echo json_encode($gotData);
  }else{
    $gotData->error=true;
    $gotData->errorMessage="Please, try again after few minutes!";
    echo json_encode($gotData);
  }
}
else{
  $gotData = (object) null;
  $gotData->error=true;
  $gotData->errorMessage="Please, try again after few minutes!";
  echo json_encode($gotData);
}
?>
