var myApp = angular.module("myapp", ['ngCookies','ngStorage']);
myApp.controller("DeviceStatusController", function($rootScope,$scope,$http,$window,$sce,$timeout,$cookies) {
  $scope.user=$rootScope.$storage.user;
  $scope.userID=$rootScope.$storage.userID;
  $scope.homeID=$cookies.get('homeID');
  $scope.roomID=$cookies.get('roomID');
  $scope.hwID=$cookies.get('hwID');
  $scope.dvID=$cookies.get('dvID');
  $rootScope.device="";
  $rootScope.deviceSlider="";
  $scope.deviceSliderValue=0;
  $scope.dvName="";
  $scope.addClass={"btn":true,"btn-danger":false,"btn-primary":false};
  $scope.deviceStatusPrint="Connection Problem!";
  $scope.getDevice=function(){
    $http({
      method: "POST",
      url: "device_actions.php",
      data: "action=5&email="+$scope.user+"&deviceID="+$scope.dvID,
      headers: {'Content-Type': 'application/x-www-form-urlencoded'}
    }).then(function mySuccess(response){
      var data=response.data;
      if(!data.error){
        if(typeof data.user=='undefined'){
          $rootScope.device="";
        }
        else{
          $rootScope.device=data.user;
          $rootScope.deviceSlider=data.user.deviceSlider;
          $scope.deviceStatus=$rootScope.device.dvStatus;
          if($rootScope.deviceSlider!="null"){
            $scope.amountp=$rootScope.deviceSlider.value;
            $scope.deviceSliderValue=$rootScope.deviceSlider.value;
            $("#deviceSliderValue").val($rootScope.deviceSlider.value);
          }
          if($scope.deviceStatus==1){
            $scope.deviceStatusPrint="ON";
            $scope.addClass['btn-primary']=true;
            $scope.addClass['btn-danger']=false;
          }else{
            $scope.deviceStatusPrint="OFF";
            $scope.addClass['btn-primary']=false;
            $scope.addClass['btn-danger']=true;
          }
        }
      }else{
      }
    },function myError(response){

    });
  };
  $scope.getDevice();
  $scope.changeDeviceStatus=function(val){
    if(val==0){
      val=1;
    }else{
      val=0;
    }
    $http({
      method: "POST",
      url: "device_actions.php",
      data: "action=6&email="+$scope.user+"&deviceID="+$scope.dvID+"&status="+val,
      headers: {'Content-Type': 'application/x-www-form-urlencoded'}
    }).then(function mySuccess(response){
      var data=response.data;
      if(!data.error){
        if(typeof data.user=='undefined'){
          $rootScope.device="";
        }
        else{
          $rootScope.device=data.user;
          $rootScope.deviceSlider=data.user.deviceSlider;
          $scope.deviceStatus=$rootScope.device.dvStatus;
          if($rootScope.deviceSlider!="null"){
            $scope.amountp=$rootScope.deviceSlider.value;
            $scope.deviceSliderValue=$rootScope.deviceSlider.value;
          }
          if($scope.deviceStatus==1){
            $scope.deviceStatusPrint="ON";
            $scope.addClass['btn-primary']=true;
            $scope.addClass['btn-danger']=false;
          }else{
            $scope.deviceStatusPrint="OFF";
            $scope.addClass['btn-primary']=false;
            $scope.addClass['btn-danger']=true;
          }
        }
      }else{
      }
    },function myError(response){

    });
  };
  $scope.changeDeviceSlider=function(val){
    if(typeof val == 'undefined' || val==null){

    }else{
      $http({
        method: "POST",
        url: "device_actions.php",
        data: "action=7&email="+$scope.user+"&deviceID="+$scope.dvID+"&value="+val,
        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
      }).then(function mySuccess(response){
        var data=response.data;
        if(!data.error){
          $scope.getDevice();
        }else{
        }
      },function myError(response){

      });
    }
  };
});
myApp.directive("sliderDir",function($rootScope,$http){
  return{
    require: 'ngModel',
    link: function(scope, element, attr, mCtrl){
      function myValidation(val){
        if(typeof val == 'undefined' || val==null || val<0 || val>5){
          mCtrl.$setValidity('sliderValid',false);
        }else{
          mCtrl.$setValidity('sliderValid',true);
        }
        return val;
      }
      mCtrl.$parsers.push(myValidation);
    }
  };
});
