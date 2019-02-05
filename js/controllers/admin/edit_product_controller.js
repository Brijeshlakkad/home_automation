myApp.controller("EditProductController", function($rootScope, $scope, $http, $window, $sce, $timeout, $routeParams, $cookies, $ocLazyLoad) {
  $ocLazyLoad.load(['js/meanmenu/jquery.meanmenu.js','js/notification/bootstrap-growl.min.js'],{cache:false});
  $scope.productName = $routeParams.productName;
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
  $scope.editProduct = function(){
    if($rootScope.copyProduct.name!=$scope.product.name || $rootScope.copyProduct.s_rate!=$scope.product.s_rate || $rootScope.copyProduct.p_rate!=$scope.product.p_rate || $rootScope.copyProduct.description!=$scope.product.description || $rootScope.copyProduct.taxation!=$scope.product.taxation || $rootScope.copyProduct.hsncode!=$scope.product.hsncode || $rootScope.copyProduct.qty_name!=$scope.product.qty_name){
      $http({
        method: "POST",
        url: "admin/product_actions.php",
        data: "action=2&id="+$rootScope.copyProduct.id+"&name=" + $scope.product.name + "&s_rate=" + $scope.product.s_rate + "&p_rate=" + $scope.product.p_rate + "&description=" + $scope.product.description + "&taxation=" + $scope.product.taxation + "&hsncode=" + $scope.product.hsncode + "&qty_name=" + $scope.product.qty_name,
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        }
      }).then(function mySuccess(response) {
        var data = response.data;
        if (!data.error) {
          $window.location.href=data.product.location;
          $rootScope.openNotification($rootScope.dataFrom, $rootScope.dataAlign, $rootScope.dataIcon, $rootScope.dataType[0], $rootScope.dataAnimIn, $rootScope.dataAnimOut, "Added  ", "Product named " + data.product.name + " is created");
        } else {
          $rootScope.openNotification($rootScope.dataFrom, $rootScope.dataAlign, $rootScope.dataIcon, $rootScope.dataType[1], $rootScope.dataAnimIn, $rootScope.dataAnimOut, "Error  ", "Please, check entered product deatils or try again later");
        }
      }, function myError(response) {
        $rootScope.openNotification($rootScope.dataFrom, $rootScope.dataAlign, $rootScope.dataIcon, $rootScope.dataType[1], $rootScope.dataAnimIn, $rootScope.dataAnimOut, "Error  ", "Please, check entered product deatils or try again later");
      });
    }
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
