myApp.controller("HardwareController", function($rootScope, $scope, $http, $window, $sce, $timeout, $cookies, $routeParams, $ocLazyLoad) {
  $ocLazyLoad.load(['js/meanmenu/jquery.meanmenu.js','js/notification/bootstrap-growl.min.js','js/wow.min.js','js/main.js'], {
    rerun: true,
    cache: false
  });
  $scope.user = $rootScope.$storage.user;
  $scope.userID = $rootScope.$storage.userID;
  $scope.showAddHardware = false;
  $scope.hwReName = "";
  $scope.hwReSeries = "";
  $scope.hwReIP = "";
  $scope.beforeHwName = "";
  $scope.beforeHwSeries = "";
  $scope.beforeHwIP = "";
  $scope.homeID = $routeParams.homeID;
  $scope.roomID = $routeParams.roomID;
  $scope.hwID = "";
  $rootScope.hwList = "";
  $scope.patternIP = /^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;
  $scope.hwSeriesStyle = {
    "border-bottom-width": "2px"
  };
  $scope.analyzeHwSeries = function(val) {
    if (val != "" && val != null) {
      $scope.hwSeriesStyle['border-bottom-color'] = "green";
    } else {
      $scope.hwSeriesStyle['border-bottom-color'] = "red";
    }
  };
  $scope.hwIPStyle = {
    "border-bottom-width": "2px"
  };
  $scope.analyzeHwIP = function(val) {
    if (val != "" && val != null) {
      $scope.hwIPStyle['border-bottom-color'] = "green";
    } else {
      $scope.hwIPStyle['border-bottom-color'] = "red";
    }
  };
  $scope.addHardware = function() {
    $rootScope.body.addClass("loading");
    $http({
      method: "POST",
      url: "hardware_actions.php",
      data: "action=1&email=" + $scope.user + "&hwName=" + $scope.hwName + "&hwSeries=" + $scope.hwSeries + "&hwIP=" + $scope.hwIP + "&homeName=" + $scope.homeID + "&roomName=" + $scope.roomID + "&userID=" + $scope.userID,
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      }
    }).then(function mySuccess(response) {
      var data = response.data;
      $scope.hwForm.$setPristine();
      $scope.hwName = "";
      if (!data.error) {
        $scope.showSuccessDialog("Hardware Created");
        $scope.getAllHardware();
        $rootScope.body.removeClass("loading");
      } else {
        $rootScope.body.removeClass("loading");
        $scope.showErrorDialog(data.errorMessage);
      }
    }, function myError(response) {
      $rootScope.body.removeClass("loading");
    });
  };
  $scope.getHardwareList = function() {
    $http({
      method: "POST",
      url: "hardware_actions.php",
      data: "action=4&email=" + $scope.user + "&homeName=" + $scope.homeID + "&roomName=" + $scope.roomID + "&userID=" + $scope.userID,
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      }
    }).then(function mySuccess(response) {
      var data = response.data;
      if (!data.error) {
        if (typeof data.user.hw == 'undefined') {
          $rootScope.hwList = [];
        } else {
          $rootScope.hwList = data.user.hw;
        }
      } else {}
    }, function myError(response) {

    });
  };
  $scope.showErrorDialog = function(error) {
    swal({
      title: "Try Again!",
      text: "" + error,
      timer: 2000
    });
  };
  $scope.showSuccessDialog = function(val) {
    swal("" + val, "", "success");
  };
  $scope.getAllHardware = function() {
    $rootScope.body.addClass("loading");
    $http({
      method: "POST",
      url: "hardware_actions.php",
      data: "action=0&email=" + $scope.user + "&homeName=" + $scope.homeID + "&roomName=" + $scope.roomID + "&userID=" + $scope.userID,
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      }
    }).then(function mySuccess(response) {
      var data = response.data;
      if (!data.error) {
        var showAllHardware = data.user.allHardware;
        $scope.showAllHardware = $sce.trustAsHtml(showAllHardware);
        $scope.getHardwareList();
        $scope.showAddHardware = true;
        $rootScope.body.removeClass("loading");
      } else {
        $scope.showAddHardware = false;
        $rootScope.body.removeClass("loading");
        $scope.showErrorDialog(data.errorMessage);
      }
    }, function myError(response) {
      $scope.showAddHardware = false;
      $rootScope.body.removeClass("loading");
    });
  };
  $scope.getAllHardware();
  $scope.deleteHardware = function(val) {
    swal({
      title: "Are you sure?",
      text: "You will not be able to recover this hardware!",
      type: "warning",
      showCancelButton: true,
      confirmButtonText: "Yes, delete it!",
    }).then(function() {
      $rootScope.body.addClass("loading");
      $http({
        method: "POST",
        url: "hardware_actions.php",
        data: "action=2&email=" + $scope.user + "&id=" + val,
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        }
      }).then(function mySuccess(response) {
        $rootScope.body.removeClass("loading");
        var data = response.data;
        if (!data.error) {
          swal("Deleted!", "Your hardware has been deleted.", "success");
          $scope.getAllHardware();
        } else {
          $scope.showErrorDialog(data.errorMessage);
        }
      });
    });
  };
  $scope.setRoomName = function(id, hwName, hwSeries, hwIP, callback) {
    $scope.beforeHwName = hwName;
    $scope.beforeHwSeries = hwSeries;
    $scope.beforeHwIP = hwIP;
    $scope.hwReName = hwName;
    $scope.hwReSeries = hwSeries;
    $scope.hwReIP = hwIP;
    $scope.hwID = id;
    $timeout(callback, 10);
  };
  $scope.editHardware = function(id, hwName, hwSeries, hwIP) {
    $scope.setRoomName(id, hwName, hwSeries, hwIP, function() {
      $("#renameHardware").modal("show");
    });
  };

  $scope.modifyHardware = function() {
    if ($scope.beforeHwName != $scope.hwReName || $scope.beforeHwSeries != $scope.hwReSeries || $scope.beforeHwIP != $scope.hwReIP) {
      $rootScope.body.addClass("loading");
      $http({
        method: "POST",
        url: "hardware_actions.php",
        data: "action=3&email=" + $scope.user + "&hwName=" + $scope.hwReName + "&hwSeries=" + $scope.hwReSeries + "&hwIP=" + $scope.hwReIP + "&id=" + $scope.hwID,
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        }
      }).then(function mySuccess(response) {
        var data = response.data;
        $scope.hwReForm.$setPristine();
        $scope.beforeHwName = "";
        $scope.hwReName = "";
        if (!data.error && (typeof data.error != 'undefined')) {
          $scope.showSuccessDialog("Hardware Modified");
          $scope.getAllHardware();
          $rootScope.body.removeClass("loading");
        } else {
          $scope.showErrorDialog(data.errorMessage);
          $rootScope.body.removeClass("loading");
        }
      }, function myError(response) {
        $rootScope.body.removeClass("loading");
      });
    }
  };

});

