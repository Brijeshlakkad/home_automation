myApp.controller("ProductSerialController", function($rootScope, $scope, $http, $window, $sce, $timeout, $cookies, $routeParams, $ocLazyLoad) {
  $ocLazyLoad.load(['js/meanmenu/jquery.meanmenu.js', 'js/data-table/jquery.dataTables.min.js', 'js/data-table/data-table-act.js', 'js/notification/bootstrap-growl.min.js'], {
    cache: false
  });
  $scope.isSearchBoxOn = false;
  $scope.productName = $routeParams.productName;
  $scope.productSerialArray = [];
  $scope.searchProductSerial = {};
  $rootScope.modelInp = {};
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
      $scope.isSearchBoxOn = false;
      var data = response.data;
      if (!data.error) {
        if (data.user.totalRows > 0) {
          $scope.assignedSerials = data.user.assigned;
          $scope.notAssignedSerials = data.user.notAssigned;
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
  // $scope.checkAlreadyHas=function(duplicateSeries,modelArray){
  //   var i=0;
  //   for(i=0;i<duplicateSeries.length;i++){
  //     if(duplicateSeries[i].productSeries)
  //   }
  // }
  $scope.applyDuplicateValidation=function(duplicateSeries){
    //var input = angular.element($("input[name='"+$scope.inputArray[duplicateSeries[0].location[0]]+"']"));
    var name,i,j;
    for(i=0;i<duplicateSeries.length;i++){
      for(j=0;j<duplicateSeries[i].location.length;j++){
        name=$scope.inputArray[duplicateSeries[i].location[j]];
        $scope.productSerialForm[name+""].$setValidity('duplicateExists', false);
        var element=angular.element($("input[name='"+name+"']"));
        element.css({
          "border-bottom-width": "1.45px",
          "border-bottom-color": 'red'
        });
      }
    }
    return true;
  };
  $scope.checkDuplicate=function(modelArray){
    var i=0,j=0,k=0,m,len,key,flag=0,flag1=0;
    var duplicateSeries=[];
    for(i=0;i<modelArray.length;i++){
      for(j=0;j<modelArray.length;j++){
        if(i!=j && modelArray[i]==modelArray[j]){
          duplicateSeries[k]={};
          flag1=-99;
          len=duplicateSeries.length;
          for(n=0;n<len;n++){
            if(duplicateSeries[n]['name']==modelArray[j]){
              flag1=n;
            }
          }
          //$scope.checkAlreadyHas(duplicateSeries,modelArray[i]);
          //duplicateSeries[k]={"productSeries":modelArray[j],"location1":i,"location2":j};
          if(flag1==-99){
            duplicateSeries[k].name=modelArray[j];
            duplicateSeries[k].location=[i,j];
            k++;
          }else{
            flag=0;
            len = duplicateSeries[flag1]['location'].length;
            for(m=0;m<len;m++){
              if(duplicateSeries[flag1]['location'][m]==j){
                flag=1;
              }
            }
            if(flag==0){
              duplicateSeries[flag1]['location'][len]=j;
            }
          }
        }
      }
    }
    if(duplicateSeries.length==0){
      return false;
    }
    $scope.applyDuplicateValidation(duplicateSeries);
  };
  // var aa=['1212','121212','1212'];
  // $scope.checkDuplicate(aa);
  $scope.submitProductSerials = function() {
    var i = 0;
    var modelArray = [];
    for (key in $scope.modelInp) {
      if ($scope.modelInp.hasOwnProperty(key)) {
        modelArray[i] = $scope.modelInp[key];
        i++;
      }
    }
    if($scope.checkDuplicate(modelArray)){
      return;
    }
    $("#openInputModal").modal("hide");
    $http({
      method: "POST",
      url: "admin/product_actions.php",
      data: "action=8&productID=" + $scope.product.id + "&productSerialArray=" + JSON.stringify(modelArray),
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      }
    }).then(function mySuccess(response) {
      var data = response.data;
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
          $rootScope.openNotification($rootScope.dataFrom, $rootScope.dataAlign, $rootScope.dataIcon, $rootScope.dataType[0], $rootScope.dataAnimIn, $rootScope.dataAnimOut, "Duplicate Serial Numbers ", "" + failedStr);
        } else {
          $window.location.href="#!admin/home/"+$routeParams.productName;
          $rootScope.openNotification($rootScope.dataFrom, $rootScope.dataAlign, $rootScope.dataIcon, $rootScope.dataType[0], $rootScope.dataAnimIn, $rootScope.dataAnimOut, "Added  ", modelArray.length + " Product Serial numbers");
        }
        $scope.getAllProductSerials($scope.product.id);
      } else {
        $rootScope.openNotification($rootScope.dataFrom, $rootScope.dataAlign, $rootScope.dataIcon, $rootScope.dataType[1], $rootScope.dataAnimIn, $rootScope.dataAnimOut, "Error  ", "Please, check entered product deatils or try again later");
      }
    }, function myError(response) {
      $rootScope.openNotification($rootScope.dataFrom, $rootScope.dataAlign, $rootScope.dataIcon, $rootScope.dataType[1], $rootScope.dataAnimIn, $rootScope.dataAnimOut, "Error  ", "Please, check entered product deatils or try again later");
    });
  };
  // $scope.searchProductSerial = function(val) {
  //   $http({
  //     method: "POST",
  //     url: "admin/product_actions.php",
  //     data: "action=9&sProductSerial=" + val,
  //     headers: {
  //       'Content-Type': 'application/x-www-form-urlencoded'
  //     }
  //   }).then(function mySuccess(response) {
  //     var data = response.data;
  //     if (!data.error) {
  //       var list = data.product.list;
  //       $scope.autoSuggest = $sce.trustAsHtml(list);
  //     } else {
  //       $rootScope.showErrorDialog(data.errorMessage);
  //     }
  //   }, function myError(response) {});
  // };
  $scope.searchProductSerialFunc = function(val) {
    if(val==undefined || val==""){
      $scope.isSearchBoxOn = false;
      return;
    };
    $rootScope.body.addClass("loading");
    $http({
      method: "POST",
      url: "admin/product_actions.php",
      data: "action=9&sProductSerial=" + val,
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      }
    }).then(function mySuccess(response) {
      var data = response.data;
      if (!data.error) {
        $scope.isSearchBoxOn = true;
        $scope.searchProductSerial = $sce.trustAsHtml(data.user.searchProductSerial);
        $rootScope.body.removeClass("loading");
      } else {
        $scope.isSearchBoxOn = false;
        $rootScope.body.removeClass("loading");
        $rootScope.showErrorDialog(data.errorMessage);
      }
    }, function myError(response) {});
  };
  $scope.closeSearchBox=function(){
    $scope.isSearchBoxOn=false;
  };
});
myApp.directive("productSerialDir", function($rootScope, $http) {
  return {
    require: 'ngModel',
    link: function(scope, element, attr, mCtrl) {
      function myValidation(value) {
        mCtrl.$setValidity('duplicateExists', true);
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
myApp.directive('myEnter', function() {
  return function(scope, element, attrs) {
    element.bind("keydown keypress", function(event) {
      if (event.which === 13) {
        scope.$apply(function() {
          scope.$eval(attrs.myEnter);
        });
        event.preventDefault();
      }
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
