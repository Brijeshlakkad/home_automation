myApp.controller("DistributorCustomerController", function($rootScope, $scope, $http, $window, $sce, $timeout, $cookies, $routeParams, $ocLazyLoad) {
  $rootScope.checkSessionData();
  $ocLazyLoad.load(['js/meanmenu/jquery.meanmenu.js', 'js/data-table/jquery.dataTables.min.js', 'js/data-table/data-table-act.js', 'js/notification/bootstrap-growl.min.js', 'js/wow.min.js', 'js/main.js'], {
    rerun: true,
    cache: false
  });
  $scope.user = $rootScope.$storage.user;
  $scope.userID = $rootScope.$storage.userID;
  $scope.userType = $rootScope.$storage.userType;
  $scope.productList = [];
  $rootScope.selectedProduct = "";
  $scope.numProductSerials = 0;
  $rootScope.maxNumProductSerials = 0;
  $rootScope.minNumProductSerials = 1;
  $scope.selectProductStyle = {
    "border-width": "1.45px",
    "border-color": "green"
  };
  $scope.getDistributorProductList = function(id) {
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
  $scope.getDistributorProductList($scope.userID);
  $scope.changedProductSelected = function(product) {
    $rootScope.selectedProduct = product;
    $rootScope.body.addClass("loading");
    $http({
      method: "POST",
      url: "dealer_distributor/dealer_distributor_interface.php",
      data: "action=3&productID=" + $rootScope.selectedProduct.id + "&id=" + $scope.userID,
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      }
    }).then(function mySuccess(response) {
      var data = response.data;
      if (!data.error) {
        if ($scope.usingQuantity == 1) {
          $scope.sellToCustomerSerialForm.$setPristine();
          $scope.productSerial = undefined;
        } else if ($scope.usingQuantity == 0) {
          $scope.sellToCustomerForm.$setPristine();
          $scope.quantity = undefined;
        }
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
  $scope.sellProduct = function(customerEmail, selectedProductID, numProductSerials) {
    var distributorID = $scope.userID;
    $rootScope.body.addClass("loading");
    $http({
      method: "POST",
      url: "dealer_distributor/dealer_distributor_interface.php",
      data: "action=5&soldToEmail=" + customerEmail + "&productID=" + selectedProductID + "&numProductSerials=" + numProductSerials + "&userID=" + $scope.userID + "&userType=" + $scope.userType,
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
  $scope.sellProductUsingSerial = function(customerEmail, selectedProductID, productSerial) {
    $rootScope.body.addClass("loading");
    $http({
      method: "POST",
      url: "dealer_distributor/dealer_distributor_interface.php",
      data: "action=11&soldToEmail=" + customerEmail + "&productID=" + selectedProductID + "&productSerial=" + productSerial + "&userID=" + $scope.userID + "&userType=" + $scope.userType,
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
myApp.directive("customerEmailExistsDir", function($rootScope, $http) {
  return {
    require: "ngModel",
    link: function(scope, element, attr, mCtrl) {
      function myValidation(value) {
        $rootScope.customerDetail = "";
        var patt = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
        if (patt.test(value)) {
          mCtrl.$setValidity('emailValid', true);
          element.css({
            "border-width": "1.45px",
            "border-color": 'green'
          });
          $http({
            method: "POST",
            url: "dealer_distributor/dealer_distributor_interface.php",
            data: "action=4&customerEmail=" + value,
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded'
            }
          }).then(function mySuccess(response) {
            var data = response.data;
            if (!data.error) {
              if (data.user.customerEmailExists) {
                $rootScope.customerDetail = data.user.customer.contact;
              } else {
                $rootScope.customerDetail = "Email is not registered with us.";
              }
            } else {
              $rootScope.customerDetail = "Try again!";
            }
          }, function myError(response) {
            $rootScope.customerDetail = "Server Load";
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
myApp.directive("productSerialExistsDir", function($rootScope, $http) {
  return {
    require: 'ngModel',
    link: function(scope, element, attr, mCtrl) {
      function myValidation(value) {
        mCtrl.$setValidity('productSerialExists', true);
        var pattSeries = /^([0-9]{4})([A-Z0-9]{4})([0-9]{4})$/;
        if (pattSeries.test(value)) {
          mCtrl.$setValidity('productSerialValid', true);
          if (typeof $rootScope.selectedProduct.id != 'undefined') {
            $http({
              method: "POST",
              url: "dealer_distributor/dealer_distributor_interface.php",
              data: "action=10&productSerial=" + value + "&productID=" + $rootScope.selectedProduct.id + "&userID=" + $rootScope.$storage.userID + "&userType=" + $rootScope.$storage.userType,
              headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
              }
            }).then(function mySuccess(response) {
              var data = response.data;
              if (data.user.productSerialExists == true) {
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
            mCtrl.$setValidity('productSerialExists', false);
            element.css({
              "border-bottom-width": "1.45px",
              "border-bottom-color": 'red'
            });
          }
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
