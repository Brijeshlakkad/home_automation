myApp.controller("DistributorCustomerController", function($rootScope, $scope, $http, $window, $sce, $timeout, $cookies, $routeParams, $ocLazyLoad) {
  $ocLazyLoad.load(['js/meanmenu/jquery.meanmenu.js', 'js/data-table/jquery.dataTables.min.js', 'js/data-table/data-table-act.js', 'js/notification/bootstrap-growl.min.js', 'js/wow.min.js', 'js/main.js'], {
    rerun: true,
    cache: false
  });
  $scope.user = $rootScope.$storage.user;
  $scope.userID = $rootScope.$storage.userID;
  $scope.userType = $rootScope.$storage.userType;
  $scope.productList = [];
  $scope.selectedProduct = "";
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
        $scope.productList = data.d.product;
        $scope.selectedProduct = $scope.productList[0];
        $scope.numProductSerials = 0;
        $scope.changedProductSelected($scope.productList[0], id);
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
  $scope.changedProductSelected = function(product, dealerID) {
    var productID = product.id;
    $rootScope.body.addClass("loading");
    $http({
      method: "POST",
      url: "dealer_distributor/dealer_distributor_interface.php",
      data: "action=3&productID=" + productID + "&id=" + dealerID,
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
  $scope.sellProduct = function(customerEmail, selectedProductID, numProductSerials) {
    alert(customerEmail+"&"+numProductSerials+"&"+selectedProductID);
    var distributorID = $scope.userID;
    $rootScope.body.addClass("loading");
    $http({
      method: "POST",
      url: "dealer_distributor/dealer_distributor_interface.php",
      data: "action=7&customerEmail=" + customerEmail + "&productID=" + selectedProductID + "&numProductSerials=" + numProductSerials + "&distributorID=" + distributorID,
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      }
    }).then(function mySuccess(response) {
      var data = response.data;
      $rootScope.body.removeClass("loading");
      if (!data.error) {
        var message= data.responseMessage;
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
        $rootScope.customerDetail="";
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
            data: "action=6&customerEmail=" + value,
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
            }else{
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
