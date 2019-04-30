<?php
class ProductSerial{ //  gets product serial data using given sql
  var $id,$serial_no,$product_id,$assigned_dealer,$assigned_distributor,$sold_product_id,$date,$error,$errorMessage;
  function getData($con,$sql){
    $check=mysqli_query($con,$sql);
    if($check && (mysqli_num_rows($check)==1))
    {
      $row=mysqli_fetch_array($check);
      $this->id=$row['id'];
      $this->serial_no=$row['serial_no'];
      $this->product_id=$row['product_id'];
      $this->assigned_dealer=$row['assigned_dealer'];
      $this->assigned_distributor=$row['assigned_distributor'];
      $this->sold_product_id=$row['sold_product_id'];
      $this->date=$row['date'];
      $this->error=false;
      $this->errorMessage="null";
    }
    else{
      $this->error=true;
      $this->errorMessage="ProductSerial doesn't exists.";
    }
  }
}
function getProductSerialDataUsingSerialNo($con,$productSerial){
  $p = new ProductSerial;
  $sql="SELECT * FROM product_serial where serial_no='$productSerial'";
  $p->getData($con,$sql);
  return $p;
}
function getProductSerialDataUsingID($con,$id){
  $p = new ProductSerial;
  $sql="SELECT * FROM product_serial where id='$id'";
  $p->getData($con,$sql);
  return $p;
}
?>
