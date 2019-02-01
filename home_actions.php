<?php
require_once("config.php");
function getUserID($gotData)
{
  $email=$gotData->user->email;
  $sql="SELECT * FROM user where email='$email'";
  $check=mysqli_query($gotData->con,$sql);
  if($check && (mysqli_num_rows($check)==1))
  {
    $row=mysqli_fetch_array($check);
    $gotData->user->userID=$row['id'];
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="You do not have account in OUR app. Please register yourself at OUR app";
  return $gotData;
}
function createHome($gotData){
  $gotData=getUserID($gotData);
  if($gotData->error) return $gotData;
  $userID=$gotData->user->userID;
  $homeName=$gotData->user->home->homeName;
  $sql="INSERT INTO home(uid,homename) VALUES('$userID','$homeName')";
  $result=mysqli_query($gotData->con,$sql);
  if($result)
  {
    $gotData->error=false;
    $gotData->user->home->id=mysqli_insert_id($gotData->con);
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="Try again!";
  return $gotData;
}
function deleteHome($gotData){
  $gotData=getUserID($gotData);
  if($gotData->error) return $gotData;
  $id=$gotData->user->home->id;
  $sql="DELETE FROM room_device where hid='$id'";
  $result=mysqli_query($gotData->con,$sql);
  if($result)
  {
    $sql="DELETE FROM hardware where hid='$id'";
    $result=mysqli_query($gotData->con,$sql);
    if($result)
    {
      $sql="DELETE FROM room where hid='$id'";
      $result=mysqli_query($gotData->con,$sql);
      if($result)
      {
        $sql="DELETE FROM home where id='$id'";
        $result=mysqli_query($gotData->con,$sql);
        if($result)
        {
          $gotData->error=false;
          return $gotData;
        }
        $gotData->error=true;
        $gotData->errorMessage="Try again!";
        return $gotData;
      }
      $gotData->error=true;
      $gotData->errorMessage="Try again!";
      return $gotData;
    }
    $gotData->error=true;
    $gotData->errorMessage="Try again!";
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="Try again!";
  return $gotData;
}
function renameHome($gotData){
  $gotData=getUserID($gotData);
  if($gotData->error) return $gotData;
  $homeName=$gotData->user->home->homeName;
  $id=$gotData->user->home->id;
  $sql="UPDATE home SET homename='$homeName' where id='$id'";
  $result=mysqli_query($gotData->con,$sql);
  if($result && (mysqli_affected_rows($gotData->con)==1))
  {
    $gotData->error=false;
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="Try again!";
  return $gotData;
}
function getHomeData($gotData){
  $gotData=getUserID($gotData);
  if($gotData->error) return $gotData;
  $userID=$gotData->user->userID;
  $sql="SELECT * FROM home where uid='$userID'";
  $check=mysqli_query($gotData->con,$sql);
  $gotData->total=mysqli_num_rows($check);
  if($check && ($gotData->total>=0))
  {
    $gotData->user->allHome=(object) null;
    $email=$gotData->user->email;
    if($gotData->total==0){
      $noFound='<div class="row" align="center" style="margin-top: 80px;">
              	<div id="no_found"><img src="images/not-found.png" width="100px" alt="no found" /></div>
              	<br/>
              	<div style="color:gray;"><h4>Not found!</h4></div>
              	</div>';
      $gotData->user->allHome=$noFound;
      return $gotData;
    }
    $allHome="<div class='grid-container' style='cursor:pointer;'>";
    while($row=mysqli_fetch_array($check)){
      $homeName=$row['homename'];
      $id=$row['id'];
      $editFunc = "editHome($id,'".$homeName."')";
      $deleteButton="<button class='btn btn-default' onclick='deleteHome($id)' ><span class='glyphicon glyphicon-trash'></span> Delete</button>";
      $editButton="<button class='btn btn-default' onclick=$editFunc><span class='glyphicon glyphicon-pencil'></span> Edit</button>";
      $allHome.='<div class="grid-item card"><div class="row"><a href="#!customer/home/'.$homeName.'">'.$homeName.'</a></div><div class="row"><div class="col-md-3"></div><div class="col-md-3">'.$editButton.'</div><div class="col-md-3">'.$deleteButton.'</div><div class="col-md-3"></div></div></div>';
    }
    $allHome.='</div>';
    $gotData->user->allHome=$allHome;
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="Try again!";
  return $gotData;
}
function getHomeList($gotData){
  $gotData=getUserID($gotData);
  if($gotData->error) return $gotData;
  $userID=$gotData->user->userID;
  $sql="SELECT * FROM home where uid='$userID'";
  $check=mysqli_query($gotData->con,$sql);
  $gotData->total=mysqli_num_rows($check);
  if($check && ($gotData->total>=0))
  {
    $i=0;
    $email=$gotData->user->email;
    while($row=mysqli_fetch_array($check)){
      $gotData->user->home[$i] = (object) null;
      $homeName=$row['homename'];
      $id=$row['id'];
      $gotData->user->home[$i]->homeName=$homeName;
      $gotData->user->home[$i]->id=$id;
      $gotData->user->home[$i]->email=$email;
      $i++;
    }
    return $gotData;
  }
  $gotData->error=true;
  $gotData->errorMessage="Try again!";
  return $gotData;
}
if(isset($_REQUEST['action'])){
  $action=$_REQUEST['action'];
  if($action=="0" && isset($_REQUEST['email'])){
    $email=$_REQUEST['email'];
    $gotData = (object) null;
    $gotData->user=(object) null;
    $gotData->error=false;
    $gotData->errorMessage="null";
    $gotData->con=$con;
    $gotData->user->email=$email;
    $gotData=getHomeData($gotData);
    $gotData->con=(object) null;
    echo json_encode($gotData);
  }else if($action=="1" && isset($_REQUEST['email']) && isset($_REQUEST['homeName'])){
    $email=$_REQUEST['email'];
    $homeName=$_REQUEST['homeName'];
    $gotData = (object) null;
    $gotData->user=(object) null;
    $gotData->user->home=(object) null;
    $gotData->error=false;
    $gotData->errorMessage="null";
    $gotData->con=$con;
    $gotData->user->email=$email;
    $gotData->user->home->homeName=$homeName;
    $gotData->user->home->email=$email;
    $gotData->user->home->action=$action;
    $gotData=createHome($gotData);
    $gotData->con=(object) null;
    echo json_encode($gotData);
  }else if($action=="2" && isset($_REQUEST['email']) && isset($_REQUEST['id']))
  {
    $email=$_REQUEST['email'];
    $id=$_REQUEST['id'];
    $gotData = (object) null;
    $gotData->user=(object) null;
    $gotData->user->home=(object) null;
    $gotData->error=false;
    $gotData->errorMessage="null";
    $gotData->con=$con;
    $gotData->user->email=$email;
    $gotData->user->home->id=$id;
    $gotData->user->home->email=$email;
    $gotData->user->home->action=$action;
    $gotData=deleteHome($gotData);
    echo json_encode($gotData);
  }else if($action=="3" && isset($_REQUEST['email']) && isset($_REQUEST['id']) && isset($_REQUEST['homeName']))
  {
    $email=$_REQUEST['email'];
    $id=$_REQUEST['id'];
    $homeName=$_REQUEST['homeName'];
    $gotData = (object) null;
    $gotData->user=(object) null;
    $gotData->user->home=(object) null;
    $gotData->error=false;
    $gotData->errorMessage="null";
    $gotData->user->home->id=$id;
    $gotData->con=$con;
    $gotData->user->email=$email;
    $gotData->user->home->homeName=$homeName;
    $gotData->user->home->email=$email;
    $gotData->user->home->action=$action;
    $gotData=renameHome($gotData);
    $gotData->con=(object) null;
    echo json_encode($gotData);
  }else if($action=="3" && isset($_REQUEST['email']) && isset($_REQUEST['id']) && isset($_REQUEST['homeName']))
  {
    $email=$_REQUEST['email'];
    $id=$_REQUEST['id'];
    $homeName=$_REQUEST['homeName'];
    $gotData = (object) null;
    $gotData->user=(object) null;
    $gotData->user->home=(object) null;
    $gotData->error=false;
    $gotData->errorMessage="null";
    $gotData->user->home->id=$id;
    $gotData->con=$con;
    $gotData->user->email=$email;
    $gotData->user->home->homeName=$homeName;
    $gotData->user->home->email=$email;
    $gotData->user->home->action=$action;
    $gotData=renameHome($gotData);
    $gotData->con=(object) null;
    echo json_encode($gotData);
  }else if($action=="4" && isset($_REQUEST['email'])){
    $email=$_REQUEST['email'];
    $gotData = (object) null;
    $gotData->user=(object) null;
    $gotData->error=false;
    $gotData->errorMessage="null";
    $gotData->con=$con;
    $gotData->user->email=$email;
    $gotData=getHomeList($gotData);
    $gotData->con=(object) null;
    echo json_encode($gotData);
  }else{
    $gotData = (object) null;
    $gotData->error=true;
    $gotData->errorMessage="Please, try again after few minutes!";
    echo json_encode($gotData);
  }
}

?>
