<?php
class AssignedUser{  // dealer and distributor assigned to product serial
  var $id,$serial_id,$user_id,$user_type,$date,$error,$errorMessage;
  function getData($con,$sql){
    $check=mysqli_query($con,$sql);
    if($check && (mysqli_num_rows($check)==1))
    {
      $row=mysqli_fetch_array($check);
      $this->id=$row['id'];
      $this->serial_id=$row['serial_id'];
      $this->user_id=$row['user_id'];
      $this->user_type=$row['user_type'];
      $this->date=$row['date'];
      $this->error=false;
      $this->errorMessage="null";
    }
    else{
      $this->error=true;
      $this->errorMessage="Assigned user doesn't exists.";
    }
  }
}
class SoldProduct{  // customer id who has bought the product serial
  var $id,$serial_id,$customer_email,$date,$error,$errorMessage;
  function getData($con,$sql){
    $check=mysqli_query($con,$sql);
    if($check && (mysqli_num_rows($check)==1))
    {
      $row=mysqli_fetch_array($check);
      $this->id=$row['id'];
      $this->serial_id=$row['serial_id'];
      $this->customer_email=$row['customer_email'];
      $this->date=$row['date'];
      $this->error=false;
      $this->errorMessage="null";
    }
    else{
      $this->error=true;
      $this->errorMessage="Assigned user doesn't exists.";
    }
  }
}
function getAssignedUserDataUsingUserID($con,$serialID,$userID){
  $a = new AssignedUser;
  $sql="SELECT * FROM assigned_user where serial_id='$serialID' AND user_id='$userID'";
  $a->getData($con,$sql);
  return $a;
}
function getAssignedUserDataUsingID($con,$id){
  $a = new AssignedUser;
  $sql="SELECT * FROM assigned_user where id='$id'";
  $a->getData($con,$sql);
  return $a;
}
function getSoldUserDataUsingUserID($con,$serialID,$soldEmail){
  $sold = new SoldProduct;
  $sql="SELECT * FROM sold_product where serial_id='$serialID' AND customer_email='$soldEmail'";
  $sold->getData($con,$sql);
  return $sold;
}
function getSoldUserDataUsingID($con,$id){
  $sold = new SoldProduct;
  $sql="SELECT * FROM sold_product where id='$id'";
  $sold->getData($con,$sql);
  return $sold;
}
?>