function deleteHardware(id) {
  angular.element($("#hwModificationCtrl")).scope().deleteHardware(id);
}

function editHardware(id, hwName, hwSeries, hwIP) {
  angular.element($("#hwModificationCtrl")).scope().editHardware(id, hwName, hwSeries, hwIP);
}
myApp.directive("hwNameDir", function($rootScope, $http) {
  return {
    require: 'ngModel',
    link: function(scope, element, attr, mCtrl) {
      function myValidation(value) {
        mCtrl.$setValidity('hwNameExistsValid', true);
        var pattHardware = /^[a-zA-Z0-9]+$/;
        var i, flag = 0;
        var len = $rootScope.hwList.length;
        if (pattHardware.test(value) && value.length > 3) {
          mCtrl.$setValidity('hwNameValid', true);
          mCtrl.$setValidity('hwNameLenValid', true);
          for (i = 0; i < len; i++) {
            if ($rootScope.hwList[i].hwName == value) {
              flag = 1;
            }
          }
          if (flag == 1) {
            mCtrl.$setValidity('hwNameExistsValid', false);
            element.css({
              "border-bottom-width": "1.45px",
              "border-bottom-color": 'red'
            });
          } else {
            mCtrl.$setValidity('hwNameExistsValid', true);
            element.css({
              "border-bottom-width": "1.45px",
              "border-bottom-color": 'green'
            });
          }
        } else if (!pattHardware.test(value) && value.length > 3) {
          mCtrl.$setValidity('hwNameValid', false);
          mCtrl.$setValidity('hwNameLenValid', true);
          element.css({
            "border-bottom-width": "1.45px",
            "border-bottom-color": 'red'
          });
        } else if (pattHardware.test(value) && value.length <= 3) {
          mCtrl.$setValidity('hwNameValid', true);
          mCtrl.$setValidity('hwNameLenValid', false);
          element.css({
            "border-bottom-width": "1.45px",
            "border-bottom-color": 'red'
          });
        } else {
          mCtrl.$setValidity('hwNameValid', false);
          mCtrl.$setValidity('hwNameLenValid', false);
          element.css({
            "border-bottom-width": "1.45px",
            "border-bottom-color": 'red'
          });
        }
        return value;
      }
      mCtrl.$parsers.push(myValidation);
    }
  };
});

myApp.directive("hwSeriesDir", function() {
  return {
    require: 'ngModel',
    link: function(scope, element, attr, mCtrl) {
      function myValidation(value) {
        var pattSeries = /^([0-9]{4})([A-Z0-9]{4})([0-9]{4})$/;
        if (pattSeries.test(value)) {
          mCtrl.$setValidity('hwSeriesValid', true);
          element.css({
            "border-bottom-width": "1.45px",
            "border-bottom-color": 'green'
          });
        } else {
          mCtrl.$setValidity('hwSeriesValid', false);
          element.css({
            "border-bottom-width": "1.45px",
            "border-bottom-color": 'red'
          });
        }
        return value;
      }
      mCtrl.$parsers.push(myValidation);
    }
  };
});
