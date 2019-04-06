myApp.controller("ProductSoldController", function($rootScope, $scope, $http, $window, $sce, $timeout, $cookies, $routeParams, $ocLazyLoad) {
  $rootScope.checkSessionData();
  $ocLazyLoad.load(['js/meanmenu/jquery.meanmenu.js', 'js/data-table/jquery.dataTables.min.js', 'js/data-table/data-table-act.js', 'js/notification/bootstrap-growl.min.js', 'js/wow.min.js', 'js/main.js'], {
    rerun: true,
    cache: false
  });
  $scope.user = $rootScope.$storage.user;
  $scope.userID = $rootScope.$storage.userID;
  $scope.userType = $rootScope.$storage.userType;
  $scope.productSoldList = [];
  $scope.showTextFieldArray = [];
  $scope.getProductSoldList = function(id, type) {
    $rootScope.body.addClass("loading");
    $http({
      method: "POST",
      url: "dealer_distributor/dealer_distributor_interface.php",
      data: "action=8&id=" + id + "&type=" + type,
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      }
    }).then(function mySuccess(response) {
      var data = response.data;
      $scope.showTextFieldArray = [];
      if (!data.error) {
        if (data.user.totalRows == 0) {
          $scope.productSoldList = [];
        } else {
          $scope.productSoldList = data.user.productSold;
          for (i = 0; i < $scope.productSoldList.length; i++) {
            $scope.showTextFieldArray[i] = false;
          }
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
  $scope.getProductSoldList($scope.userID, $scope.userType);
  $scope.getShowingTextFieldIndex = function() {
    for (i = 0; i < $scope.showTextFieldArray.length; i++) {
      if ($scope.showTextFieldArray[i] == true) {
        return i;
      }
    }
    return -99;
  }
  $scope.changeToTextField = function(product, index) {
    var showingIndex = $scope.getShowingTextFieldIndex();
    if (showingIndex == -99) {
      $scope.changeCustomerEmailModel = product.soldToEmail;
      $scope.showTextFieldArray[index] = true;
    } else {
      $scope.showTextFieldArray[showingIndex] = false;
      $scope.changeCustomerEmailModel = product.soldToEmail;
      $scope.showTextFieldArray[index] = true;
    }
  };
  $scope.backToLabel = function(product, index, customerEmail) {
    $scope.showTextFieldArray[index] = false;
    if (product.soldToEmail != customerEmail) {
      $rootScope.body.addClass("loading");
      $http({
        method: "POST",
        url: "dealer_distributor/dealer_distributor_interface.php",
        data: "action=9&serialNo=" + product.serialNo + "&customerEmail=" + customerEmail + "&userID=" + $scope.userID + "&oldCustomerEmail=" + product.soldToEmail,
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        }
      }).then(function mySuccess(response) {
        var data = response.data;
        if (data.error == false) {
          $scope.getProductSoldList($scope.userID, $scope.userType);
          $rootScope.showSuccessDialog(data.responseMessage);
        } else {
          $scope.changeCustomerEmailModel = product.soldToEmail;
          $rootScope.showErrorDialog(data.errorMessage);
        }
        $rootScope.body.removeClass("loading");
      }, function myError(response) {
        $scope.changeCustomerEmailModel = product.soldToEmail;
        $rootScope.body.removeClass("loading");
      });
    } else {
      $rootScope.showSuccessDialog("Customer Email has been changed!");
    }
  }
});
myApp.directive("customerEmailDir", function() {
  return {
    require: "ngModel",
    link: function(scope, element, attr, mCtrl) {
      function myValidation(value) {
        var patt = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
        if (patt.test(value)) {
          mCtrl.$setValidity('emailValid', true);
          element.css({
            "border-width": "1.45px",
            "border-color": 'green'
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
