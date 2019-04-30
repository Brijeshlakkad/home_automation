<?php
require_once("hardware_data.php");
class DeviceSlider{ // device slider class to get device value
  var $id,$dvID,$value,$error,$errorMessage;
  function getData($con,$sql){
    $check=mysqli_query($con,$sql);
    if($check && (mysqli_num_rows($check)==1))
    {
      $row=mysqli_fetch_array($check);
      $this->id=$row['id'];
      $this->dvID=$row['did'];
      $this->value=$row['value'];
      $this->error=false;
      $this->errorMessage=null;
    }
    else{
      $this->error=true;
      $this->errorMessage="Device Slider does not exists.";
    }
  }
}
function getDeviceSliderDataUsingID($con,$id){
  $dvSlider = new DeviceSlider;
  $sql="SELECT * FROM devicevalue where id='$id'";
  $dvSlider->getData($con,$sql);
  return $dvSlider;
}
function getDeviceSliderDataUsingDID($con,$dvID){
  $dvSlider = new DeviceSlider;
  $sql="SELECT * FROM devicevalue where did='$dvID'";
  $dvSlider->getData($con,$sql);
  return $dvSlider;
}
?>
