myApp.controller("HardwareController", function($rootScope,$scope,$http,$window,$sce,$timeout,$cookies,$routeParams) {
  $scope.user=$rootScope.$storage.user;
  $scope.userID=$rootScope.$storage.userID;
  $scope.hwReName="";
  $scope.hwReSeries="";
  $scope.hwReIP="";
  $scope.beforeHwName="";
  $scope.beforeHwSeries="";
  $scope.beforeHwIP="";
  $scope.homeID=$routeParams.homeID;
  $scope.roomID=$routeParams.roomID;
  $scope.hwID="";
  $rootScope.hwList="";
  $scope.hwNameStyle={
    "border-bottom-width":"2px"
  };
  $scope.analyzeHwName=function(val){
    var patt_room=new RegExp("^[a-zA-Z0-9]+$");
    if(patt_room.test(val) && val.length>3){
      $scope.hwNameStyle['border-bottom-color']="green";
    }else{
      $scope.hwNameStyle['border-bottom-color']="red";
    }
  };
  $scope.hwSeriesStyle={
    "border-bottom-width":"2px"
  };
  $scope.analyzeHwSeries=function(val){
    if(val!="" && val!=null){
      $scope.hwSeriesStyle['border-bottom-color']="green";
    }else{
      $scope.hwSeriesStyle['border-bottom-color']="red";
    }
  };
  $scope.hwIPStyle={
    "border-bottom-width":"2px"
  };
  $scope.analyzeHwIP=function(val){
    if(val!="" && val!=null){
      $scope.hwIPStyle['border-bottom-color']="green";
    }else{
      $scope.hwIPStyle['border-bottom-color']="red";
    }
  };
  $scope.addHardware=function(){
    $http({
      method: "POST",
      url: "hardware_actions.php",
      data: "action=1&email="+$scope.user+"&hwName="+$scope.hwName+"&hwSeries="+$scope.hwSeries+"&hwIP="+$scope.hwIP+"&homeID="+$scope.homeID+"&roomID="+$scope.roomID,
      headers: {'Content-Type': 'application/x-www-form-urlencoded'}
    }).then(function mySuccess(response){
      var data=response.data;
      $scope.hwForm.$setPristine();
      $scope.hwName="";
      if(!data.error){
        $scope.showSuccessDialog("Hardware Created");
        $scope.getAllHardware();
      }else{
        $scope.showErrorDialog(data.errorMessage);
      }
    },function myError(response){

    });
  };
  $scope.getHardwareList=function(){
    $http({
      method: "POST",
      url: "hardware_actions.php",
      data: "action=4&email="+$scope.user+"&homeID="+$scope.homeID+"&roomID="+$scope.roomID,
      headers: {'Content-Type': 'application/x-www-form-urlencoded'}
    }).then(function mySuccess(response){
      var data=response.data;
      if(!data.error){
        if(typeof data.user.hw=='undefined'){
          $rootScope.hwList=[];
        }
        else{
          $rootScope.hwList=data.user.hw;
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
  $scope.getAllHardware=function(){
    $http({
      method: "POST",
      url: "hardware_actions.php",
      data: "action=0&email="+$scope.user+"&homeID="+$scope.homeID+"&roomID="+$scope.roomID,
      headers: {'Content-Type': 'application/x-www-form-urlencoded'}
    }).then(function mySuccess(response){
      var data=response.data;
      if(!data.error){
        var showAllHardware=data.user.allHardware;
        $scope.showAllHardware=$sce.trustAsHtml(showAllHardware);
        $scope.getHardwareList();
      }else{
        $scope.showErrorDialog(data.errorMessage);
      }
    },function myError(response){

    });
  };
  $scope.getAllHardware();
  $scope.deleteHardware = function(val){
    swal({
      title: "Are you sure?",
      text: "You will not be able to recover this hardware!",
      type: "warning",
      showCancelButton: true,
      confirmButtonText: "Yes, delete it!",
    }).then(function(){
      $http({
        method: "POST",
        url: "hardware_actions.php",
        data: "action=2&email="+$scope.user+"&id="+val,
        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
      }).then(function mySuccess(response){
        var data=response.data;
        if(!data.error){
          swal("Deleted!", "Your hardware has been deleted.", "success");
          $scope.getAllHardware();
        }else{
          $scope.showErrorDialog(data.errorMessage);
        }
      });
    });
  };
  $scope.setRoomName=function(id,hwName,hwSeries,hwIP,callback){
    $scope.beforeHwName=hwName;
    $scope.beforeHwSeries=hwSeries;
    $scope.beforeHwIP=hwIP;
    $scope.hwReName=hwName;
    $scope.hwReSeries=hwSeries;
    $scope.hwReIP=hwIP;
    $scope.hwID=id;
    $timeout(callback,10);
  };
  $scope.editHardware = function(id,hwName,hwSeries,hwIP){
    $scope.setRoomName(id,hwName,hwSeries,hwIP,function(){
      $("#renameHardware").modal("show");
    });
  };

  $scope.modifyHardware=function(){
    if($scope.beforeHwName!=$scope.hwReName || $scope.beforeHwSeries!=$scope.hwReSeries || $scope.beforeHwIP!=$scope.hwReIP){
      $http({
        method: "POST",
        url: "hardware_actions.php",
        data: "action=3&email="+$scope.user+"&hwName="+$scope.hwReName+"&hwSeries="+$scope.hwReSeries+"&hwIP="+$scope.hwReIP+"&id="+$scope.hwID,
        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
      }).then(function mySuccess(response){
        var data=response.data;
        $scope.hwReForm.$setPristine();
        $scope.beforeHwName="";
        $scope.hwReName="";
        if(!data.error && (typeof data.error != 'undefined')){
          $scope.showSuccessDialog("Hardware Modified");
          $scope.getAllHardware();
        }else{
          $scope.showErrorDialog(data.errorMessage);
        }
      },function myError(response){

      });
    }
  };
});
function deleteHardware(id){
  angular.element($("#hwModificationCtrl")).scope().deleteHardware(id);
}
function editHardware(id,hwName,hwSeries,hwIP){
  angular.element($("#hwModificationCtrl")).scope().editHardware(id,hwName,hwSeries,hwIP);
}
myApp.directive("hwNameDir",function($rootScope,$http){
  return{
    require: 'ngModel',
    link: function(scope, element, attr, mCtrl){
      function myValidation(value){
        var patt_room=/^[a-zA-Z0-9]+$/;
        if(patt_room.test(value)){
          mCtrl.$setValidity('hwNameValid',true);
        }else{
          mCtrl.$setValidity('hwNameValid',false);
        }
        if(value.length>3){
          mCtrl.$setValidity('hwNameLenValid',true);
        }else{
          mCtrl.$setValidity('hwNameLenValid',false);
        }
        var i,flag=0;
        var len=$rootScope.hwList.length;
        for(i=0;i<len;i++){
          if($rootScope.hwList[i].hwName==value){
            flag=1;
          }
        }
        if(flag==1){
          mCtrl.$setValidity('hwNameExistsValid',false);
        }else{
          mCtrl.$setValidity('hwNameExistsValid',true);
        }
        return value;
      }
      mCtrl.$parsers.push(myValidation);
    }
  };
});
