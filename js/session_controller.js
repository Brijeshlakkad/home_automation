myApp.controller("userController",function($rootScope,$localStorage,$sessionStorage){
  if($localStorage.userID==null || $localStorage.user==null){
    $window.location.href="index.php";
  }
  $rootScope.$storage = $localStorage;
});
