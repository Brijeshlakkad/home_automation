myApp.controller("userController", function($rootScope, $scope, $localStorage, $sessionStorage, $window, $location, $sce, $interval, $ocLazyLoad) {
  $ocLazyLoad.load('js/meanmenu/jquery.meanmenu.js');
  $scope.index = ['/customer', '/customer/login', '/customer/register', '/dealer/login', '/dealer/signup', '/customer/forget_password', '/dealer/forget_password', '/admin/login'];
  $scope.path = 0;
  var i;
  $rootScope.isLoggedIn=false;
  for (i = 0; i < $scope.index.length; i++) {
    if ($location.path() == $scope.index[i]) {
      $scope.path = 1;
    }
  }
  if ($localStorage.userID == null || $localStorage.user == null || $localStorage.userType == null) {
    $rootScope.logout();
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
  $rootScope.inputNotValid=$sce.trustAsHtml("<span style='color:red;'><i class='glyphicon glyphicon-remove'></i></span>");
  $rootScope.inputValid=$sce.trustAsHtml("<span style='color:green;'><i class='glyphicon glyphicon-ok'></i></span>");
  $scope.noProduct="No Products";
  $scope.noProductSerial="No Product Serial";
});
myApp.directive('showNothing', function() {
  return {
    restrict: 'E',
    scope: {
      nothing: '=stringb',
    },
    templateUrl: 'show_nothing.html'
  };
});
