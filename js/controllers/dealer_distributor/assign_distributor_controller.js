myApp.controller("AssignDistributorController", function($rootScope, $scope, $http, $window, $sce, $timeout, $cookies, $routeParams, $ocLazyLoad) {
  $ocLazyLoad.load(['js/meanmenu/jquery.meanmenu.js', 'js/data-table/jquery.dataTables.min.js', 'js/data-table/data-table-act.js', 'js/notification/bootstrap-growl.min.js','js/wow.min.js','js/main.js'], {
    rerun: true,
    cache: false
  });
  $rootScope.checkSessionData();
  $scope.user = $rootScope.$storage.user;
  $scope.userID = $rootScope.$storage.userID;
  $scope.userType = $rootScope.$storage.userType;
  $scope.productList = [];
  $rootScope.maxNumProductSerials = 0;
  $rootScope.minNumProductSerials = 1;
  $rootScope.showBlock2 = false;
  $rootScope.showBlock3 = false;
  $rootScope.selectedProduct="";
  $scope.selectProductStyle = {
    "border-width": "1.45px",
    "border-color": "green"
  };
  $rootScope.dataFrom = undefined;
  $rootScope.dataAlign = undefined;
  $rootScope.dataIcon = undefined;
  $rootScope.dataType = {
    0: "success",
    1: "danger"
  };
  $rootScope.dataAnimIn = "animated bounceInRight";
  $rootScope.dataAnimOut = "animated bounceOutRight";
  $rootScope.openNotification = function(dataFrom, dataAlign, dataIcon, dataType, dataAnimIn, dataAnimOut, title, message) {
    notify(dataFrom, dataAlign, dataIcon, dataType, dataAnimIn, dataAnimOut, title, message);
  };
  $rootScope.getProductList = function(id) {
    $rootScope.body.addClass("loading");
    $http({
      method: "POST",
      url: "dealer_distributor/dealer_distributor_interface.php",
      data: "action=0&id=" + id,
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      }
    }).then(function mySuccess(response) {
      var data = response.data;
      if (!data.error) {
        if(data.d.productRows==0){
          $scope.productList=[];
        }
        else{
          $scope.productList = data.d.product;
          $rootScope.selectedProduct = $scope.productList[0];
          $scope.numProductSerials = 0;
          $scope.changedProductSelected($scope.productList[0]);
          $rootScope.showBlock3 = true;
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
  $scope.changedProductSelected = function(product) {
    $rootScope.selectedProduct=product;
    $rootScope.body.addClass("loading");
    $http({
      method: "POST",
      url: "dealer_distributor/dealer_distributor_interface.php",
      data: "action=3&productID=" + $rootScope.selectedProduct.id+ "&id=" + $scope.userID,
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      }
    }).then(function mySuccess(response) {
      var data = response.data;
      if (!data.error) {
        $rootScope.maxNumProductSerials = data.user.notAssigned;
        $rootScope.body.removeClass("loading");
      } else {
        $rootScope.maxNumProductSerials = 0;
        $rootScope.body.removeClass("loading");
        $rootScope.showErrorDialog(data.errorMessage);
      }
    }, function myError(response) {
      $scope.numProductSerials = 0;
      $rootScope.body.removeClass("loading");
    });
  };
  $scope.assignProductSerial = function(distributorEmail, selectedProductID, numProductSerials) {
    $rootScope.body.addClass("loading");
    $http({
      method: "POST",
      url: "dealer_distributor/dealer_distributor_interface.php",
      data: "action=5&soldToEmail=" + distributorEmail + "&productID=" + selectedProductID + "&numProductSerials=" + numProductSerials + "&userID=" + $scope.userID+"&userType="+$scope.userType,
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      }
    }).then(function mySuccess(response) {
      var data = response.data;
      $rootScope.body.removeClass("loading");
      if (!data.error) {
        var message = data.responseMessage;
        $rootScope.body.removeClass("loading");
        $window.location.href = data.user.location;
        $rootScope.showSuccessDialog(message);
      } else {
        $rootScope.body.removeClass("loading");
        $rootScope.showErrorDialog(data.errorMessage);
      }
    }, function myError(response) {
      $rootScope.body.removeClass("loading")
    });
  };
  $scope.assignProductSerialUsingSerial = function(distributorEmail, selectedProductID, productSerial) {
    $rootScope.body.addClass("loading");
    $http({
      method: "POST",
      url: "dealer_distributor/dealer_distributor_interface.php",
      data: "action=11&soldToEmail=" + distributorEmail + "&productID=" + selectedProductID + "&productSerial=" + productSerial + "&userID=" + $scope.userID + "&userType=" + $scope.userType,
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      }
    }).then(function mySuccess(response) {
      var data = response.data;
      $rootScope.body.removeClass("loading");
      if (!data.error) {
        var message = data.responseMessage;
        $rootScope.body.removeClass("loading");
        $window.location.href = data.user.location;
        $rootScope.showSuccessDialog(message);
      } else {
        $rootScope.body.removeClass("loading");
        $rootScope.showErrorDialog(data.errorMessage);
      }
    }, function myError(response) {
      $rootScope.body.removeClass("loading")
    });
  };
});
myApp.directive("distributorEmailExistsDir", function($rootScope, $http) {
  return {
    require: "ngModel",
    link: function(scope, element, attr, mCtrl) {
      function myValidation(value) {
        mCtrl.$setValidity('distributorEmailExists', true);
        var patt = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
        if (patt.test(value)) {
          mCtrl.$setValidity('emailValid', true);
          $http({
            method: "POST",
            url: "dealer_distributor/dealer_distributor_interface.php",
            data: "action=2&distributorEmail=" + value,
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded'
            }
          }).then(function mySuccess(response) {
            var data = response.data;
            if (!data.error) {
              if (data.user.distributorEmailExists) {
                $rootScope.getProductList($rootScope.$storage.userID);
                $rootScope.distributorName = data.user.distributorName;
                $rootScope.showBlock2 = true;
                mCtrl.$setValidity('distributorEmailExists', true);
                element.css({
                  "border-width": "1.45px",
                  "border-color": 'green'
                });
              } else {
                mCtrl.$setValidity('distributorEmailExists', false);
                element.css({
                  "border-width": "1.45px",
                  "border-color": 'red'
                });
              }
            } else {
              mCtrl.$setValidity('distributorEmailExists', false);
              element.css({
                "border-width": "1.45px",
                "border-color": 'red'
              });
            }
          }, function myError(response) {
            mCtrl.$setValidity('distributorEmailExists', false);
            element.css({
              "border-width": "1.45px",
              "border-color": 'red'
            });
          });
        } else {
          mCtrl.$setValidity('emailValid', false);
          element.css({
            "border-width": "1.45px",
            "border-color": 'red'
          });
        }
        return value;
      }
      mCtrl.$parsers.push(myValidation);
    }
  };
});
myApp.directive("rangeNumberDir", function($rootScope, $http) {
  return {
    require: "ngModel",
    link: function(scope, element, attr, mCtrl) {
      function myValidation(value) {
        if (value >= $rootScope.minNumProductSerials && value <= $rootScope.maxNumProductSerials) {
          mCtrl.$setValidity('rangeNumberValid', true);
          element.css({
            "border-width": "1.45px",
            "border-color": 'green'
          });
        } else {
          mCtrl.$setValidity('rangeNumberValid', false);
          element.css({
            "border-width": "1.45px",
            "border-color": 'red'
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
