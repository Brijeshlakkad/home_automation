myApp.controller("HomePageController", function($rootScope, $scope, $http, $window, $sce, $timeout, $cookies, $localStorage, $sessionStorage, $ocLazyLoad) {
  $ocLazyLoad.load(['js/meanmenu/jquery.meanmenu.js','js/notification/bootstrap-growl.min.js','js/wow.min.js','js/main.js'], {
    rerun: true,
    cache: false
  });
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
