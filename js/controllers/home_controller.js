myApp.controller("HomeController",function($rootScope,$scope,$http,$window,$sce,$timeout,$cookies) {
  $scope.user=$rootScope.$storage.user;
  $scope.homeReName="";
  $scope.beforeHomeName="";
  $scope.homeID="";
  $rootScope.homeList="";
  $scope.userID=$rootScope.$storage.userID;
  $scope.homeNameStyle={
    "border-bottom-width":"2px"
  };
  $scope.analyzeHomeName=function(val){
    var patt_home=new RegExp("^[a-zA-Z0-9]+$");
    if(patt_home.test(val) && val.length>3){
      $scope.homeNameStyle['border-bottom-color']="green";
    }else{
      $scope.homeNameStyle['border-bottom-color']="red";
    }
  };
  $scope.addHome=function(){
    $http({
      method: "POST",
      url: "home_actions.php",
      data: "action=1&email="+$scope.user+"&homeName="+$scope.homeName,
      headers: {'Content-Type': 'application/x-www-form-urlencoded'}
    }).then(function mySuccess(response){
      var data=response.data;
      $scope.homeNameForm.$setPristine();
      $scope.homeName="";
      if(!data.error){
        $scope.showSuccessDialog("Home Created");
        $scope.getAllHome();
      }else{
        $scope.showErrorDialog(data.errorMessage);
      }
    },function myError(response){

    });
  };
  $scope.getHomeList=function(){
    $http({
      method: "POST",
      url: "home_actions.php",
      data: "action=4&email="+$scope.user,
      headers: {'Content-Type': 'application/x-www-form-urlencoded'}
    }).then(function mySuccess(response){
      var data=response.data;
      if(!data.error){
        $rootScope.homeList=data.user.home;
      }else{
      }
    },function myError(response){

    });
  };
  $scope.getHomeList();
  $scope.showErrorDialog=function(error){
    swal({
     title: "Try Again!",
     text: ""+error,
     timer: 2000
   });
  };
  $scope.showSuccessDialog=function(val){
    swal(""+val, "", "success");
  };
  $scope.getAllHome=function(){
    $http({
      method: "POST",
      url: "home_actions.php",
      data: "action=0&email="+$scope.user,
      headers: {'Content-Type': 'application/x-www-form-urlencoded'}
    }).then(function mySuccess(response){
      var data=response.data;
      if(!data.error){
        var showAllHome=data.user.allHome;
        $scope.showAllHome=$sce.trustAsHtml(showAllHome);
        $scope.getHomeList();
      }else{
        $scope.showErrorDialog(data.errorMessage);
      }
    },function myError(response){

    });
  };
  $scope.getAllHome();
  $scope.deleteHome = function(val){
    swal({
      title: "Are you sure?",
      text: "You will not be able to recover this home!",
      type: "warning",
      showCancelButton: true,
      confirmButtonText: "Yes, delete it!",
    }).then(function(){
      $http({
        method: "POST",
        url: "home_actions.php",
        data: "action=2&email="+$scope.user+"&id="+val,
        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
      }).then(function mySuccess(response){
        var data=response.data;
        if(!data.error){
          swal("Deleted!", "Your home has been deleted.", "success");
          $scope.getAllHome();
        }else{
          $scope.showErrorDialog(data.errorMessage);
        }
      });
    });
  };
  $scope.setHomeName=function(id,homeName,callback){
    $scope.beforeHomeName=homeName;
    $scope.homeReName=homeName;
    $scope.homeID=id;
    $timeout(callback,10);
  };
  $scope.editHome = function(id,homeName){
    $scope.setHomeName(id,homeName,function(){
      $("#renameHome").modal("show");
    });
  };

  $scope.modifyHome=function(){
    if($scope.beforeHomeName!=$scope.homeReName){
      $http({
        method: "POST",
        url: "home_actions.php",
        data: "action=3&email="+$scope.user+"&homeName="+$scope.homeReName+"&id="+$scope.homeID,
        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
      }).then(function mySuccess(response){
        var data=response.data;
        $scope.homeReNameForm.$setPristine();
        $scope.beforeHomeName="";
        $scope.homeReName="";
        if(!data.error && (typeof data.error != 'undefined')){
          $scope.showSuccessDialog("Home Name Modified");
          $scope.getAllHome();
        }else{
          $scope.showErrorDialog(data.errorMessage);
        }
      },function myError(response){

      });
    }
  };

  $scope.gotoHome = function(id){
    $cookies.remove('homeID');
    $cookies.put('homeID',id);
    $window.location.href="room.php";
  };

});
function deleteHome(id){
  angular.element($("#homeModificationCtrl")).scope().deleteHome(id);
}
function editHome(id,homeName){
  angular.element($("#homeModificationCtrl")).scope().editHome(id,homeName);
}
function gotoHome(id){
  angular.element($("#homeModificationCtrl")).scope().gotoHome(id);
}
myApp.directive("homeNameDir",function($rootScope,$http){
  return{
    require: 'ngModel',
    link: function(scope, element, attr, mCtrl){
      function myValidation(value){
        var patt_home=/^[a-zA-Z0-9]+$/;
        if(patt_home.test(value)){
          mCtrl.$setValidity('homeNameValid',true);
        }else{
          mCtrl.$setValidity('homeNameValid',false);
        }
        if(value.length>3){
          mCtrl.$setValidity('homeNameLenValid',true);
        }else{
          mCtrl.$setValidity('homeNameLenValid',false);
        }
        var i,flag=0;
        var len=$rootScope.homeList.length;
        for(i=0;i<len;i++){
          if($rootScope.homeList[i].homeName==value){
            flag=1;
          }
        }
        if(flag==1){
          mCtrl.$setValidity('homeNameExistsValid',false);
        }else{
          mCtrl.$setValidity('homeNameExistsValid',true);
        }
        return value;
      }
      mCtrl.$parsers.push(myValidation);
    }
  };
});
