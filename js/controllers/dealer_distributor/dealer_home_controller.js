myApp.controller("DealerHomeController", function($rootScope, $scope, $http, $window, $sce, $timeout, $cookies, $routeParams, $ocLazyLoad) {
  $ocLazyLoad.load(['js/meanmenu/jquery.meanmenu.js', 'js/data-table/jquery.dataTables.min.js', 'js/data-table/data-table-act.js', 'js/notification/bootstrap-growl.min.js','js/wow.min.js','js/main.js'], {
    rerun: true,
    cache: false
  });
  $rootScope.checkSessionData();
  $scope.user = $rootScope.$storage.user;
  $scope.userID = $rootScope.$storage.userID;
  $scope.userType = $rootScope.$storage.userType;
  $scope.productArray=[];
  $scope.getAllProducts = function(id) {
    $rootScope.body.addClass("loading");
    $http({
      method: "POST",
      url: "dealer_distributor/dealer_distributor_interface.php",
      data: "action=0&id="+id,
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      }
    }).then(function mySuccess(response) {
      var data = response.data;
      if (data.error==false) {
        if (data.d.productRows > 0) {
          $scope.productArray = data.d.product;
        }
        $rootScope.body.removeClass("loading");
      } else {
        $rootScope.body.removeClass("loading");
        $rootScope.showErrorDialog(data.errorMessage);
      }
    }, function myError(response) {
      $rootScope.body.removeClass("loading");
    });
  };
  $scope.getAllProducts($scope.userID);
  $scope.goToProduct= function(productName){
    $window.location.href="#!/dealer_distributor/home/"+productName;
  };
});
