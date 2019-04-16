myApp.controller("ScheduledDevicesController", function($rootScope, $scope, $http, $window, $sce, $timeout, $cookies, $routeParams, $ocLazyLoad) {
  $rootScope.checkSessionData();
  $ocLazyLoad.load(['js/meanmenu/jquery.meanmenu.js', 'js/notification/bootstrap-growl.min.js', 'js/wow.min.js', 'js/main.js'], {
    rerun: true,
    cache: false
  });
  $scope.user = $rootScope.$storage.user;
  $scope.userID = $rootScope.$storage.userID;
  $scope.userType = $rootScope.$storage.userType;
  $scope.scheduledDeviceList = [];
  $scope.getScheduleList = function() {
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
        if (data.totalRows > 0) {
          $scope.scheduledDeviceList = data.scheduledDevice;
        } else {
          $scope.scheduledDeviceList = [];
        }
      } else {
        $scope.scheduledDeviceList = [];
      }
    }, function myError(response) {
      $scope.scheduledDeviceList = [];
    });
  };
  $scope.getScheduleList();
  $scope.removeSchedule = function(scheduledDevice) {
    swal({
      title: "Are you sure?",
      text: "You will not be able to recover this home!",
      type: "warning",
      showCancelButton: true,
      confirmButtonText: "Yes, delete it!",
    }).then(function() {
      $rootScope.body.addClass("loading");
      $http({
        method: "POST",
        url: "schedule_device.php",
        data: "action=4&email=" + $scope.user + "&scheduleID=" + scheduledDevice.scheduleID,
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        }
      }).then(function mySuccess(response) {
        var data = response.data;
        if (!data.error) {
          $scope.getScheduleList();
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
    });
  };
});
