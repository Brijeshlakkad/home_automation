myApp.controller("RoomController", function($rootScope, $scope, $http, $window, $sce, $timeout, $cookies, $routeParams, $ocLazyLoad) {
  $ocLazyLoad.load('js/meanmenu/jquery.meanmenu.js');
  $scope.user = $rootScope.$storage.user;
  $scope.userID = $rootScope.$storage.userID;
  $scope.showAddRoom = false;
  $scope.roomReName = "";
  $scope.beforeRoomName = "";
  $scope.homeID = $routeParams.homeID;
  $scope.roomID = "";
  $rootScope.roomList = "";
  $scope.addRoom = function() {
    $rootScope.body.addClass("loading");
    $http({
      method: "POST",
      url: "room_actions.php",
      data: "action=1&email=" + $scope.user + "&roomName=" + $scope.roomName + "&homeName=" + $scope.homeID + "&userID=" + $scope.userID,
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      }
    }).then(function mySuccess(response) {
      var data = response.data;
      $scope.roomNameForm.$setPristine();
      $scope.roomName = "";
      if (!data.error) {
        $rootScope.showSuccessDialog("Room Created");
        $scope.getAllRoom();
        $rootScope.body.removeClass("loading");
      } else {
        $rootScope.body.removeClass("loading");
        $rootScope.showErrorDialog(data.errorMessage);
      }
    }, function myError(response) {
      $rootScope.body.removeClass("loading");
    });
  };
  $scope.getRoomList = function() {
    $http({
      method: "POST",
      url: "room_actions.php",
      data: "action=4&email=" + $scope.user + "&homeName=" + $scope.homeID + "&userID=" + $scope.userID,
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      }
    }).then(function mySuccess(response) {
      var data = response.data;
      if (!data.error) {
        if (typeof data.user.room == 'undefined') {
          $rootScope.roomList = [];
        } else {
          $rootScope.roomList = data.user.room;
        }
      } else {}
    }, function myError(response) {

    });
  };

  $scope.getAllRoom = function() {
    $rootScope.body.addClass("loading");
    $http({
      method: "POST",
      url: "room_actions.php",
      data: "action=0&email=" + $scope.user + "&homeName=" + $scope.homeID + "&userID=" + $scope.userID,
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      }
    }).then(function mySuccess(response) {
      var data = response.data;
      if (!data.error) {
        var showAllRoom = data.user.allRoom;
        $scope.showAllRoom = $sce.trustAsHtml(showAllRoom);
        $scope.getRoomList();
        $scope.showAddRoom = true;
        $rootScope.body.removeClass("loading");
      } else {
        $scope.showAddRoom = false;
        $rootScope.body.removeClass("loading");
        $rootScope.showErrorDialog(data.errorMessage);
      }
    }, function myError(response) {
      $scope.showAddRoom = false;
      $rootScope.body.removeClass("loading");
    });
  };
  $scope.getAllRoom();
  $scope.deleteRoom = function(val) {
    swal({
      title: "Are you sure?",
      text: "You will not be able to recover this room!",
      type: "warning",
      showCancelButton: true,
      confirmButtonText: "Yes, delete it!",
    }).then(function() {
      $rootScope.body.addClass("loading");
      $http({
        method: "POST",
        url: "room_actions.php",
        data: "action=2&email=" + $scope.user + "&id=" + val,
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        }
      }).then(function mySuccess(response) {
        $rootScope.body.removeClass("loading");
        var data = response.data;
        if (!data.error) {
          swal("Deleted!", "Your room has been deleted.", "success");
          $scope.getAllRoom();
        } else {
          $rootScope.showErrorDialog(data.errorMessage);
        }
      });
    });
  };
  $scope.setRoomName = function(id, roomName, callback) {
    $scope.beforeRoomName = roomName;
    $scope.roomReName = roomName;
    $scope.roomID = id;
    $timeout(callback, 10);
  };
  $scope.editRoom = function(id, roomName) {
    $scope.setRoomName(id, roomName, function() {
      $("#renameRoom").modal("show");
    });
  };

  $scope.modifyRoom = function() {
    if ($scope.beforeRoomName != $scope.roomReName) {
      $rootScope.body.addClass("loading");
      $http({
        method: "POST",
        url: "room_actions.php",
        data: "action=3&email=" + $scope.user + "&roomName=" + $scope.roomReName + "&id=" + $scope.roomID,
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        }
      }).then(function mySuccess(response) {
        var data = response.data;
        $scope.roomReNameForm.$setPristine();
        $scope.beforeRoomName = "";
        $scope.roomReName = "";
        if (!data.error && (typeof data.error != 'undefined')) {
          $rootScope.showSuccessDialog("Room Name Modified");
          $scope.getAllRoom();
          $rootScope.body.removeClass("loading");
        } else {
          $rootScope.body.removeClass("loading");
          $rootScope.showErrorDialog(data.errorMessage);
        }
      }, function myError(response) {
        $rootScope.body.removeClass("loading");
      });
    }
  };
});

function deleteRoom(id) {
  angular.element($("#roomModificationCtrl")).scope().deleteRoom(id);
}

function editRoom(id, roomName) {
  angular.element($("#roomModificationCtrl")).scope().editRoom(id, roomName);
}
myApp.directive("roomNameDir", function($rootScope, $http) {
  return {
    require: 'ngModel',
    link: function(scope, element, attr, mCtrl) {
      function myValidation(value) {
        mCtrl.$setValidity('roomNameExistsValid', true);
        var pattRoom = /^([a-zA-Z]+)([0-9]*)$/;
        var i, flag = 0;
        var len = $rootScope.roomList.length;
        if (pattRoom.test(value) && (value.length > 2 || value.length <= 8)) {
          mCtrl.$setValidity('roomNameValid', true);
          mCtrl.$setValidity('roomNameLenValid', true);
          for (i = 0; i < len; i++) {
            if ($rootScope.roomList[i].roomName == value) {
              flag = 1;
            }
          }
          if (flag == 1) {
            mCtrl.$setValidity('roomNameExistsValid', false);
            element.css({
              "border-bottom-width": "1.45px",
              "border-bottom-color": 'red'
            });
          } else {
            mCtrl.$setValidity('roomNameExistsValid', true);
            element.css({
              "border-bottom-width": "1.45px",
              "border-bottom-color": 'green'
            });
          }
        } else if (!pattHome.test(value) && (value.length > 2 || value.length <= 8)) {
          mCtrl.$setValidity('roomNameValid', false);
          mCtrl.$setValidity('roomNameLenValid', true);
          element.css({
            "border-bottom-width": "1.45px",
            "border-bottom-color": 'red'
          });
        } else if (pattHome.test(value) && (value.length <= 2 || value.length > 8)) {
          mCtrl.$setValidity('roomNameValid', true);
          mCtrl.$setValidity('roomNameLenValid', false);
          element.css({
            "border-bottom-width": "1.45px",
            "border-bottom-color": 'red'
          });
        } else {
          mCtrl.$setValidity('roomNameValid', false);
          mCtrl.$setValidity('roomNameLenValid', false);
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
