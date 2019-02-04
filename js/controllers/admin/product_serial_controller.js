myApp.controller("ProductSerialController", function($rootScope, $scope, $http, $window, $sce, $timeout, $cookies, $ocLazyLoad) {
  $ocLazyLoad.load('js/meanmenu/jquery.meanmenu.js');
  $ocLazyLoad.load('js/data-table/jquery.dataTables.min.js');
  $ocLazyLoad.load('js/data-table/data-table-act.js');
  $ocLazyLoad.load('js/notify.js');
  $scope.productName = $routeParams.productName;
  $scope.productSerial=[];
  $scope.product="";
  $rootScope.copyProduct="";
  $rootScope.dataFrom = undefined;
  $rootScope.dataAlign = undefined;
  $rootScope.dataIcon = undefined;
  $rootScope.dataType = {
    0: "success",
    1: "danger"
  };
  $rootScope.dataAnimIn = "animated bounceInRight";
  $rootScope.dataAnimOut = "animated bounceOutRight";
  $scope.getProduct=function(productName){
    alert("s");
    $http({
      method: "POST",
      url: "admin/product_actions.php",
      data: "action=5&productName="+productName,
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      }
    }).then(function mySuccess(response) {
      var data = response.data;
      if (!data.error) {
        $scope.product=data.product;
        $rootScope.copyProduct=angular.copy($scope.product);
        $rootScope.body.removeClass("loading");
        $scope.getAllProductSerials($scope.product.id);
      } else {
        $rootScope.body.removeClass("loading");
        $rootScope.showErrorDialog(data.errorMessage);
      }
    }, function myError(response) {
      $scope.showAddRoom = false;
      $rootScope.body.removeClass("loading");
    });
  };
  $rootScope.openNotification = function(dataFrom, dataAlign, dataIcon, dataType, dataAnimIn, dataAnimOut, title, message) {
    notify(dataFrom, dataAlign, dataIcon, dataType, dataAnimIn, dataAnimOut, title, message);
  };
  $scope.getProduct($scope.productName);

  $scope.getAllProductSerials=function(productID){
    alert(productID);
  };
});
function notify(from, align, icon, type, animIn, animOut, title, message) {
  $.growl({
    icon: icon,
    title: title,
    message: message,
    url: ''
  }, {
    element: 'body',
    type: type,
    allow_dismiss: true,
    placement: {
      from: from,
      align: align
    },
    offset: {
      x: 20,
      y: 85
    },
    spacing: 10,
    z_index: 1031,
    delay: 2500,
    timer: 1000,
    url_target: '_blank',
    mouse_over: false,
    animate: {
      enter: animIn,
      exit: animOut
    },
    icon_type: 'class',
    template: '<div data-growl="container" class="alert" role="alert">' +
      '<button type="button" class="close" data-growl="dismiss">' +
      '<span aria-hidden="true">&times;</span>' +
      '<span class="sr-only">Close</span>' +
      '</button>' +
      '<span data-growl="icon"></span>' +
      '<span data-growl="title"></span>' +
      '<span data-growl="message"></span>' +
      '<a href="#" data-growl="url"></a>' +
      '</div>'
  });
};
