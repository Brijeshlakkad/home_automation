myApp.controller("SettingsController", function($rootScope, $scope, $http, $window, $sce, $timeout, $cookies, $ocLazyLoad) {
  $ocLazyLoad.load(['js/meanmenu/jquery.meanmenu.js', 'js/notification/bootstrap-growl.min.js', 'js/wow.min.js', 'js/main.js'], {
    rerun: true,
    cache: false
  });
  $scope.user = $rootScope.$storage.user;
  $scope.userID = $rootScope.$storage.userID;
  $scope.editStatus = "";
  $rootScope.userDetails = {};
  $scope.openTab = function(index, tabName) {
    var i, tabcontent, tablinks, content;

    tabcontent = document.getElementsByClassName("tabcontent");
    var wrappedContents = angular.element(tabcontent);
    wrappedContents.addClass("remove").removeClass("show");
    tablinks = document.getElementsByClassName("tablinks");
    var wrappedLinks = angular.element(tablinks);
    wrappedLinks.removeClass("active");

    content = document.getElementById(tabName + "");
    var wrappedContent = angular.element(content);
    wrappedContent.addClass("show").removeClass("remove");

    link = document.getElementById(index + "");
    var wrappedLink = angular.element(link);
    wrappedLink.addClass("active");
  };
  $scope.openTab('0', 'account_details');

  $scope.editDetails = function() {
    if ($rootScope.userDetails['name'] != $scope.s_name || $rootScope.userDetails['city'] != $scope.s_city || $rootScope.userDetails['address'] != $scope.s_address || $rootScope.userDetails['contact'] != $scope.s_contact) {
      $rootScope.body.addClass("loading");
      $http({
        method: "POST",
        url: "customer_interface.php",
        data: "action=1&email=" + $scope.s_email + "&name=" + $scope.s_name + "&address=" + $scope.s_address + "&city=" + $scope.s_city + "&contact=" + $scope.s_contact,
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        }
      }).then(function mySuccess(response) {
        $rootScope.body.removeClass("loading");
        flag = response.data;
        // we should be using flag in only this block so logic in following
        if (!flag.error) {
          $scope.editStatus = "Details Updated!";
          $rootScope.userDetails = flag.userUpdated;
          $scope.s_status_0 = false;
          $scope.s_status_1 = true;
        } else {
          $scope.s_status_1 = false;
          $scope.s_status_0 = true;
        }
      }, function myError(response) {
        $rootScope.body.removeClass("loading");
      });
    } else {
      $scope.editStatus = "Details has not been modified.";
      $scope.s_status_1 = true;
      $scope.s_status_0 = false;
    }
  };

  $scope.showErrorDialog = function(error) {
    swal({
      title: "Try Again!",
      text: "" + error,
      timer: 2000
    });
  };
  $scope.showSuccessDialog = function(val) {
    swal("" + val, "", "success");
  };

  $scope.get_user_details = function() {
    $http({
      method: "POST",
      url: "customer_interface.php",
      data: "action=0&userID=" + $scope.userID,
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      }
    }).then(function mySuccess(response) {
      var data = response.data;
      if (!data.error) {
        $scope.s_name = data.name;
        $scope.s_email = data.email;
        $scope.s_city = data.city;
        $scope.s_address = data.address;
        $scope.s_contact = data.contact;
        $rootScope.userDetails = data;
      } else {
        $scope.showErrorDialog(data.errorMessage);
      }
    }, function myError(response) {});
  };
  $scope.get_user_details();

  var strongRegex = new RegExp("^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{8,})");
  var mediumRegex = new RegExp("^(((?=.*[a-z])(?=.*[A-Z]))|((?=.*[a-z])(?=.*[0-9]))|((?=.*[A-Z])(?=.*[0-9])))(?=.{6,})");
  /*  $scope.old_passwordStrength = {
      "border-width":"1.45px"
    };
    $scope.analyze_old = function(value) {
        if(strongRegex.test(value)) {
            $scope.old_passwordStrength["border-color"] = "green";
        } else if(mediumRegex.test(value)) {
            $scope.old_passwordStrength["border-color"] = "orange";
        } else {
            $scope.old_passwordStrength["border-color"] = "red";
        }
    };*/
  $scope.new_passwordStrength = {
    "border-width": "1.45px"
  };
  $scope.analyze_new = function(value) {
    if (strongRegex.test(value)) {
      $scope.new_passwordStrength["border-color"] = "green";
    } else if (mediumRegex.test(value)) {
      $scope.new_passwordStrength["border-color"] = "orange";
    } else {
      $scope.new_passwordStrength["border-color"] = "red";
    }
  };
  $scope.cnew_passwordStrength = {
    "border-width": "1.45px"
  };
  $scope.analyze_cnew = function(value) {
    if (strongRegex.test(value)) {
      $scope.cnew_passwordStrength["border-color"] = "green";
    } else if (mediumRegex.test(value)) {
      $scope.cnew_passwordStrength["border-color"] = "orange";
    } else {
      $scope.cnew_passwordStrength["border-color"] = "red";
    }
  };
  $scope.showErrorDialog = function(error) {
    swal({
      title: "Try Again!",
      text: "" + error,
      timer: 2000
    });
  };
  $scope.showSuccessDialog = function(val) {
    swal("" + val, "", "success");
  };
  $scope.submit_password = function() {
    if ($scope.new_password == $scope.confirm_new_password) {
      $http({
        method: "POST",
        url: "customer_interface.php",
        data: "action=2&oldPassword=" + $scope.old_password + "&newPassword=" + $scope.new_password + "&userID=" + $scope.userID,
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        }
      }).then(function mySuccess(response) {
        var data = response.data;
        if (data.error) {
          $scope.showErrorDialog(data.errorMessage);
        } else {
          $scope.passwordForm.$setPristine();
          $scope.old_password = "";
          $scope.new_password = "";
          $scope.confirm_new_password = "";
          $scope.showSuccessDialog(data.responseMessage);
        }
      }, function myError(response) {
        alert("error");
      });
    } else {
      alert("passwords are not matching");
    }
  };
  // Add input field dynamically (Author: Brijesh Lakkad)
  $scope.memberInputCount = 0; // To track of count of input field
  $scope.memberInputList = []; // To give name to name in input field
  $rootScope.memberModelList = []; // To store inut value in ng-model
  $scope.incrementMemberInput = function() {
    $scope.memberInputCount++;
    for (i = 0; i < $scope.memberInputCount; i++) {
      $scope.memberInputList[i] = "memberInput" + (i + 1);
    }
  };
  $scope.removeMemberInput = function(memberInput) {
    $scope.memberInputCount--;
    var index = $scope.memberInputList.indexOf(memberInput);
    var memberList = [];
    for (i = 0; i < index; i++) {
      memberList[i] = $scope.memberInputList[i];
    }
    for (j = index + 1; j < $scope.memberInputList.length; j++) {
      memberList[i] = $scope.memberInputList[j];
      $scope.memberModelList[i] = $scope.memberModelList[j];
      i++;
    }
    $scope.memberModelList[i] = null;
    $scope.memberInputList = memberList;
  };
});
myApp.directive('memberEmailDir', function($rootScope,$http) {
  return {
    require: 'ngModel',
    link: function(scope, element, attr, mCtrl) {
      function myValidation(value) {
        var patt = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
        if (patt.test(value)) {
          if(value!=$rootScope.$storage.user){
            mCtrl.$setValidity('emailValid', true);
            mCtrl.$setValidity('selfEmailValid', true);
            element.css({
              "border-bottom-width": "1.45px",
              "border-bottom-color": 'green'
            });
          }else{
            mCtrl.$setValidity('selfEmailValid', false);
            mCtrl.$setValidity('emailValid', true);
            element.css({
              "border-bottom-width": "1.45px",
              "border-bottom-color": 'green'
            });
          }
        } else {
          mCtrl.$setValidity('selfEmailValid', true);
          mCtrl.$setValidity('emailValid', false);
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
