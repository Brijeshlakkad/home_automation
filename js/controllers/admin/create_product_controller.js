myApp.controller("CreateProductController", function($rootScope, $scope, $http, $window, $sce, $timeout, $cookies, $ocLazyLoad) {
  $ocLazyLoad.load(['js/meanmenu/jquery.meanmenu.js', 'js/data-table/jquery.dataTables.min.js', 'js/data-table/data-table-act.js', 'js/notification/bootstrap-growl.min.js','js/wow.min.js','js/main.js'], {
    rerun: true,
    cache: false
  });
  $scope.user = $rootScope.$storage.user;
  $scope.userID = $rootScope.$storage.userID;
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
  $scope.addProduct = function() {
    $http({
      method: "POST",
      url: "admin/product_actions.php",
      data: "action=1&name=" + $scope.name + "&s_rate=" + $scope.s_rate + "&p_rate=" + $scope.p_rate + "&description=" + $scope.description + "&taxation=" + $scope.taxation + "&hsncode=" + $scope.hsncode + "&qty_name=" + $scope.qty_name,
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
  };
});

myApp.directive('productExistsDir', function($rootScope,$http) {
  return {
    require: 'ngModel',
    link: function(scope, element, attr, mCtrl) {
      function myValidation(value) {
        var form = document.getElementsByTagName('form');
        var formAng = angular.element(form);
        var id = formAng.attr("name");
        mCtrl.$setValidity('productNameExists', true);
        var productNamePattern = /^([a-zA-Z0-9])+(([\s]{1})([a-zA-Z0-9])+)?$/;
        if (productNamePattern.test(value)) {
          mCtrl.$setValidity('productNameValid', true);
          if(id=="editProductForm"){
            var beforeName=$rootScope.copyProduct.name;
            if(beforeName==value){
              mCtrl.$setValidity('productNameExists', true);
              return value;
            }
          }
          $http({
            method: "POST",
            url: "admin/product_actions.php",
            data: "action=4&productName=" + value,
            headers: {
              "Content-Type": 'application/x-www-form-urlencoded'
            }
          }).then(function mySuccess(response) {
            var data = response.data;
            if (data.error || data.product.productNameExists) {
              mCtrl.$setValidity('productNameExists', false);
              element.css({
                "border-bottom-width": "1.45px",
                "border-bottom-color": 'red'
              });
            } else {
              mCtrl.$setValidity('productNameExists', true);
              element.css({
                "border-bottom-width": "1.45px",
                "border-bottom-color": 'green'
              });
            }
          }, function myErrr(response) {
            mCtrl.$setValidity('productNameExists', false);
            element.css({
              "border-bottom-width": "1.45px",
              "border-bottom-color": 'red'
            });
          });
        } else {
          mCtrl.$setValidity('productNameValid', false);
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

// myApp.directive('a', function() {
//     return {
//         restrict: 'E',
//         link: function(scope, elem, attrs) {
//             if(attrs.ngClick || attrs.href === '' || attrs.href === '#'){
//                 elem.on('click', function(e){
//                     e.preventDefault();
//                 });
//             }
//         }
//    };
// });
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
