myApp.controller("HomePageController", function($rootScope, $scope, $http, $window, $sce, $timeout, $cookies, $localStorage, $sessionStorage, $ocLazyLoad) {
  $ocLazyLoad.load('home_page/vendor/jquery/jquery.min.js');
  if ($localStorage.userID != null && $localStorage.user != null && $localStorage.userType != null) {
    if($localStorage.userType=="customer"){
      $window.location.href = "#!customer/home";
    }else if($localStorage.userType=="dealer"){
      $window.location.href = "#!dealer_distributor/home";
    }else if($localStorage.userType=="admin"){
      $window.location.href = "#!admin/home";
    }
  }
});
