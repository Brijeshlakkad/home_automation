myApp.controller("userController", function($rootScope, $scope, $localStorage, $sessionStorage, $window, $location, $sce, $interval, $ocLazyLoad) {
  $ocLazyLoad.load('js/meanmenu/jquery.meanmenu.js');
  var i;
  $rootScope.isLoggedIn=false;
  $scope.index = ['/','/customer/login', '/customer/register', '/customer/forget_password', '/dealer_distributor/login', '/dealer_distributor/signup', '/customer/forget_password', '/dealer_distributor/forget_password', '/admin/login'];
  $rootScope.checkPathIndex=function(){
    for (i = 0; i < $scope.index.length; i++) {
      if ($location.path() == $scope.index[i]) {
        return false;
      }
    }
    return true;
  };
  $rootScope.checkSessionData = function(){
    if ($localStorage.userID != null && $localStorage.user != null && $localStorage.userType != null) {
      if(!$rootScope.checkPathIndex()){
        if($localStorage.userType=="customer"){
          $rootScope.isLoggedIn = true;
          $window.location.href = "#!/customer/home";
        }else if($localStorage.userType=="dealer"){
          $rootScope.isLoggedIn = true;
          $window.location.href = "#!/dealer_distributor/home";
        }else if($localStorage.userType=="admin"){
          $rootScope.isLoggedIn = true;
          $window.location.href = "#!/admin/home";
        }
      }
    }else{
      $localStorage.$reset({
        userID: null,
        user: null,
        userType: null
      });
      if($rootScope.checkPathIndex()){
        $window.location.href = "#!/";
      }
      $rootScope.isLoggedIn = false;
    }
  };
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
  $scope.noProductSerial="No Product Serials";
  $scope.noSubscription="You do not have any subscription yet. Please register product using product serial in hardware.";
  $scope.noMember="No Members";
  $scope.noHardwareList="No Hardware";
  $scope.noScheduledDevice="Devices are not scheduled yet";
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
