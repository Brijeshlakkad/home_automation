myApp.controller("userController",function($rootScope,$scope,$localStorage,$sessionStorage,$window){
  if($localStorage.userID==null || $localStorage.user==null){
    $window.location.href="index.php";
  }
  $rootScope.$storage = $localStorage;
  $rootScope.logout=function(){
    $localStorage.$reset();
    $window.location.href="index.php";
  };
  var body=document.getElementsByTagName("BODY");
  $rootScope.body=angular.element(body);
});
