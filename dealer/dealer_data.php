<?php
class Dealer{
  var $id,$email,$name,$password,$contact,$city,$address,$code,$type,$error,$errorMessage;
  function getData($con,$sql){
    $check=mysqli_query($con,$sql);
    if($check && (mysqli_num_rows($check)==1))
    {
      $row=mysqli_fetch_array($check);
      $this->id=$row['id'];
      $this->name=$row['name'];
      $this->email=$row['email'];
      $this->password=$row['password'];
      $this->address=$row['address'];
      $this->city=$row['city'];
      $this->contact=$row['mobile'];
      $this->code=$row['code'];
      $this->type=$row['type'];
      $this->error=false;
      $this->errorMessage="null";
    }
    else{
      $this->error=true;
      $this->errorMessage="You do not have account in our app. Please register yourself at OUR app";
    }
  }
}
function getDealerDataUsingEmail($con,$email){
  $d = new Dealer;
  $sql="SELECT * FROM dealer where email='$email'";
  $d->getData($con,$sql);
  return $d;
}
function getDealerDataUsingID($con,$id){
  $d = new Dealer;
  $sql="SELECT * FROM dealer where id='$id'";
  $d->getData($con,$sql);
  return $d;
}
?>
