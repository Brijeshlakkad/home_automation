myApp.controller("HomePageController", function($rootScope, $scope, $http, $window, $sce, $timeout, $cookies, $localStorage, $sessionStorage, $ocLazyLoad) {
  $ocLazyLoad.load(['js/meanmenu/jquery.meanmenu.js','js/notification/bootstrap-growl.min.js','js/wow.min.js','js/main.js'], {
    rerun: true,
    cache: false
  });
  $rootScope.checkSessionData();
});
