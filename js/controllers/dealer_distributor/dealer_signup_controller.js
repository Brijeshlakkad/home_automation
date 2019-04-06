myApp.controller("DealerSignupController", function($rootScope, $scope, $http, $window, $ocLazyLoad) {
  $rootScope.checkSessionData();
  $ocLazyLoad.load(['js/meanmenu/jquery.meanmenu.js', 'js/notification/bootstrap-growl.min.js', 'js/wow.min.js', 'js/main.js'], {
    rerun: true,
    cache: false
  });
  $scope.typeList = [{
      "key": "dealer",
      "value": "Dealer"
    },
    {
      "key": "distributor",
      "value": "Distributor"
    }
  ];
  $scope.s_type = $scope.typeList[0];
  $scope.signup_status = function() {
    $http({
      method: "POST",
      url: "dealer_distributor/signup_data.php",
      data: "email=" + $scope.s_email + "&password=" + $scope.s_password + "&name=" + $scope.s_name + "&address=" + $scope.s_address + "&city=" + $scope.s_city + "&contact=" + $scope.s_contact + "&type=" + $scope.s_type.key,
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
      $scope.signupStatus = "";
      $scope.s_status_1 = false;
      $scope.s_status_0 = true;
    });
  };
});
