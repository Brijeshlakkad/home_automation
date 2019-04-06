myApp.controller("AdminHomeController", function($rootScope, $scope, $http, $window, $sce, $timeout, $cookies, $ocLazyLoad) {
  $rootScope.checkSessionData();
  $ocLazyLoad.load(['js/meanmenu/jquery.meanmenu.js', 'js/data-table/jquery.dataTables.min.js', 'js/data-table/data-table-act.js', 'js/notification/bootstrap-growl.min.js','js/wow.min.js','js/main.js'], {
    rerun: true,
    cache: false
  });
  $scope.productArray = [];
  $scope.showNothing = "";
  $rootScope.copyProduct = "";
  $rootScope.dataFrom = undefined;
  $rootScope.dataAlign = undefined;
  $rootScope.dataIcon = undefined;
  $rootScope.dataType = {
    0: "success",
    1: "danger"
  };
  $rootScope.dataAnimIn = "animated bounceInRight";
  $rootScope.dataAnimOut = "animated bounceOutRight";
  $scope.getAllProducts = function() {
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
        if (data.user.totalRows > 0) {
          $scope.productArray = data.user.product;
        } else {
          $scope.showNothing = $sce.trustAsHtml(data.user.showNothing);
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
  $rootScope.openNotification = function(dataFrom, dataAlign, dataIcon, dataType, dataAnimIn, dataAnimOut, title, message) {
    notify(dataFrom, dataAlign, dataIcon, dataType, dataAnimIn, dataAnimOut, title, message);
  };
  $scope.deleteProduct = function(productName) {
    $http({
      method: "POST",
      url: "admin/product_actions.php",
      data: "action=3&productName=" + productName,
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      }
    }).then(function mySuccess(response) {
      var data = response.data;
      alert(JSON.stringify(data));
      if (!data.error) {
        $scope.getAllProducts();
        $rootScope.openNotification($rootScope.dataFrom, $rootScope.dataAlign, $rootScope.dataIcon, $rootScope.dataType[0], $rootScope.dataAnimIn, $rootScope.dataAnimOut, "Removed  ", "Product named " + productName + " is removed");
      } else {
        $rootScope.openNotification($rootScope.dataFrom, $rootScope.dataAlign, $rootScope.dataIcon, $rootScope.dataType[1], $rootScope.dataAnimIn, $rootScope.dataAnimOut, "Error  ", "Please, check entered product deatils or try again later");
      }
    }, function myError(response) {
      $rootScope.openNotification($rootScope.dataFrom, $rootScope.dataAlign, $rootScope.dataIcon, $rootScope.dataType[1], $rootScope.dataAnimIn, $rootScope.dataAnimOut, "Error  ", "Please, check entered product deatils or try again later");
    });
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
