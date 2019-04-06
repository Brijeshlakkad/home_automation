myApp.controller("DeviceStatusController", function($rootScope, $scope, $http, $window, $sce, $timeout, $cookies, $routeParams, $ocLazyLoad) {
  $ocLazyLoad.load(['js/meanmenu/jquery.meanmenu.js', 'js/notification/bootstrap-growl.min.js', 'js/wow.min.js', 'js/main.js'], {
    rerun: true,
    cache: false
  });
  $rootScope.checkSessionData();
  $scope.user = $rootScope.$storage.user;
  $scope.userID = $rootScope.$storage.userID;
  $scope.homeID = $routeParams.homeID;
  $scope.roomID = $routeParams.roomID;
  $scope.hwID = $routeParams.hwID;
  $scope.dvID = $routeParams.dvID;
  $scope.dateMin = new Date().toISOString().split('T')[0];
  $scope.dateMin = $scope.dateMin + "T00:00:00";
  $scope.repetitionArray=['WEEKLY','ONCE','SUNDAY','MONDAY','TUESDAY','WEDNESDAY','THURSDAY','FRIDAY','SATURDAY']
  $scope.afterStatusArray=['OFF','ON'];
  $scope.afterStatus=1;
  $scope.editMode=false;
  $scope.scheduleInfo={};
  $scope.isScheduled=false;
  $scope.repetition=$scope.repetitionArray[0];
  $scope.afterStatusPrint=$scope.afterStatusArray[$scope.afterStatus];
  $rootScope.device = "";
  $rootScope.deviceSlider = "";
  $scope.deviceSliderValue = 0;
  $scope.dvName = "";
  $scope.addClass = {
    "btn": true,
    "btn-danger": false,
    "btn-primary": false
  };
  $scope.addClassAfterStatus = {
    "btn": true,
    "btn-danger": false,
    "btn-primary": true
  };
  $scope.deviceStatusPrint = "Connection Problem!";
  $scope.getDevice = function() {
    $rootScope.body.addClass("loading");
    $http({
      method: "POST",
      url: "device_actions.php",
      data: "action=5&email=" + $scope.user + "&deviceName=" + $scope.dvID + "&homeName=" + $scope.homeID + "&roomName=" + $scope.roomID + "&hwName=" + $scope.hwID + "&userID=" + $scope.userID,
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      }
    }).then(function mySuccess(response) {
      var data = response.data;
      if (!data.error) {
        if (typeof data.user == 'undefined') {
          $rootScope.device = "";
        } else {
          $rootScope.device = data.user;
          $rootScope.deviceImg = data.user.deviceImg;
          $rootScope.deviceSlider = data.user.deviceSlider;
          $scope.deviceStatus = $rootScope.device.dvStatus;
          if ($rootScope.deviceSlider != "null") {
            $scope.amountp = $rootScope.deviceSlider.value;
            $scope.deviceSliderValue = $rootScope.deviceSlider.value;
            $("#deviceSliderValue").val($rootScope.deviceSlider.value);
          }
          if ($scope.deviceStatus == 1) {
            $scope.deviceStatusPrint = "ON";
            $scope.addClass['btn-primary'] = true;
            $scope.addClass['btn-danger'] = false;
          } else {
            $scope.deviceStatusPrint = "OFF";
            $scope.addClass['btn-primary'] = false;
            $scope.addClass['btn-danger'] = true;
          }
        }
        $rootScope.body.removeClass("loading");
      } else {
        $rootScope.body.removeClass("loading");
        $scope.showErrorDialog(data.errorMessage);
      }
    }, function myError(response) {
      $rootScope.body.removeClass("loading");
    });
  };
  $scope.getDevice();
  $scope.changeDeviceStatus = function(val) {
    if (val == 0) {
      val = 1;
    } else {
      val = 0;
    }
    $rootScope.body.addClass("loading");
    $http({
      method: "POST",
      url: "device_actions.php",
      data: "action=6&email=" + $scope.user + "&deviceName=" + $scope.dvID + "&status=" + val + "&homeName=" + $scope.homeID + "&roomName=" + $scope.roomID + "&hwName=" + $scope.hwID + "&userID=" + $scope.userID,
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      }
    }).then(function mySuccess(response) {
      var data = response.data;
      if (!data.error) {
        if (typeof data.user == 'undefined') {
          $rootScope.device = "";
        } else {
          $rootScope.device = data.user;
          $rootScope.deviceImg = data.deviceImg;
          $rootScope.deviceSlider = data.user.deviceSlider;
          $scope.deviceStatus = $rootScope.device.dvStatus;
          if ($rootScope.deviceSlider != "null") {
            $scope.amountp = $rootScope.deviceSlider.value;
            $scope.deviceSliderValue = $rootScope.deviceSlider.value;
          }
          if ($scope.deviceStatus == 1) {
            $scope.deviceStatusPrint = "ON";
            $scope.addClass['btn-primary'] = true;
            $scope.addClass['btn-danger'] = false;
          } else {
            $scope.deviceStatusPrint = "OFF";
            $scope.addClass['btn-primary'] = false;
            $scope.addClass['btn-danger'] = true;
          }
        }
        $rootScope.body.removeClass("loading");
      } else {
        $rootScope.body.removeClass("loading");
        $scope.showErrorDialog(data.errorMessage);
      }
    }, function myError(response) {
      $rootScope.body.removeClass("loading");
    });
  };
  $scope.showErrorDialog = function(error) {
    swal({
      title: "Try Again!",
      text: "" + error,
      timer: 2000
    });
  };
  $scope.changeDeviceSlider = function(val) {
    if (typeof val == 'undefined' || val == null) {

    } else {
      $rootScope.body.addClass("loading");
      $http({
        method: "POST",
        url: "device_actions.php",
        data: "action=7&email=" + $scope.user + "&deviceName=" + $scope.dvID + "&value=" + val + "&homeName=" + $scope.homeID + "&roomName=" + $scope.roomID + "&hwName=" + $scope.hwID + "&userID=" + $scope.userID,
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        }
      }).then(function mySuccess(response) {
        var data = response.data;
        if (!data.error) {
          $scope.getDevice();
          $rootScope.body.removeClass("loading");
        } else {
          $rootScope.body.removeClass("loading");
          $scope.showErrorDialog(data.errorMessage);
        }
      }, function myError(response) {
        $rootScope.body.removeClass("loading");
      });
    }
  };
  $scope.getRepition=function(){
    $http({
      method: "POST",
      url: "schedule_device.php",
      data: "action=3&email=" + $scope.user,
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      }
    }).then(function mySuccess(response) {
      var data = response.data;
      if (!data.error) {
        $scope.repetition = data.frequency;
        if($scope.repetition==0){
          $scope.repetitionSet=$scope.repetitionArray[0];
        }
        $rootScope.body.removeClass("loading");
      } else {
        $rootScope.body.removeClass("loading");
        $scope.showErrorDialog(data.errorMessage);
      }
    }, function myError(response) {
      $rootScope.body.removeClass("loading");
    });
  };
  $scope.changeAfterStatus=function(afterStatus){
    if(afterStatus==1){
      $scope.afterStatus=0;
      $scope.addClassAfterStatus['btn-primary'] = false;
      $scope.addClassAfterStatus['btn-danger'] = true;
    }else{
      $scope.afterStatus=1;
      $scope.addClassAfterStatus['btn-primary'] = true;
      $scope.addClassAfterStatus['btn-danger'] = false;
    }
    $scope.afterStatusPrint=$scope.afterStatusArray[$scope.afterStatus];
  };
  $scope.convertDatePickerTimeToMySQLTime = function(str) {
        var month, day, year, hours, minutes, seconds;
        var date = new Date(str),
            month = ("0" + (date.getMonth() + 1)).slice(-2),
            day = ("0" + date.getDate()).slice(-2);
        hours = ("0" + date.getHours()).slice(-2);
        minutes = ("0" + date.getMinutes()).slice(-2);
        seconds = ("0" + date.getSeconds()).slice(-2);

        var mySQLDate = [date.getFullYear(), month, day].join("-");
        var mySQLTime = [hours, minutes, seconds].join(":");
        return [mySQLDate, mySQLTime].join(" ");
    };
  $scope.scheduleDevice = function(startTime, endTime, afterStatus, repetition) {
    $rootScope.body.addClass("loading");
    startTime=$scope.convertDatePickerTimeToMySQLTime(startTime);
    endTime=$scope.convertDatePickerTimeToMySQLTime(endTime);
    $http({
      method: "POST",
      url: "schedule_device.php",
      data: "action=1&email=" + $scope.user + "&deviceName=" + $scope.dvID + "&roomName=" + $scope.roomID + "&startTime=" + startTime +"&endTime=" + endTime + "&afterStatus=" + afterStatus + "&repetition=" + repetition,
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      }
    }).then(function mySuccess(response) {
      var data = response.data;
      if (!data.error) {
        $rootScope.showSuccessDialog(data.data);
        $scope.getScheduleDevice();
        $rootScope.body.removeClass("loading");
      } else {
        $rootScope.body.removeClass("loading");
        $scope.showErrorDialog(data.errorMessage);
      }
    }, function myError(response) {
      $rootScope.body.removeClass("loading");
    });
  };
  $scope.getScheduleDevice = function() {
    $http({
      method: "POST",
      url: "schedule_device.php",
      data: "action=2&email=" + $scope.user + "&deviceName=" + $scope.dvID + "&roomName=" + $scope.roomID,
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      }
    }).then(function mySuccess(response) {
      var data = response.data;
      if (!data.error) {
        $scope.isScheduled=data.isScheduled;
        if($scope.isScheduled==true){
          $scope.scheduleInfo=data.scheduleInfo;
        }else{
          $scope.scheduleInfo={};
        }
      } else {
        $scope.scheduleInfo={};
      }
    }, function myError(response) {
      $scope.scheduleInfo={};
    });
  };
  $scope.getScheduleDevice();
  $scope.deleteScheduleDevice = function() {
    $http({
      method: "POST",
      url: "schedule_device.php",
      data: "action=3&email=" + $scope.user + "&deviceName=" + $scope.dvID + "&roomName=" + $scope.roomID,
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      }
    }).then(function mySuccess(response) {
      var data = response.data;
      alert(JSON.stringify(data));
      if (!data.error) {
        $scope.getScheduleDevice();
        $rootScope.showSuccessDialog(data.data);
        $rootScope.body.removeClass("loading");
      } else {
        $rootScope.showErrorDialog(data.errorMessage);
        $rootScope.body.removeClass("loading");
      }
    }, function myError(response) {
      $rootScope.showErrorDialog("Please try again later!");
      $rootScope.body.removeClass("loading");
    });
  };
});
myApp.directive("sliderDir", function($rootScope, $http) {
  return {
    require: 'ngModel',
    link: function(scope, element, attr, mCtrl) {
      function myValidation(val) {
        if (typeof val == 'undefined' || val == null || val < 0 || val > $rootScope.deviceImg.maxVal) {
          mCtrl.$setValidity('sliderValid', false);
        } else {
          mCtrl.$setValidity('sliderValid', true);
        }
        return val;
      }
      mCtrl.$parsers.push(myValidation);
    }
  };
});
