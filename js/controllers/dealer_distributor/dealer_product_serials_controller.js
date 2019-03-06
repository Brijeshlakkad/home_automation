myApp.controller("DealerProductSerialsController", function($rootScope, $scope, $http, $window, $sce, $timeout, $cookies, $routeParams, $ocLazyLoad) {
  $ocLazyLoad.load(['js/meanmenu/jquery.meanmenu.js', 'js/data-table/jquery.dataTables.min.js', 'js/data-table/data-table-act.js', 'js/notification/bootstrap-growl.min.js'], {
    rerun: true,
    cache: false
  });
  $scope.user = $rootScope.$storage.user;
  $scope.userID = $rootScope.$storage.userID;
  $scope.userType = $rootScope.$storage.userType;
  $scope.productName = $routeParams.productName;
  $scope.productSerialArray=[];
  $scope.getProductSerials = function(id,productName){
    $rootScope.body.addClass("loading");
    $http({
      method: "POST",
      url: "dealer_distributor/dealer_distributor_interface.php",
      data: "action=1&id="+id+"&productName="+productName,
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      }
    }).then(function mySuccess(response) {
      $rootScope.body.addClass("loading");
      var data = response.data;
      if (!data.error) {
        if (data.d.productSerialRows > 0) {
          $scope.productSerialArray = data.d.productSerial;
        }
        $rootScope.body.removeClass("loading");
      } else {
        $rootScope.body.removeClass("loading");
        $rootScope.showErrorDialog(data.errorMessage);
      }
    }, function myError(response) {
      $rootScope.body.removeClass("loading");
    });
  }
  $scope.getProductSerials($scope.userID,$scope.productName);
});
