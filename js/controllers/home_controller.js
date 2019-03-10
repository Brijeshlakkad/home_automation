myApp.controller("HomeController", function($rootScope, $scope, $http, $window, $sce, $timeout, $cookies, $ocLazyLoad) {
  $ocLazyLoad.load(['js/meanmenu/jquery.meanmenu.js','js/notification/bootstrap-growl.min.js','js/wow.min.js','js/main.js'], {
    rerun: true,
    cache: false
  });
  $scope.user = $rootScope.$storage.user;
  $scope.userID = $rootScope.$storage.userID;
  $scope.userType = $rootScope.$storage.userType;
  $scope.showAddHome = false;
  $scope.homeReName = "";
  $scope.beforeHomeName = "";
  $scope.homeID = "";
  $rootScope.homeList = "";
  $rootScope.editHomeName = "";
  $scope.addHome = function() {
    $rootScope.body.addClass("loading");
    $http({
      method: "POST",
      url: "home_actions.php",
      data: "action=1&email=" + $scope.user + "&homeName=" + $scope.homeName,
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      }
    }).then(function mySuccess(response) {
      var data = response.data;
      $scope.homeNameForm.$setPristine();
      $scope.homeName = "";
      if (!data.error) {
        $scope.showSuccessDialog("Home Created");
        $scope.getAllHome();
        $rootScope.body.removeClass("loading");
      } else {
        $rootScope.body.removeClass("loading");
        $scope.showErrorDialog(data.errorMessage);
      }
    }, function myError(response) {
      $rootScope.body.removeClass("loading");
    });
  };
  $scope.getHomeList = function() {
    $http({
      method: "POST",
      url: "home_actions.php",
      data: "action=4&email=" + $scope.user,
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      }
    }).then(function mySuccess(response) {
      var data = response.data;
      if (!data.error) {
        $rootScope.homeList = data.user.home;
      } else {}
    }, function myError(response) {

    });
  };
  $scope.getHomeList();
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
  $scope.getAllHome = function() {
    $rootScope.body.addClass("loading");
    $http({
      method: "POST",
      url: "home_actions.php",
      data: "action=0&email=" + $scope.user,
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      }
    }).then(function mySuccess(response) {
      var data = response.data;
      if (!data.error) {
        var showAllHome = data.user.allHome;
        $scope.showAllHome = $sce.trustAsHtml(showAllHome);
        $scope.getHomeList();
        $scope.showAddHome = true;
        $rootScope.body.removeClass("loading");
      } else {
        $scope.showAddHome = false;
        $rootScope.body.removeClass("loading");
        $scope.showErrorDialog(data.errorMessage);
      }
    }, function myError(response) {
      $scope.showAddHome = false;
      $rootScope.body.removeClass("loading");
    });
  };
  $scope.getAllHome();
  $scope.deleteHome = function(val) {
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
        url: "home_actions.php",
        data: "action=2&email=" + $scope.user + "&id=" + val,
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        }
      }).then(function mySuccess(response) {
        $rootScope.body.removeClass("loading");
        var data = response.data;
        if (!data.error) {
          swal("Deleted!", "Your home has been deleted.", "success");
          $scope.getAllHome();
        } else {
          $scope.showErrorDialog(data.errorMessage);
        }
      });
    });
  };
  $scope.setHomeName = function(id, homeName, callback) {
    $scope.beforeHomeName = homeName;
    $scope.homeReName = homeName;
    $scope.homeID = id;
    $timeout(callback, 10);
  };
  $scope.editHome = function(id, homeName) {
    $rootScope.editHomeName = homeName;
    $scope.setHomeName(id, homeName, function() {
      $("#renameHome").modal("show");
    });
  };

  $scope.modifyHome = function() {
    if ($scope.beforeHomeName != $scope.homeReName) {
      $rootScope.body.addClass("loading");
      $http({
        method: "POST",
        url: "home_actions.php",
        data: "action=3&email=" + $scope.user + "&homeName=" + $scope.homeReName + "&id=" + $scope.homeID,
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        }
      }).then(function mySuccess(response) {
        var data = response.data;
        $scope.homeReNameForm.$setPristine();
        $scope.beforeHomeName = "";
        $scope.homeReName = "";
        if (!data.error && (typeof data.error != 'undefined')) {
          $scope.showSuccessDialog("Home Name Modified");
          $scope.getAllHome();
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
});

function deleteHome(id) {
  angular.element($("#homeModificationCtrl")).scope().deleteHome(id);
}

function editHome(id, homeName) {
  angular.element($("#homeModificationCtrl")).scope().editHome(id, homeName);
}
myApp.directive("homeNameDir", function($rootScope, $http) {
  return {
    require: 'ngModel',
    link: function(scope, element, attr, mCtrl) {
      function myValidation(value) {
        mCtrl.$setValidity('homeNameExistsValid', true);
        var pattHome = /^([a-zA-Z]+)([0-9]*)$/;
        var i, flag = 0;
        var len = $rootScope.homeList.length;
        if (pattHome.test(value) && (value.length > 2 || value.length <= 8)) {
          mCtrl.$setValidity('homeNameValid', true);
          mCtrl.$setValidity('homeNameLenValid', true);
          for (i = 0; i < len; i++) {
            if ($rootScope.homeList[i].homeName == value) {
              flag = 1;
            }
          }
          if (flag == 1) {
            mCtrl.$setValidity('homeNameExistsValid', false);
            element.css({
              "border-bottom-width": "1.45px",
              "border-bottom-color": 'red'
            });
          } else {
            mCtrl.$setValidity('homeNameExistsValid', true);
            element.css({
              "border-bottom-width": "1.45px",
              "border-bottom-color": 'green'
            });
          }
        } else if (!pattHome.test(value) && (value.length > 2 || value.length <= 8)) {
          mCtrl.$setValidity('homeNameValid', false);
          mCtrl.$setValidity('homeNameLenValid', true);
          element.css({
            "border-bottom-width": "1.45px",
            "border-bottom-color": 'red'
          });
        } else if (pattHome.test(value) && (value.length <= 2 || value.length > 8)) {
          mCtrl.$setValidity('homeNameValid', true);
          mCtrl.$setValidity('homeNameLenValid', false);
          element.css({
            "border-bottom-width": "1.45px",
            "border-bottom-color": 'red'
          });
        } else {
          mCtrl.$setValidity('homeNameValid', false);
          mCtrl.$setValidity('homeNameLenValid', false);
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
