myApp.controller("ProductSerialController", function($rootScope, $scope, $http, $window, $sce, $timeout, $cookies, $routeParams, $ocLazyLoad) {
  $ocLazyLoad.load(['js/meanmenu/jquery.meanmenu.js', 'js/data-table/jquery.dataTables.min.js', 'js/data-table/data-table-act.js', 'js/notification/bootstrap-growl.min.js'], {
    cache: false
  });
  $scope.productName = $routeParams.productName;
  $scope.productSerialArray = [];
  $scope.modelInp = {};
  $scope.inputArray = [];
  $scope.product = "";
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
  $scope.getProduct = function(productName) {
    $http({
      method: "POST",
      url: "admin/product_actions.php",
      data: "action=5&productName=" + productName,
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      }
    }).then(function mySuccess(response) {
      var data = response.data;
      if (!data.error) {
        $scope.product = data.product;
        $rootScope.copyProduct = angular.copy($scope.product);
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

  $scope.getAllProductSerials = function(productID) {
    $rootScope.body.addClass("loading");
    $http({
      method: "POST",
      url: "admin/product_actions.php",
      data: "action=6&productID=" + productID,
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      }
    }).then(function mySuccess(response) {
      var data = response.data;
      if (!data.error) {
        if (data.user.totalRows > 0) {
          $scope.productSerialArray = data.user.productSerial;
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
  $scope.openInputModal = function() {
    $scope.inputArray = [];
    if ($scope.isNumAllowed) {
      if ($scope.numSerials != undefined) {
        for (i = 0; i < $scope.numSerials; i++) {
          $scope.inputArray[i] = "productSerial" + i;
        }
      } else {
        $scope.inputArray[0] = "productSerial";
      }
    } else {
      $scope.inputArray[0] = "productSerial";
    }
    $("#openInputModal").modal("show");
  };
  $scope.submitProductSerials = function() {
    var i = 0;
    var modelArray = [];
    for (key in $scope.modelInp) {
      if ($scope.modelInp.hasOwnProperty(key)) {
        modelArray[i] = $scope.modelInp[key];
        i++;
      }
    }
    $http({
      method: "POST",
      url: "admin/product_actions.php",
      data: "action=8&productID=" + $scope.product.id + "&productSerialArray=" + JSON.stringify(modelArray),
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      }
    }).then(function mySuccess(response) {
      var data = response.data;
      alert(JSON.stringify(data));
      if (!data.error) {
        if (data.product.failed.error) {
          var len = data.product.failed.totalRows;
          var failedArray = data.product.failed.productFailed;
          var failedStr = "";
          for (i = 0; i <= len; i++) {
            if (i == len) {
              failedStr += failedArray[i] + "";
              break;
            }
            failedStr += failedArray[i] + ", ";
          }
          $rootScope.openNotification($rootScope.dataFrom, $rootScope.dataAlign, $rootScope.dataIcon, $rootScope.dataType[0], $rootScope.dataAnimIn, $rootScope.dataAnimOut, "Error  ", "in inserting product Serial numbers: " + failedStr);
        } else if (data.product.exists.error) {
          var len = data.product.exists.totalRows;
          var failedArray = data.product.exists.productFailed;
          var failedStr = "";
          for (i = 0; i <= len; i++) {
            if (i == len) {
              failedStr += failedArray[i] + "";
              break;
            }
            failedStr += failedArray[i] + ", ";
          }
          $rootScope.openNotification($rootScope.dataFrom, $rootScope.dataAlign, $rootScope.dataIcon, $rootScope.dataType[0], $rootScope.dataAnimIn, $rootScope.dataAnimOut, "Duplicate Serial Numbers ", ""+failedStr);
        } else {
          $rootScope.openNotification($rootScope.dataFrom, $rootScope.dataAlign, $rootScope.dataIcon, $rootScope.dataType[0], $rootScope.dataAnimIn, $rootScope.dataAnimOut, "Added  ", modelArray.length + " Product Serial numbers");
        }
      } else {
        $rootScope.openNotification($rootScope.dataFrom, $rootScope.dataAlign, $rootScope.dataIcon, $rootScope.dataType[1], $rootScope.dataAnimIn, $rootScope.dataAnimOut, "Error  ", "Please, check entered product deatils or try again later");
      }
    }, function myError(response) {
      $rootScope.openNotification($rootScope.dataFrom, $rootScope.dataAlign, $rootScope.dataIcon, $rootScope.dataType[1], $rootScope.dataAnimIn, $rootScope.dataAnimOut, "Error  ", "Please, check entered product deatils or try again later");
    });
  };
});
myApp.directive("productSerialDir", function($rootScope, $http) {
  return {
    require: 'ngModel',
    link: function(scope, element, attr, mCtrl) {
      function myValidation(value) {
        mCtrl.$setValidity('productSerialExists', true);
        var pattSeries = /^([0-9]{4})([A-Z0-9]{4})([0-9]{4})$/;
        if (pattSeries.test(value)) {
          mCtrl.$setValidity('productSerialValid', true);
          $http({
            method: "POST",
            url: "admin/product_actions.php",
            data: "action=7&productSerial=" + value,
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded'
            }
          }).then(function mySuccess(response) {
            var data = response.data;
            if (!data.product.productSerialExists) {
              mCtrl.$setValidity('productSerialExists', true);
              element.css({
                "border-bottom-width": "1.45px",
                "border-bottom-color": 'green'
              });
            } else {
              mCtrl.$setValidity('productSerialExists', false);
              element.css({
                "border-bottom-width": "1.45px",
                "border-bottom-color": 'red'
              });
            }
          }, function myError(response) {
            mCtrl.$setValidity('productSerialExists', false);
            element.css({
              "border-bottom-width": "1.45px",
              "border-bottom-color": 'red'
            });
          });
        } else {
          mCtrl.$setValidity('productSerialValid', false);
          element.css({
            "border-bottom-width": "1.45px",
            "border-bottom-color": 'red'
          });
        }
        return value;
      }
      mCtrl.$parsers.push(myValidation);
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
