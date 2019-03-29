myApp.controller("SettingsController", function($rootScope, $scope, $http, $window, $sce, $timeout, $cookies, $ocLazyLoad) {
  $ocLazyLoad.load(['js/meanmenu/jquery.meanmenu.js', 'js/data-table/jquery.dataTables.min.js', 'js/data-table/data-table-act.js', 'js/notification/bootstrap-growl.min.js', 'js/wow.min.js', 'js/main.js'], {
    rerun: true,
    cache: false
  });
  $scope.user = $rootScope.$storage.user;
  $scope.userID = $rootScope.$storage.userID;
  $scope.editStatus = "";
  $scope.memberList = [];
  $scope.hwList = [];
  $rootScope.userDetails = {};
  $scope.selectedHardware=undefined;
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
        if (flag.error == false) {
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
      if (data.error == false) {
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
        if (data.error == false) {
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
  $scope.applyDuplicateValidation = function(duplicateSeries) {
    var name, i, j;
    for (i = 0; i < duplicateSeries.length; i++) {
      for (j = 0; j < duplicateSeries[i].location.length; j++) {
        name = $scope.memberInputList[duplicateSeries[i].location[j]];
        $scope.memberForm[name + ""].$setValidity('duplicateExists', false);
        var element = angular.element($("input[name='" + name + "']"));
        element.css({
          "border-bottom-width": "1.45px",
          "border-bottom-color": 'red'
        });
      }
    }
    return true;
  };
  $scope.checkDuplicate = function(modelArray) {
    var i = 0,
      j = 0,
      k = 0,
      m, len, key, flag = 0,
      flag1 = 0;
    var duplicateSeries = [];
    for (i = 0; i < modelArray.length; i++) {
      for (j = 0; j < modelArray.length; j++) {
        if (i != j && modelArray[i] == modelArray[j]) {
          duplicateSeries[k] = {};
          flag1 = -99;
          len = duplicateSeries.length;
          for (n = 0; n < len; n++) {
            if (duplicateSeries[n]['name'] == modelArray[j]) {
              flag1 = n;
            }
          }
          if (flag1 == -99) {
            duplicateSeries[k].name = modelArray[j];
            duplicateSeries[k].location = [i, j];
            k++;
          } else {
            flag = 0;
            len = duplicateSeries[flag1]['location'].length;
            for (m = 0; m < len; m++) {
              if (duplicateSeries[flag1]['location'][m] == j) {
                flag = 1;
              }
            }
            if (flag == 0) {
              duplicateSeries[flag1]['location'][len] = j;
            }
          }
        }
      }
    }
    if (duplicateSeries.length == 0) {
      return false;
    }
    $scope.applyDuplicateValidation(duplicateSeries);
  };
  $scope.removeNullFromList = function(memberList) {
    var newMemberList = [];
    k = 0;
    for (i = 0; i < memberList.length; i++) {
      if (memberList[i] != null) {
        newMemberList[k] = memberList[i];
        k++;
      }
    }
    return newMemberList;
  };
  $scope.submitMembers = function() {
    var memberList = $scope.removeNullFromList($scope.memberModelList);
    if ($scope.checkDuplicate($scope.memberModelList)) {
      return;
    }
    $http({
      method: "POST",
      url: "customer_actions.php",
      data: "action=2&userID=" + $scope.userID + "&memberModelList=" + JSON.stringify(memberList)+"&hwSeries="+$scope.selectedHardware.hwSeries,
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      }
    }).then(function mySuccess(response) {
      var data = response.data;
      if (data.error == false) {
        if (data.user.failed.error) {
          var errorMessage = "Email ID could not be saved: ";
          for (i = 0; i < data.user.failedList.length; i++) {
            if (i == data.user.failedList.length - 1) {
              errorMessage += data.user.failedList[i] + ".";
            } else {
              errorMessage += data.user.failedList[i] + ", ";
            }
          }
          $rootScope.showErrorDialog(errorMessage);
        } else {
          $scope.getMemberList();
          $rootScope.showSuccessDialog(data.responseMessage)
        }
      } else {
        $rootScope.showErrorDialog(data.errorMessage);
      }
    }, function myError(response) {
      $rootScope.showErrorDialog("Server Load!");
    });
  };
  $scope.getMemberList = function() {
    $http({
      method: "POST",
      url: "customer_actions.php",
      data: "action=0&userID=" + $scope.userID,
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      }
    }).then(function mySuccess(response) {
      var data = response.data;
      if (data.error == false) {
        $scope.memberList = data.user.memberList;
      } else {
        $rootScope.showErrorDialog(data.errorMessage);
      }
    }, function myError(response) {
      $rootScope.showErrorDialog("Server Load!");
    });
  };
  $scope.getMemberList();
  $scope.removeMember = function(memberEmail,hwName) {
    $http({
      method: "POST",
      url: "customer_actions.php",
      data: "action=3&userID=" + $scope.userID + "&memberEmail=" + memberEmail+"&hwName="+hwName,
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      }
    }).then(function mySuccess(response) {
      var data = response.data;
      if (data.error == false) {
        $rootScope.showSuccessDialog(data.responseMessage);
        $scope.getMemberList();
      } else {
        $rootScope.showErrorDialog(data.errorMessage);
      }
    }, function myError(response) {
      $rootScope.showErrorDialog("Server Load!");
    });
  };
  $scope.getHardwareList = function() {
    $http({
      method: "POST",
      url: "customer_actions.php",
      data: "action=4&email=" + $scope.user,
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      }
    }).then(function mySuccess(response) {
      var data = response.data;
      if (!data.error) {
        if (data.user.totalRows<=0) {
          $scope.hwList = [];
        } else {
          $scope.hwList = data.user.hwList;
          $scope.hwList[data.user.totalRows]= {"hwName":"Permission for all hardwares","hwSeries":"-99"};
          $scope.selectedHardware=$scope.hwList[0];
        }
      } else {
        $scope.hwList = [];
      }
    }, function myError(response) {
      $scope.hwList = [];
    });
  };
  $scope.getHardwareList();
});
myApp.directive('memberEmailDir', function($rootScope, $http) {
  return {
    require: 'ngModel',
    link: function(scope, element, attr, mCtrl) {
      function myValidation(value) {
        mCtrl.$setValidity('duplicateExists', true);
        mCtrl.$setValidity('memberEmailNotExists', true);
        var patt = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
        if (patt.test(value)) {
          if (value != $rootScope.$storage.user) {
            mCtrl.$setValidity('emailValid', true);
            mCtrl.$setValidity('selfEmailValid', true);
            $http({
              method: "POST",
              url: "check_data_exists.php",
              data: "email=" + value,
              headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
              }
            }).then(function mySuccess(response) {

              var data = response.data;
              // we should be using flag in only this block so logic in following
              if (data.error || (!data.user.emailExists)) {
                mCtrl.$setValidity('memberEmailNotExists', false);
                element.css({
                  "border-bottom-width": "1.45px",
                  "border-bottom-color": 'red'
                });
              } else {
                mCtrl.$setValidity('memberEmailNotExists', true);
                element.css({
                  "border-bottom-width": "1.45px",
                  "border-bottom-color": 'green'
                });
              }
            }, function myError(response) {
              mCtrl.$setValidity('memberEmailNotExists', false);
              element.css({
                "border-bottom-width": "1.45px",
                "border-bottom-color": 'red'
              });
            });
          } else {
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
