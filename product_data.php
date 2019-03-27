<?php
class Product{
  var $id,$name,$s_rate,$p_rate,$description,$taxation,$product_code,$hsncode,$qty_name,$error,$errorMessage;
  function getData($con,$sql){
    $check=mysqli_query($con,$sql);
    if($check && (mysqli_num_rows($check)==1))
    {
      $row=mysqli_fetch_array($check);
      $this->id=$row['id'];
      $this->name=$row['name'];
      $this->s_rate=$row['s_rate'];
      $this->p_rate=$row['p_rate'];
      $this->description=$row['description'];
      $this->taxation=$row['taxation'];
      $this->product_code=$row['product_code'];
      $this->hsncode=$row['hsncode'];
      $this->qty_name=$row['qty_name'];
      $this->error=false;
      $this->errorMessage="null";
    }
    else{
      $this->error=true;
      $this->errorMessage="Product Serial doesn't exists.";
    }
  }
}
function getProductDataUsingName($con,$name){
  $p = new Product;
  $sql="SELECT * FROM product where name='$name'";
  $p->getData($con,$sql);
  return $p;
}
function getProductDataUsingID($con,$id){
  $p = new Product;
  $sql="SELECT * FROM product where id='$id'";
  $p->getData($con,$sql);
  return $p;
}
?>
