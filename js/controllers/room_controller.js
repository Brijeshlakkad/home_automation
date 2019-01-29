myApp.controller("RoomController", function($rootScope,$scope,$http,$window,$sce,$timeout,$cookies,$routeParams) {
  $scope.user=$rootScope.$storage.user;
  $scope.userID=$rootScope.$storage.userID;
  $scope.showAddRoom=false;
  $scope.roomReName="";
  $scope.beforeRoomName="";
  $scope.homeID=$routeParams.homeID;
  $scope.roomID="";
  $rootScope.roomList="";
  $scope.roomNameStyle={
    "border-bottom-width":"2px"
  };
  $scope.analyzeRoomName=function(val){
    var patt_room=new RegExp("^[a-zA-Z0-9]+$");
    if(patt_room.test(val) && val.length>3){
      $scope.roomNameStyle['border-bottom-color']="green";
    }else{
      $scope.roomNameStyle['border-bottom-color']="red";
    }
  };
  $scope.addRoom=function(){
    $http({
      method: "POST",
      url: "room_actions.php",
      data: "action=1&email="+$scope.user+"&roomName="+$scope.roomName+"&homeName="+$scope.homeID+"&userID="+$scope.userID,
      headers: {'Content-Type': 'application/x-www-form-urlencoded'}
    }).then(function mySuccess(response){
      var data=response.data;
      $scope.roomNameForm.$setPristine();
      $scope.roomName="";
      if(!data.error){
        $scope.showSuccessDialog("Room Created");
        $scope.getAllRoom();
      }else{
        $scope.showErrorDialog(data.errorMessage);
      }
    },function myError(response){

    });
  };
  $scope.getRoomList=function(){
    $http({
      method: "POST",
      url: "room_actions.php",
      data: "action=4&email="+$scope.user+"&homeName="+$scope.homeID+"&userID="+$scope.userID,
      headers: {'Content-Type': 'application/x-www-form-urlencoded'}
    }).then(function mySuccess(response){
      var data=response.data;
      if(!data.error){
        if(typeof data.user.room=='undefined'){
          $rootScope.roomList=[];
        }
        else{
          $rootScope.roomList=data.user.room;
        }
      }else{
      }
    },function myError(response){

    });
  };
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
  $scope.getAllRoom=function(){
    $http({
      method: "POST",
      url: "room_actions.php",
      data: "action=0&email="+$scope.user+"&homeName="+$scope.homeID+"&userID="+$scope.userID,
      headers: {'Content-Type': 'application/x-www-form-urlencoded'}
    }).then(function mySuccess(response){
      var data=response.data;
      if(!data.error){
        var showAllRoom=data.user.allRoom;
        $scope.showAllRoom=$sce.trustAsHtml(showAllRoom);
        $scope.getRoomList();
        $scope.showAddRoom=true;
      }else{
        $scope.showErrorDialog(data.errorMessage);
        $scope.showAddRoom=false;
      }
    },function myError(response){
      $scope.showAddRoom=false;
    });
  };
  $scope.getAllRoom();
  $scope.deleteRoom = function(val){
    swal({
      title: "Are you sure?",
      text: "You will not be able to recover this room!",
      type: "warning",
      showCancelButton: true,
      confirmButtonText: "Yes, delete it!",
    }).then(function(){
      $http({
        method: "POST",
        url: "room_actions.php",
        data: "action=2&email="+$scope.user+"&id="+val,
        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
      }).then(function mySuccess(response){
        var data=response.data;
        if(!data.error){
          swal("Deleted!", "Your room has been deleted.", "success");
          $scope.getAllRoom();
        }else{
          $scope.showErrorDialog(data.errorMessage);
        }
      });
    });
  };
  $scope.setRoomName=function(id,roomName,callback){
    $scope.beforeRoomName=roomName;
    $scope.roomReName=roomName;
    $scope.roomID=id;
    $timeout(callback,10);
  };
  $scope.editRoom = function(id,roomName){
    $scope.setRoomName(id,roomName,function(){
      $("#renameRoom").modal("show");
    });
  };

  $scope.modifyRoom=function(){
    if($scope.beforeRoomName!=$scope.roomReName){
      $http({
        method: "POST",
        url: "room_actions.php",
        data: "action=3&email="+$scope.user+"&roomName="+$scope.roomReName+"&id="+$scope.roomID,
        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
      }).then(function mySuccess(response){
        var data=response.data;
        $scope.roomReNameForm.$setPristine();
        $scope.beforeRoomName="";
        $scope.roomReName="";
        if(!data.error && (typeof data.error != 'undefined')){
          $scope.showSuccessDialog("Room Name Modified");
          $scope.getAllRoom();
        }else{
          $scope.showErrorDialog(data.errorMessage);
        }
      },function myError(response){

      });
    }
  };
});
function deleteRoom(id){
  angular.element($("#roomModificationCtrl")).scope().deleteRoom(id);
}
function editRoom(id,roomName){
  angular.element($("#roomModificationCtrl")).scope().editRoom(id,roomName);
}
myApp.directive("roomNameDir",function($rootScope,$http){
  return{
    require: 'ngModel',
    link: function(scope, element, attr, mCtrl){
      function myValidation(value){
        var patt_room=/^[a-zA-Z0-9]+$/;
        if(patt_room.test(value)){
          mCtrl.$setValidity('roomNameValid',true);
        }else{
          mCtrl.$setValidity('roomNameValid',false);
        }
        if(value.length>3){
          mCtrl.$setValidity('roomNameLenValid',true);
        }else{
          mCtrl.$setValidity('roomNameLenValid',false);
        }
        var i,flag=0;
        var len=$rootScope.roomList.length;
        for(i=0;i<len;i++){
          if($rootScope.roomList[i].roomName==value){
            flag=1;
          }
        }
        if(flag==1){
          mCtrl.$setValidity('roomNameExistsValid',false);
        }else{
          mCtrl.$setValidity('roomNameExistsValid',true);
        }
        return value;
      }
      mCtrl.$parsers.push(myValidation);
    }
  };
});
