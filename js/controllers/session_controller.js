myApp.controller("userController",function($rootScope,$scope,$localStorage,$sessionStorage,$window){
  if($localStorage.userID==null || $localStorage.user==null){
    $rootScope.isLoggedIn=false;
    $window.location.href="#!/";
  }else{
    $rootScope.isLoggedIn=true;
  }
  $rootScope.$storage = $localStorage;
  $rootScope.logout=function(){
    $localStorage.$reset({
        userID: null,
        user: null
    });
    $window.location.href="#!/";
    $rootScope.isLoggedIn=false;
  };
  var body=document.getElementsByTagName("BODY");
  $rootScope.body=angular.element(body);
});
