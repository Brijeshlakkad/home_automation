myApp.controller("SubscriptionController", function($rootScope, $scope, $http, $window, $localStorage, $sessionStorage, $ocLazyLoad) {
  $ocLazyLoad.load(['js/meanmenu/jquery.meanmenu.js','js/notification/bootstrap-growl.min.js','js/wow.min.js','js/main.js'], {
    rerun: true,
    cache: false
  });
  $scope.user = $rootScope.$storage.user;
  $scope.userID = $rootScope.$storage.userID;
  $scope.userType = $rootScope.$storage.userType;
  $scope.subscriptionArray = [];
  $scope.getSubscriptionDetails = function(id) {
    $rootScope.body.addClass("loading");
    $http({
      method: "POST",
      url: "customer_actions.php",
      data: "action=1&userID=" + id,
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      }
    }).then(function mySuccess(response) {
      var data = response.data;
      if (data.error == false) {
        if(data.user.totalRows>0){
          $scope.subscriptionArray = data.user.row;
        }else{
          $scope.subscriptionArray = [];
        }
      }
      $rootScope.body.removeClass("loading");
      return;
    }, function myError(response) {
      $rootScope.body.removeClass("loading");
      return;
    });
  };
  $scope.getSubscriptionDetails($scope.userID);
});
