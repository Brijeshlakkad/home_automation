myApp.controller("userController",function($rootScope,$scope,$localStorage,$sessionStorage,$window,$location,$interval){
  $scope.index=['/customer','/customer/login','/customer/register','/dealer/login','/dealer/signup','/customer/forget_password','/dealer/forget_password','/admin/login'];
  $scope.path=0;
  var i;
  for(i=0;i<$scope.index.length;i++){
    if($location.path()==$scope.index[i]){
      $scope.path=1;
    }
  }
  if($localStorage.userID==null || $localStorage.user==null || $localStorage.userType==null){
    $rootScope.isLoggedIn=false;
    if($scope.path!=1){
      $window.location.href="#!/";
    }
  }else{
    alert($localStorage.userType);
    $rootScope.isLoggedIn=true;
  }
  $rootScope.$storage = $localStorage;
  $rootScope.logout=function(){
    $localStorage.$reset({
        userID: null,
        user: null,
        userType: null
    });
    $window.location.href="#!/";
    $rootScope.isLoggedIn=false;
  };
  var body=document.getElementsByTagName("BODY");
  $rootScope.body=angular.element(body);
});
