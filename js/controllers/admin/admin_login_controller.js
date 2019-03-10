myApp.controller("AdminLoginController", function($rootScope, $scope, $http, $window, $localStorage, $sessionStorage, $ocLazyLoad) {
  if ($localStorage.userID != null && $localStorage.user != null && $localStorage.userType != null) {
    if($localStorage.userType=="customer"){
      $window.location.href = "#!customer/home";
    }else if($localStorage.userType=="dealer"){
      $window.location.href = "#!dealer_distributor/home";
    }else if($localStorage.userType=="admin"){
      $window.location.href = "#!admin/home";
    }
  }
  $ocLazyLoad.load(['js/meanmenu/jquery.meanmenu.js', 'js/data-table/jquery.dataTables.min.js', 'js/data-table/data-table-act.js', 'js/notification/bootstrap-growl.min.js','js/wow.min.js','js/main.js'], {
    rerun: true,
    cache: false
  });
  $scope.passwordStyle = {
    "border-bottom-width": "1.45px"
  };
  $scope.analyzePassword = function(value) {
    if (value == "" || value == null) {
      $scope.passwordStyle["border-bottom-color"] = "red";
    } else {
      $scope.passwordStyle["border-bottom-color"] = "green";
    }
  };
  $scope.login_status = function() {
    $http({
      method: "POST",
      url: "admin/login_data.php",
      data: "email=" + $scope.l_email + "&password=" + $scope.l_password,
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      }
    }).then(function mySuccess(response) {
      flag = response.data;
      // we should be using flag in only this block so logic in following
      if (!flag.error) {
        $scope.l_status_0 = false;
        $scope.l_status_1 = true;
        $localStorage.userID = flag.user.userID;
        $localStorage.user = flag.user.email;
        $localStorage.userType = flag.user.userType;
        /*$scope.$watch('userID', function() {
            $localStorage.userID = $scope.userID;
        });
        $scope.$watch('user', function() {
            $localStorage.user = $scope.user;
        });
        $scope.$watch(function() {
            return angular.toJson($localStorage);
        }, function() {
            $scope.userID = $localStorage.userID;
            $scope.user = $localStorage.user;
        });*/
        $window.location.href = flag.user.location;
        $rootScope.isLoggedIn = true;
      } else {
        $scope.l_status_1 = false;
        $scope.l_status_0 = true;
      }
    }, function myError(response) {

    });
  };

});
