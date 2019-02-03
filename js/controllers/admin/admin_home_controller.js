myApp.controller("AdminHomeController", function($rootScope, $scope, $http, $window, $sce, $timeout, $cookies, $ocLazyLoad) {
  $ocLazyLoad.load('js/meanmenu/jquery.meanmenu.js');
  $ocLazyLoad.load('js/data-table/jquery.dataTables.min.js');
  $ocLazyLoad.load('js/data-table/data-table-act.js');
  //$scope.showAllRoom = $sce.trustAsHtml(showAllRoom);
  $scope.productArray=[];
  $scope.showNothing="";
  $scope.getAllProducts=function(){
    $rootScope.body.addClass("loading");
    $http({
      method: "POST",
      url: "admin/product_actions.php",
      data: "action=0",
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      }
    }).then(function mySuccess(response) {
      var data = response.data;
      if (!data.error) {
        if(data.user.totalRows>0){
          $scope.productArray=data.user.product;
        }else{
          alert(data.user.showNothing+"");
          $scope.showNothing=$sce.trustAsHtml(data.user.showNothing);
        }
        $rootScope.body.removeClass("loading");
      } else {
        $rootScope.body.removeClass("loading");
        $rootScope.showErrorDialog(data.errorMessage);
      }
    }, function myError(response) {
      $scope.showAddRoom = false;
      $rootScope.body.removeClass("loading");
    });
  };
  $scope.getAllProducts();

});
