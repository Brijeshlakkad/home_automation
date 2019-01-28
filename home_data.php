<?php
class Home{
  var $homeID,$userID,$homeName,$error,$errorMessage;
  function getData($con,$sql){
    $check=mysqli_query($con,$sql);
    if($check && (mysqli_num_rows($check)==1))
    {
      $row=mysqli_fetch_array($check);
      $this->homeID=$row['id'];
      $this->userID=$row['uid'];
      $this->homeName=$row['homename'];
      $this->error=false;
      $this->errorMessage="null";
    }
    else{
      $this->error=true;
      $this->errorMessage="Home does not exists.";
    }
  }
}
function getHomeDataUsingID($con,$homeID){
  $h = new Home;
  $sql="SELECT * FROM home where id='$homeID'";
  $h->getData($con,$sql);
  return $h;
}
function getHomeDataUsingName($con,$homeName){
  $h = new Home;
  $sql="SELECT * FROM home where homename='$homeName'";
  $h->getData($con,$sql);
  return $h;
}
?>
