myApp.controller("SignupController", function($rootScope, $scope, $http, $window, $localStorage, $sessionStorage, $ocLazyLoad) {
  $ocLazyLoad.load(['js/meanmenu/jquery.meanmenu.js','js/notification/bootstrap-growl.min.js','js/wow.min.js','js/main.js'], {
    rerun: true,
    cache: false
  });
  $rootScope.checkSessionData();
  $scope.signupStatus = "";
  $scope.signup_status = function() {
    $http({
      method: "POST",
      url: "signup_data.php",
      data: "action=1&email=" + $scope.s_email + "&password=" + $scope.s_password + "&name=" + $scope.s_name + "&address=" + $scope.s_address + "&city=" + $scope.s_city + "&mobile=" + $scope.s_contact,
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      }
    }).then(function mySuccess(response) {
      flag = response.data;
      // we should be using flag in only this block so logic in following
      if (!flag.error) {
        $scope.signupSuccess = flag.errorMessage;
        $scope.s_status_0 = false;
        $scope.s_status_1 = true;
      } else {
        $scope.signupError = flag.errorMessage;
        $scope.s_status_1 = false;
        $scope.s_status_0 = true;
      }
    }, function myError(response) {

    });
  };
});
