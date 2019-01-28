myApp.controller("DeviceController", function($rootScope,$scope,$http,$window,$sce,$timeout,$cookies,$routeParams) {
  $scope.user=$rootScope.$storage.user;
  $scope.userID=$rootScope.$storage.userID;
  $scope.dvReName="";
  $scope.dvRePort="";
  $scope.dvReImg="";
  $scope.beforeDvName="";
  $scope.beforeDvPort="";
  $scope.beforeDvImg="";
  $scope.homeID=$routeParams.homeID;
  $scope.roomID=$routeParams.roomID;
  $scope.hwID=$routeParams.hwID;
  $scope.dvID="";
  $rootScope.dvList=[];
  $rootScope.dvImgList=[];
  $scope.portOptions = [];
  for(i=0;i<=9;i++){
    $scope.portOptions.push(i+"");
  }
  $scope.dvNameStyle={
    "border-bottom-width":"2px"
  };
  $scope.analyzeDvName=function(val){
    var patt_room=new RegExp("^[a-zA-Z0-9]+$");
    if(patt_room.test(val)){
      $scope.dvNameStyle['border-bottom-color']="green";
    }else{
      $scope.dvNameStyle['border-bottom-color']="red";
    }
  };
  $scope.dvPortStyle={
    "border-bottom-width":"2px"
  };
  $scope.analyzeDvPort=function(val){
    if(val!="" && val!=null){
      $scope.dvPortStyle['border-bottom-color']="green";
    }else{
      $scope.dvPortStyle['border-bottom-color']="red";
    }
  };
  $scope.dvImgStyle={
    "border-bottom-width":"2px"
  };
  $scope.analyzeDvImg=function(val){
    if(val!="" && val!=null){
      $scope.dvImgStyle['border-bottom-color']="green";
    }else{
      $scope.dvImgStyle['border-bottom-color']="red";
    }
  };
  $scope.addDevice=function(){
    $http({
      method: "POST",
      url: "device_actions.php",
      data: "action=1&email="+$scope.user+"&dvName="+$scope.dvName+"&dvPort="+$scope.dvPort+"&dvImg="+$scope.dvImg+"&homeName="+$scope.homeID+"&roomName="+$scope.roomID+"&hwName="+$scope.hwID,
      headers: {'Content-Type': 'application/x-www-form-urlencoded'}
    }).then(function mySuccess(response){
      var data=response.data;
      $scope.dvForm.$setPristine();
      $scope.dvName="";
      alert(JSON.stringify(data));
      if(!data.error){
        $scope.showSuccessDialog("Device Created");
        $scope.getAllDevice();
      }else{
        $scope.showErrorDialog(data.errorMessage);
      }
    },function myError(response){

    });
  };
  $scope.getDeviceImgList=function(){
    $http({
      method: "POST",
      url: "device_actions.php",
      data: "action=4",
      headers: {'Content-Type': 'application/x-www-form-urlencoded'}
    }).then(function mySuccess(response){
      var data=response.data;
      if(!data.error){
        if(typeof data.user.deviceImg=='undefined'){
          $rootScope.dvImgList=[];
        }
        else{
          $rootScope.dvImgList=data.user.deviceImg;
        }
      }else{
      }
    },function myError(response){

    });
  };
  $scope.getDeviceList=function(){
    $http({
      method: "POST",
      url: "device_actions.php",
      data: "action=8&email="+$scope.user+"&homeName="+$scope.homeID+"&roomName="+$scope.roomID+"&hwName="+$scope.hwID,
      headers: {'Content-Type': 'application/x-www-form-urlencoded'}
    }).then(function mySuccess(response){
      var data=response.data;
      if(!data.error){
        if(typeof data.user.device=='undefined'){
          $rootScope.dvList=[];
        }
        else{
          $rootScope.dvList=data.user.device;
        }
      }else{
      }
    },function myError(response){

    });
  };
  $scope.showErrorDialog=function(error){
    swal({
     title: "Try Again!",
     text: ""+error,
     timer: 2000
   });
  };
  $scope.showSuccessDialog=function(val){
    swal(""+val, "", "success");
  };
  $scope.getAllDevice=function(){
    $http({
      method: "POST",
      url: "device_actions.php",
      data: "action=0&email="+$scope.user+"&homeName="+$scope.homeID+"&roomName="+$scope.roomID+"&hwName="+$scope.hwID,
      headers: {'Content-Type': 'application/x-www-form-urlencoded'}
    }).then(function mySuccess(response){
      var data=response.data;
      if(!data.error){
        var showAllDevice=data.user.allDevice;
        $scope.showAllDevice=$sce.trustAsHtml(showAllDevice);
        $scope.getDeviceList();
        $scope.getDeviceImgList();
      }else{
        $scope.showErrorDialog(data.errorMessage);
      }
    },function myError(response){

    });
  };
  $scope.getAllDevice();
  $scope.deleteDevice = function(val){
    swal({
      title: "Are you sure?",
      text: "You will not be able to recover this device!",
      type: "warning",
      showCancelButton: true,
      confirmButtonText: "Yes, delete it!",
    }).then(function(){
      $http({
        method: "POST",
        url: "device_actions.php",
        data: "action=2&email="+$scope.user+"&id="+val,
        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
      }).then(function mySuccess(response){
        var data=response.data;
        if(!data.error){
          swal("Deleted!", "Your device has been deleted.", "success");
          $scope.getAllDevice();
        }else{
          $scope.showErrorDialog(data.errorMessage);
        }
      });
    });
  };
  $scope.setRoomName=function(id,dvName,dvPort,dvImg,callback){
    $scope.beforeDvName=dvName;
    $scope.beforeDvPort=dvPort;
    $scope.beforeDvImg=dvImg;
    $scope.dvReName=dvName;
    $scope.dvRePort=dvPort;
    $scope.dvReImg=dvImg;
    /*
    for(i=0;i<$rootScope.dvImgList.length;i++){
      if(dvImg==$rootScope.dvImgList[i].key)
      {
        $scope.dvReImg=$rootScope.dvImgList[i].value;
        break;
      }
    }
    */
    $scope.dvID=id;
    $timeout(callback,10);
  };
  $scope.editDevice = function(id,dvName,dvPort,dvImg){
    $scope.setRoomName(id,dvName,dvPort,dvImg,function(){
      $("#renameDevice").modal("show");
    });
  };

  $scope.modifyDevice=function(){
    if($scope.beforeDvName!=$scope.dvReName || $scope.beforeDvPort!=$scope.dvRePort || $scope.beforeDvImg!=$scope.dvReImg){
      $http({
        method: "POST",
        url: "device_actions.php",
        data: "action=3&email="+$scope.user+"&dvName="+$scope.dvReName+"&dvPort="+$scope.dvRePort+"&dvImg="+$scope.dvReImg+"&id="+$scope.dvID,
        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
      }).then(function mySuccess(response){
        var data=response.data;
        alert(JSON.stringify(data));
        $scope.dvReForm.$setPristine();
        $scope.beforeDvName="";
        $scope.dvReName="";
        if(!data.error && (typeof data.error != 'undefined')){
          $scope.showSuccessDialog("Device Modified");
          $scope.getAllDevice();
        }else{
          $scope.showErrorDialog(data.errorMessage);
        }
      },function myError(response){

      });
    }
  };
});
function deleteDevice(id){
  angular.element($("#deviceModificationCtrl")).scope().deleteDevice(id);
}
function editDevice(id,dvName,dvPort,dvImg){
  angular.element($("#deviceModificationCtrl")).scope().editDevice(id,dvName,dvPort,dvImg);
}
function gotoDevice(dvID,hwID,roomID,homeID){
  angular.element($("#deviceModificationCtrl")).scope().gotoDevice(dvID,hwID,roomID,homeID);
}
myApp.directive("dvNameDir",function($rootScope,$http){
  return{
    require: 'ngModel',
    link: function(scope, element, attr, mCtrl){
      function myValidation(value){
        var patt_room=/^[a-zA-Z0-9]+$/;
        if(patt_room.test(value)){
          mCtrl.$setValidity('dvNameValid',true);
        }else{
          mCtrl.$setValidity('dvNameValid',false);
        }
        var i,flag=0;
        var len=$rootScope.dvList.length;
        for(i=0;i<len;i++){
          if($rootScope.dvList[i].dvName==value){
            flag=1;
          }
        }
        if(flag==1){
          mCtrl.$setValidity('dvNameExistsValid',false);
        }else{
          mCtrl.$setValidity('dvNameExistsValid',true);
        }
        return value;
      }
      mCtrl.$parsers.push(myValidation);
    }
  };
});
