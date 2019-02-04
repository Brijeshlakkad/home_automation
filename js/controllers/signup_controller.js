myApp.controller("SignupController", function($scope, $http, $window, $localStorage, $sessionStorage, $ocLazyLoad) {
  //$ocLazyLoad.load('js/meanmenu/jquery.meanmenu.js');
  if ($localStorage.userID != null && $localStorage.user != null && $localStorage.userType != null) {
    if($localStorage.userType=="customer"){
      $window.location.href = "#!customer/home";
    }else if($localStorage.userType=="dealer"){
      $window.location.href = "#!dealer/home";
    }else if($localStorage.userType=="admin"){
      $window.location.href = "#!admin/home";
    }
  }
  $scope.signupStatus = "";
  $scope.signup_status = function() {
    $http({
      method: "POST",
      url: "signup_data.php",
      data: "email=" + $scope.s_email + "&password=" + $scope.s_password + "&name=" + $scope.s_name + "&address=" + $scope.s_address + "&city=" + $scope.s_city + "&contact=" + $scope.s_contact,
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      }
    }).then(function mySuccess(response) {
      flag = response.data;
      // we should be using flag in only this block so logic in following
      if (!flag.error) {
        $scope.signupStatus = flag.message;
        $scope.s_status_0 = false;
        $scope.s_status_1 = true;
      } else {
        $scope.signupStatus = "";
        $scope.s_status_1 = false;
        $scope.s_status_0 = true;
      }
    }, function myError(response) {

    });
  };
});
