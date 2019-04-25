<?php
include_once('../config.php');
include_once('../dealer_distributor/dealer_data.php');
include_once('../assigned_user_data.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);
function getProductCode($con){
  $sql="SELECT code FROM product_code WHERE id='1'";
  $result=mysqli_query($con,$sql);
  if($result){
    $row=mysqli_fetch_array($result);
    $code=$row['code'];
    $code=rand(10,100)+(int)$code;
    $codeSQL="UPDATE product_code SET code='$code' WHERE id='1'";
    $codeResult=mysqli_query($con,$codeSQL);
    if($codeResult){
      return $code;
    }
  }
  return "DEFUALT";
}
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
  $code=getProductCode($gotData->con);
  $sql="INSERT INTO product(name,s_rate,p_rate,product_code,description,taxation,hsncode,qty_name) VALUES('$name','$s_rate','$p_rate','$code','$description','$taxation','$hsn_code','$qty_name')";
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
  $sql="SELECT product_serial.id as `id`, product_serial.product_id as `product_id`, product_serial.serial_no as `serial_no`,
        assigned_user.user_id as `dealer_id`, product_serial.date as `creationDate`,
        assigned_user.date as `assignmentDate`
        FROM product_serial LEFT JOIN assigned_user ON product_serial.id=assigned_user.serial_id
        WHERE product_serial.product_id='$productID'";
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
    $assigned=0;
    $notAssigned=0;
    while($row=mysqli_fetch_array($check)){
      $gotData->user->productSerial[$i]=(object) null;
      $gotData->user->productSerial[$i]->id=$row['id'];
      $gotData->user->productSerial[$i]->product_id=$row['product_id'];
      $gotData->user->productSerial[$i]->serial_no=$row['serial_no'];
      $gotData->user->productSerial[$i]->dealer_id=$row['dealer_id'];
      $gotData->user->productSerial[$i]->creationDate=Date('d-m-Y h:i A',strtotime($row['creationDate']));
      $gotData->user->productSerial[$i]->assignmentDate='Not Available!';
      $dealer_name="Not Assigned Yet!";
      if($row['dealer_id']!=''){
        $assigned++;
        $d=getDealerDataUsingID($gotData->con,$row['dealer_id']);
        $dealer_name=$d->name." ( ".$d->email." )";
        $gotData->user->productSerial[$i]->assignmentDate=Date('d-m-Y h:i A',strtotime($row['assignmentDate']));
      }else{
        $notAssigned++;
      }
      $gotData->user->productSerial[$i]->dealer_name=$dealer_name;
      $gotData->user->assigned=$assigned;
      $gotData->user->notAssigned=$notAssigned;
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
      $sql="INSERT INTO product_serial(product_id,serial_no) VALUES('$productID','$productSerial')";
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
function searchProductSerial($gotData){
  $sProductSerial=$gotData->user->sProductSerial;
  $sql="SELECT product_serial.id as `id`, product_serial.serial_no as `serial_no`, assigned_user.user_id as `dealer_id`, assigned_user.date as `assignmentDate` FROM product_serial INNER JOIN assigned_user ON product_serial.id=assigned_user.serial_id WHERE product_serial.serial_no='$sProductSerial'";
  $check = mysqli_query($gotData->con,$sql);
  if($check){
    $i=0;
    $gotData->user->totalRows=mysqli_num_rows($check);
    if($gotData->user->totalRows==1){
      $row=mysqli_fetch_array($check);
      $serial_no=$row['serial_no'];
      $dealer_name="Not Assigned Yet!";
      if($row['dealer_id']!=''){
        $dealer=getDealerDataUsingID($gotData->con,$row['dealer_id']);
      }
      $dealer_name=$dealer->name;
      $gotData->user->searchProductSerial="<div class='data-table-list' style='margin:20px;'>
        <div class='basic-tb-hd'>
          <h2>Matched Product Serials</h2>
        </div>
      <table id='data-table-basic' class='table table-striped'>
        <thead>
          <tr>
            <th>Product Serial number</th>
            <th>Dealer</th>
            <th>Dealer Email ID</th>
            <th>Assignment Date</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>
              ".$serial_no."
            </td>
            <td>".$dealer_name."</td>
            <td>
            ".$dealer->email."
            </td>
            <td>
            ".$row['assignmentDate']."
            </td>
          </tr>
        </tbody>
      </table>
      </div>";
      return $gotData;
    }
    $gotData->user->searchProductSerial='<div class="row" align="center" style="margin-top: 80px;">
              <div id="no_found"><img src="images/not-found.png" width="100px" alt="no found" /></div>
              <br/>
              <div style="color:gray;"><h4>Not Found!</h4></div>
              </div>';
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="Try again!";
  return $gotData;
}
function getProductSerialCount($gotData){
    $productID=$gotData->user->productID;
    $sql="SELECT * FROM product_serial WHERE product_id='$productID'";
    $check=mysqli_query($gotData->con,$sql);
    if($check){
      $notAssigned=0;
      $assigned=0;
      while($row=mysqli_fetch_array($check)){
        if($row['assigned_dealer']!=''){
          $assigned++;
        }else{
          $notAssigned++;
        }
      }
      $gotData->user->totalRows=mysqli_num_rows($check);
      $gotData->user->assigned=$assigned;
      $gotData->user->notAssigned=$notAssigned;
      return $gotData;
    }
    $gotData->error=true;
    $gotData->errorMessage="Try again!";
    return $gotData;
}
function checkDealerEmail($gotData){
  $dealerEmail=$gotData->user->dealerEmail;
  $sql="SELECT * FROM dealer WHERE email='$dealerEmail' and type='dealer'";
  $check=mysqli_query($gotData->con,$sql);
  if($check){
    if(mysqli_num_rows($check)){
      $gotData->user->dealerEmailExists=true;
      $d=getDealerDataUsingEmail($gotData->con,$dealerEmail);
      if($d->error) return $d;
      $gotData->user->dealerName=$d->name;
    }else{
      $gotData->user->dealerEmailExists=false;
    }
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="Try again!";
  return $gotData;
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
function assignProductSerial($gotData){
  $dealerEmail=$gotData->user->dealerEmail;
  $selectedProduct=$gotData->user->selectedProduct;
  $numProductSerials=$gotData->user->numProductSerials;
  $d=getDealerDataUsingEmail($gotData->con,$dealerEmail);
  if($d->error) return $d;
  $dealer_id=$d->id;
  $dealer_type=$d->type;
  $gotData->user->dealerName=$d->name;
  $sql="SELECT * FROM product_serial WHERE product_id='$selectedProduct' AND assigned_dealer IS NULL LIMIT $numProductSerials";
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
        $assignedID=createAssignedUser($gotData->con,$id,$dealer_id,$dealer_type);
        $sql="UPDATE product_serial SET assigned_dealer='$assignedID' where id='$id'";
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
      $gotData->user->location="#!admin/dealer";
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
    $gotData->product->productSerialArray=$productSerialArray;
    $gotData=addProductSerials($gotData);
    $gotData->con=(object) null;
    echo json_encode($gotData);
    exit();
  }else if($action==9 && isset($_REQUEST['sProductSerial'])){
    $gotData->con=$con;
    $gotData->user=(object) null;
    $gotData->user->sProductSerial=$_REQUEST['sProductSerial'];
    $gotData=searchProductSerial($gotData);
    $gotData->con=(object) null;
    echo json_encode($gotData);
    exit();
  }else if($action==10 && isset($_REQUEST['productID'])){
    $gotData->con=$con;
    $gotData->user=(object) null;
    $gotData->user->productID=$_REQUEST['productID'];
    $gotData=getProductSerialCount($gotData);
    $gotData->con=(object) null;
    echo json_encode($gotData);
    exit();
  }else if($action==11 && isset($_REQUEST['dealerEmail'])){
    $gotData->con=$con;
    $gotData->user=(object) null;
    $gotData->user->dealerEmail=$_REQUEST['dealerEmail'];
    $gotData=checkDealerEmail($gotData);
    $gotData->con=(object) null;
    echo json_encode($gotData);
    exit();
  }else if($action==12 && isset($_REQUEST['dealerEmail']) && isset($_REQUEST['selectedProduct']) && isset($_REQUEST['numProductSerials'])){
    $gotData->con=$con;
    $gotData->user=(object) null;
    $gotData->user->dealerEmail=$_REQUEST['dealerEmail'];
    $gotData->user->selectedProduct=$_REQUEST['selectedProduct'];
    $gotData->user->numProductSerials=$_REQUEST['numProductSerials'];
    $gotData=assignProductSerial($gotData);
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
