myApp.controller("AdminController",function($rootScope,$scope,$http,$window,$sce,$timeout,$cookies) {
  $scope.user=$rootScope.$storage.user;
  $scope.userID=$rootScope.$storage.userID;
});
