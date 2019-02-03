myApp.controller("EditProductController", function($rootScope, $scope, $http, $window, $sce, $timeout, $routeParams, $cookies, $ocLazyLoad) {
  $scope.productName = $routeParams.productName;
});
