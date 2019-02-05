<?php
include_once('../config.php');
function getAllProducts($gotData){
  $sql="SELECT * FROM product";
  $check=mysqli_query($gotData->con,$sql);
  $gotData->user=(object) null;
  if($check){
    $i=0;
    $gotData->user->totalRows=mysqli_num_rows($check);
    if($gotData->user->totalRows==0){
      $gotData->user->showNothing='<div class="row" align="center" style="margin-top: 80px;">
              	<div id="no_found"><img src="images/not-found.png" width="100px" alt="no found" /></div>
              	<br/>
              	<div style="color:gray;"><h4>No Products</h4></div>
              	</div>';
      return $gotData;
    }
    while($row=mysqli_fetch_array($check)){
      $gotData->user->product[$i]=(object) null;
      $gotData->user->product[$i]->id=$row['id'];
      $gotData->user->product[$i]->name=$row['name'];
      $gotData->user->product[$i]->s_rate=$row['s_rate'];
      $gotData->user->product[$i]->p_rate=$row['p_rate'];
      $gotData->user->product[$i]->description=$row['description'];
      $gotData->user->product[$i]->taxation=$row['taxation'];
      $gotData->user->product[$i]->product_code=$row['product_code'];
      $gotData->user->product[$i]->hsncode=$row['hsncode'];
      $gotData->user->product[$i]->qty_name=$row['qty_name'];
      $i++;
    }
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="Try again!";
  return $gotData;
}
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
    $gotData->product->location="#!admin/create_product";
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="Try again!";
  return $gotData;
}
function editProduct($gotData){
  $id=$gotData->product->id;
  $name=$gotData->product->name;
  $s_rate=$gotData->product->s_rate;
  $p_rate=$gotData->product->p_rate;
  $description=$gotData->product->description;
  $taxation=$gotData->product->taxation;
  $hsn_code=$gotData->product->hsn_code;
  $qty_name=$gotData->product->qty_name;
  $sql="UPDATE product SET name='$name',s_rate='$s_rate',p_rate='$p_rate',description='$description',taxation='$taxation',hsncode='$hsn_code',qty_name='$qty_name' WHERE id='$id'";
  $check=mysqli_query($gotData->con,$sql);
  if($check && (mysqli_affected_rows($gotData->con)==1)){
    $gotData->product->location="#!admin/product/".$name."";
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
function getProduct($gotData){
  $productName=$gotData->product->productName;
  $sql="SELECT * FROM product where name='$productName'";
  $check=mysqli_query($gotData->con,$sql);
  if($check){
    $i=0;
    $gotData->totalRows=mysqli_num_rows($check);
    if($gotData->totalRows!=1){
      $gotData->showNothing='<div class="row" align="center" style="margin-top: 80px;">
              	<div id="no_found"><img src="images/not-found.png" width="100px" alt="no found" /></div>
              	<br/>
              	<div style="color:gray;"><h4>No Products</h4></div>
              	</div>';
      return $gotData;
    }
    $row=mysqli_fetch_array($check);
    $gotData->product=(object) null;
    $gotData->product->id=$row['id'];
    $gotData->product->name=$row['name'];
    $gotData->product->s_rate=$row['s_rate'];
    $gotData->product->p_rate=$row['p_rate'];
    $gotData->product->description=$row['description'];
    $gotData->product->taxation=$row['taxation'];
    $gotData->product->product_code=$row['product_code'];
    $gotData->product->hsncode=$row['hsncode'];
    $gotData->product->qty_name=$row['qty_name'];
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="Try again!";
  return $gotData;
}
function deleteProduct($gotData){
  $productName=$gotData->product->productName;
  $sql="DELETE FROM product WHERE name='$productName'";
  $check=mysqli_query($gotData->con,$sql);
  if($check){
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="Try again!";
  return $gotData;
}
function getAllProductSerials($gotData){
  $productID=$gotData->user->productID;
  $sql="SELECT * FROM product_serial WHERE product_id='$productID'";
  $check=mysqli_query($gotData->con,$sql);
  if($check){
    $i=0;
    $gotData->user->totalRows=mysqli_num_rows($check);
    if($gotData->user->totalRows==0){
      $gotData->user->showNothing='<div class="row" align="center" style="margin-top: 80px;">
              	<div id="no_found"><img src="images/not-found.png" width="100px" alt="no found" /></div>
              	<br/>
              	<div style="color:gray;"><h4>Not Found!</h4></div>
              	</div>';
      return $gotData;
    }
    while($row=mysqli_fetch_array($check)){
      $gotData->user->productSerial[$i]=(object) null;
      $gotData->user->productSerial[$i]->id=$row['id'];
      $gotData->user->productSerial[$i]->product_id=$row['product_id'];
      $gotData->user->productSerial[$i]->serial_no=$row['serial_no'];
      $gotData->user->productSerial[$i]->dealer_id=$row['dealer_id'];
      $i++;
    }
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="Try again!";
  return $gotData;
}
function checkProductSerial($gotData){
  $productSerial=$gotData->product->productSerial;
  $sql="SELECT * FROM product_serial WHERE serial_no='$productSerial'";
  $check=mysqli_query($gotData->con,$sql);
  if($check){
    $gotData->error=false;
    if(mysqli_num_rows($check)==0){
      $gotData->product->productSerialExists=false;
      return $gotData;
    }
    $gotData->product->productSerialExists=true;
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="Try again!";
  return $gotData;
}
function addProductSerials($gotData){
  $productSerialArray=$gotData->product->productSerialArray;
  $productID=$gotData->product->productID;
  $len = count($productSerialArray);
  $j=0;
  $k=0;
  $gotData->product->failed=(object) null;
  $gotData->product->failed->error = false;
  $gotData->product->exists=(object) null;
  $gotData->product->exists->error = false;
  $got=(object) null;
  $got->con=$gotData->con;
  $got->product=(object) null;
  for($i=0;$i<$len;$i++){
    $productSerial=$productSerialArray[$i];
    $got->product->productSerial=$productSerial;
    $got=checkProductSerial($got);
    if($got->error) return $got;
    if(!$got->product->productSerialExists){
      $sql="INSERT INTO product_serial(product_id,serial_no,dealer_id) VALUES('$productID','$productSerial','0')";
      $check=mysqli_query($gotData->con,$sql);
      if(!$check){
        $gotData->product->failed->error=true;
        $gotData->product->failed->productFailed[$j] = (object) null;
        $gotData->product->failed->productFailed[$j] = $productSerial;
        $gotData->product->failed->totalRows=$j;
        $j++;
      }
    }
    else{
      $gotData->product->exists->error=true;
      $gotData->product->exists->productFailed[$k] = (object) null;
      $gotData->product->exists->productFailed[$k] = $productSerial;
      $gotData->product->exists->totalRows=$k;
      $k++;
    }
  }
  if($gotData->product->failed->error){
    $gotData->error=true;
    $gotData->errorMessage="Try again!";
    return $gotData;
  }
  else{
    return $gotData;
  }
}
if(isset($_REQUEST['action'])){
  $action=$_REQUEST['action'];
  $gotData=(object) null;
  $gotData->error=false;
  $gotData->errorMessage=null;
  if($action==0){
    $gotData->con=$con;
    $gotData=getAllProducts($gotData);
    $gotData->con=(object) null;
    echo json_encode($gotData);
    exit();
  }else if($action==1 && isset($_REQUEST['name']) && isset($_REQUEST['s_rate']) && isset($_REQUEST['p_rate']) && isset($_REQUEST['description']) && isset($_REQUEST['taxation']) && isset($_REQUEST['hsncode']) && isset($_REQUEST['qty_name'])){
    $gotData->con=$con;
    $gotData->product=(object) null;
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
    exit();
  }else if($action==2 && isset($_REQUEST['id']) && isset($_REQUEST['name']) && isset($_REQUEST['s_rate']) && isset($_REQUEST['p_rate']) && isset($_REQUEST['description']) && isset($_REQUEST['taxation']) && isset($_REQUEST['hsncode']) && isset($_REQUEST['qty_name'])){
    $gotData->con=$con;
    $gotData->product=(object) null;
    $gotData->product->id=$_REQUEST['id'];
    $gotData->product->name=$_REQUEST['name'];
    $gotData->product->s_rate=$_REQUEST['s_rate'];
    $gotData->product->p_rate=$_REQUEST['p_rate'];
    $gotData->product->description=$_REQUEST['description'];
    $gotData->product->taxation=$_REQUEST['taxation'];
    $gotData->product->hsn_code=$_REQUEST['hsncode'];
    $gotData->product->qty_name=$_REQUEST['qty_name'];
    $gotData=editProduct($gotData);
    $gotData->con=(object) null;
    echo json_encode($gotData);
    exit();
  }else if($action==3 && isset($_REQUEST['productName'])){
    $gotData->con=$con;
    $gotData->product=(object) null;
    $gotData->product->productName=$_REQUEST['productName'];
    $gotData=deleteProduct($gotData);
    $gotData->con=(object) null;
    echo json_encode($gotData);
    exit();
  }else if($action==4 && isset($_REQUEST['productName'])){
    $gotData->con=$con;
    $gotData->product=(object) null;
    $gotData->product->productName=$_REQUEST['productName'];
    $gotData=checkProductName($gotData);
    $gotData->con=(object) null;
    echo json_encode($gotData);
    exit();
  }else if($action==5 && isset($_REQUEST['productName'])){
    $gotData->con=$con;
    $gotData->product=(object) null;
    $gotData->product->productName=$_REQUEST['productName'];
    $gotData=getProduct($gotData);
    $gotData->con=(object) null;
    echo json_encode($gotData);
    exit();
  }else if($action==6 && isset($_REQUEST['productID'])){
    $gotData->con=$con;
    $gotData->user=(object) null;
    $gotData->user->productID=$_REQUEST['productID'];
    $gotData=getAllProductSerials($gotData);
    $gotData->con=(object) null;
    echo json_encode($gotData);
    exit();
  }else if($action==7 && isset($_REQUEST['productSerial'])){
    $gotData->con=$con;
    $gotData->product=(object) null;
    $gotData->product->productSerial=$_REQUEST['productSerial'];
    $gotData=checkProductSerial($gotData);
    $gotData->con=(object) null;
    echo json_encode($gotData);
    exit();
  }else if($action==8 && isset($_REQUEST['productID']) && isset($_REQUEST['productSerialArray'])){
    $gotData->con=$con;
    $gotData->product=(object) null;
    $gotData->product->productID=$_REQUEST['productID'];
    $productSerialArray=json_decode($_REQUEST['productSerialArray']);
    // echo $_REQUEST['productSerialArray']."Ss";
    // echo $productSerialArray->array."a";
    $gotData->product->productSerialArray=$productSerialArray;
    $gotData=addProductSerials($gotData);
    $gotData->con=(object) null;
    echo json_encode($gotData);
    exit();
  }else{
    $gotData->error=true;
    $gotData->errorMessage="Please, try again after few minutes!";
    echo json_encode($gotData);
    exit();
  }
}
else{
  $gotData = (object) null;
  $gotData->error=true;
  $gotData->errorMessage="Please, try again after few minutes!";
  echo json_encode($gotData);
  exit();
}
?>
