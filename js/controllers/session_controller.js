myApp.controller("userController", function($rootScope, $scope, $localStorage, $sessionStorage, $window, $location, $interval, $ocLazyLoad) {
  $ocLazyLoad.load('js/meanmenu/jquery.meanmenu.js');
  $scope.index = ['/customer', '/customer/login', '/customer/register', '/dealer/login', '/dealer/signup', '/customer/forget_password', '/dealer/forget_password', '/admin/login'];
  $scope.path = 0;
  var i;
  for (i = 0; i < $scope.index.length; i++) {
    if ($location.path() == $scope.index[i]) {
      $scope.path = 1;
    }
  }
  if ($localStorage.userID != null && $localStorage.user != null && $localStorage.userType != null) {
    if($localStorage.userType=="customer"){
      $window.location.href = "#!customer/home";
    }else if($localStorage.userType=="dealer"){
      $window.location.href = "#!dealer/home";
    }else if($localStorage.userType=="admin"){
      $window.location.href = "#!admin/home";
    }
  }
  if ($localStorage.userID == null || $localStorage.user == null || $localStorage.userType == null) {
    $rootScope.isLoggedIn = false;
    if ($scope.path != 1) {
      $window.location.href = "#!/";
    }
  } else {
    $rootScope.isLoggedIn = true;
  }
  $rootScope.$storage = $localStorage;
  $rootScope.logout = function() {
    $localStorage.$reset({
      userID: null,
      user: null,
      userType: null
    });
    $window.location.href = "#!/";
    $rootScope.isLoggedIn = false;
  };
  var body = document.getElementsByTagName("BODY");
  $rootScope.body = angular.element(body);
  $rootScope.showErrorDialog = function(error) {
    swal({
      title: "Try Again!",
      text: "" + error,
      timer: 2000
    });
  };
  $rootScope.showSuccessDialog = function(val) {
    swal("" + val, "", "success");
  };
});
