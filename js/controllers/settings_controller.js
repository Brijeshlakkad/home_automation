myApp.controller("SettingsController",function($rootScope,$scope,$http,$window,$sce,$timeout,$cookies) {
  $scope.user=$rootScope.$storage.user;
  $scope.userID=$rootScope.$storage.userID;
  $scope.dataLoading = false;
  $scope.editStatus="";
  $rootScope.userDetails={};
  $scope.openTab = function(index,tabName) {
  		var i, tabcontent, tablinks, content;

  		tabcontent = document.getElementsByClassName("tabcontent");
      var wrappedContents = angular.element(tabcontent);
      wrappedContents.addClass("remove").removeClass("show");
  		tablinks = document.getElementsByClassName("tablinks");
      var wrappedLinks = angular.element(tablinks);
  		wrappedLinks.removeClass("active");

  		content = document.getElementById(tabName+"");
      var wrappedContent = angular.element(content);
      wrappedContent.addClass("show").removeClass("remove");

      link = document.getElementById(index+"");
      var wrappedLink = angular.element(link);
      wrappedLink.addClass("active");
  };
  $scope.openTab('0','account_details');

  var strongRegex = new RegExp("^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{8,})");
  var mediumRegex = new RegExp("^(((?=.*[a-z])(?=.*[A-Z]))|((?=.*[a-z])(?=.*[0-9]))|((?=.*[A-Z])(?=.*[0-9])))(?=.{6,})");
    $scope.passwordStrength = {
  "border-bottom-width":"1.45px"
    };
  $scope.analyzePasswordStrength = function(value) {
      if(strongRegex.test(value)) {
          $scope.passwordStrength["border-bottom-color"] = "green";
      } else if(mediumRegex.test(value)) {
          $scope.passwordStrength["border-bottom-color"] = "orange";
      } else {
          $scope.passwordStrength["border-bottom-color"] = "red";
      }
  };
  $scope.nameStyle = {
    "border-bottom-width":"1.45px"
  };
  var patt_name = /^[a-zA-Z]+$/;
  $scope.analyzeName = function(value) {
    if(patt_name.test(value) && value.length>3) {
        $scope.nameStyle["border-bottom-color"] = "green";
    }else {
        $scope.nameStyle["border-bottom-color"] = "red";
    }
  };

  var patt_address = new RegExp("^[0-9a-zA-Z,/. ]+$");
	$scope.addressStyle = {
		"border-bottom-width":"1.45px"
  };
  $scope.analyzeAddress = function(value) {
      if(patt_address.test(value) && value.length >= 10) {
          $scope.addressStyle["border-bottom-color"] = "green";
      }else {
          $scope.addressStyle["border-bottom-color"] = "red";
      }
  };

  var patt_contact = new RegExp("^[0-9]{10}$");
	$scope.contactStyle = {
		"border-bottom-width":"1.45px"
  };
  $scope.analyzeContact = function(value) {
		if(patt_contact.test(value)) {
      if($rootScope.userDetails['contact']!=value){
    		$http({
    			method : "POST",
    			url : "check_data_exists.php",
    			data: "contact="+value,
    			headers: {'Content-Type': 'application/x-www-form-urlencoded'}
    		}).then(function mySuccess(response) {
          flag = response.data;
          // we should be using flag in only this block so logic in following
          if(flag.error || flag.user.contactExists)
          {
            $scope.contactStyle["border-bottom-color"] = "red";
          }
          else
          {
            $scope.contactStyle["border-bottom-color"] = "green";
          }
        }, function myError(response) {
          $scope.contactStyle["border-bottom-color"] = "red";
        });
      }else{
        $scope.contactStyle["border-bottom-color"] = "green";
      }
    }
    else{
				$scope.contactStyle["border-bottom-color"] = "red";
		}
  };

  $scope.editDetails=function(){
    $scope.dataLoading = true;
    if($rootScope.userDetails['name']!=$scope.s_name || $rootScope.userDetails['city']!=$scope.s_city || $rootScope.userDetails['address']!=$scope.s_address || $rootScope.userDetails['contact']!=$scope.s_contact){
      $rootScope.body.addClass("loading");
      $http({
  			method : "POST",
  			url : "customer_interface.php",
  			data: "action=1&email="+$scope.s_email+"&name="+$scope.s_name+"&address="+$scope.s_address+"&city="+$scope.s_city+"&contact="+$scope.s_contact,
  			headers: {'Content-Type': 'application/x-www-form-urlencoded'}
  		}).then(function mySuccess(response) {
        $rootScope.body.removeClass("loading");
  			flag = response.data;
  			// we should be using flag in only this block so logic in following
  			if(!flag.error)
  			{
          $scope.editStatus="Details Updated!";
          $scope.dataLoading = false;
  				$scope.s_status_0=false;
  				$scope.s_status_1=true;
  			}
  			else
  			{
  				$scope.s_status_1=false;
  				$scope.s_status_0=true;
  			}
  		}, function myError(response) {
        $rootScope.body.removeClass("loading");
  		});
    }else
    {
      $scope.editStatus="Details has not been modified.";
      $scope.s_status_1=true;
      $scope.s_status_0=false;
    }
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

  $scope.get_user_details = function()
		{
			$http({
				method : "POST",
				url : "customer_interface.php",
				data: "action=0&userID="+$scope.userID,
				headers: {'Content-Type': 'application/x-www-form-urlencoded'}
			}).then(function mySuccess(response) {
        var data = response.data;
        if(!data.error){
          $scope.s_name = data.name;
          $scope.s_email = data.email;
          $scope.s_city= data.city;
          $scope.s_address = data.address;
          $scope.s_contact = data.contact;
          $rootScope.userDetails=data;
        }else{
          $scope.showErrorDialog(data.errorMessage);
        }
			}, function myError(response) {
			});
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
		    "border-width":"1.45px"
    };
    $scope.analyze_new = function(value) {
        if(strongRegex.test(value)) {
            $scope.new_passwordStrength["border-color"] = "green";
        } else if(mediumRegex.test(value)) {
            $scope.new_passwordStrength["border-color"] = "orange";
        } else {
            $scope.new_passwordStrength["border-color"] = "red";
        }
    };
		$scope.cnew_passwordStrength = {
      "border-width":"1.45px"
    };
    $scope.analyze_cnew = function(value) {
        if(strongRegex.test(value)) {
            $scope.cnew_passwordStrength["border-color"] = "green";
        } else if(mediumRegex.test(value)) {
            $scope.cnew_passwordStrength["border-color"] = "orange";
        } else {
            $scope.cnew_passwordStrength["border-color"] = "red";
        }
    };
    $scope.submit_password = function() {
			if($scope.new_password==$scope.confirm_new_password)
			{
        $http({
  				method : "POST",
  				url : "customer_interface.php",
  				data : "action=2&oldPassword="+$scope.old_password+"&newPassword="+$scope.new_password+"&userID="+$scope.userID,
  				headers: {'Content-Type': 'application/x-www-form-urlencoded'}
  			}).then(function mySuccess(response) {
  				$scope.passwordForm.$setPristine();
  				$scope.old_password="";
  				$scope.new_password="";
  				$scope.confirm_new_password="";
  				var data = response.data;
          alert(JSON.stringify(data));
  				if(data.error){
            alert(data.errorMessage);
          }else{
            alert("Password is changed");
          }
  			}, function myError(response) {
  				alert("error");
  			});
      }else{
        alert("passwords are not matching");
      }
    };
});
myApp.directive('contactDir', function($rootScope,$http) {
				return {
					require: 'ngModel',
					link: function(scope, element, attr, mCtrl) {
						function myValidation(value) {
							var patt = new RegExp("^[0-9]{10}$");
							if (patt.test(value)) {
								mCtrl.$setValidity('contactValid', true);
                if($rootScope.userDetails['contact']!=value){
                  $http({
              			method : "POST",
              			url : "check_data_exists.php",
              			data: "contact="+value,
              			headers: {'Content-Type': 'application/x-www-form-urlencoded'}
              		}).then(function mySuccess(response) {
                    var flag = response.data;
                    // we should be using flag in only this block so logic in following
                    if(flag.error || flag.user.contactExists)
                    {
                      mCtrl.$setValidity('contactExists', false);
                    }
                    else
                    {
                      mCtrl.$setValidity('contactExists', true);
                    }
                  }, function myError(response) {
                    mCtrl.$setValidity('contactExists', false);
                  });
                }else{
                  mCtrl.$setValidity('contactExists', true);
                }
							} else {
								mCtrl.$setValidity('contactValid', false);
							}
							return value;
						}
						mCtrl.$parsers.push(myValidation);
					}
				};
});
myApp.directive('addressDir', function() {
				return {
					require: 'ngModel',
					link: function(scope, element, attr, mCtrl) {
						function myValidation(value) {
							var patt = new RegExp("^[0-9a-zA-Z,/. ]+$");
							if (patt.test(value)) {
								mCtrl.$setValidity('addressvalid', true);
							} else {
								mCtrl.$setValidity('addressvalid', false);
							}
							if(value.length>=8)
							{
								mCtrl.$setValidity('addresslengthvalid', true);
							} else {
								mCtrl.$setValidity('addresslengthvalid', false);
							}
							return value;
						}
						mCtrl.$parsers.push(myValidation);
					}
				};
});
myApp.directive('passDir', function() {
				return {
					require: 'ngModel',
					link: function(scope, element, attr, mCtrl) {
						function myValidation(value) {
							var patt=new RegExp("^(((?=.*[a-z])(?=.*[A-Z]))|((?=.*[a-z])(?=.*[0-9]))|((?=.*[A-Z])(?=.*[0-9])))(?=.{6,})");
							if (patt.test(value)) {
								mCtrl.$setValidity('passvalid', true);
							} else {
								mCtrl.$setValidity('passvalid', false);
							}
							return value;
						}
						mCtrl.$parsers.push(myValidation);
					}
				};
});
myApp.directive('namesDir', function() {
				return {
					require: 'ngModel',
					link: function(scope, element, attr, mCtrl) {
						function myValidation(value) {
							var patt =  /^[a-zA-Z]+$/
							if (patt.test(value)) {
								mCtrl.$setValidity('namesvalid', true);
							} else {
								mCtrl.$setValidity('namesvalid', false);
							}
							return value;
						}
						mCtrl.$parsers.push(myValidation);
					}
				};
});
myApp.directive('nameslenDir', function() {
				return {
					require: 'ngModel',
					link: function(scope, element, attr, mCtrl) {
						function myValidation(value) {
							if (value.length>3) {
								mCtrl.$setValidity('nameslenvalid', true);
							} else {
								mCtrl.$setValidity('nameslenvalid', false);
							}
							return value;
						}
						mCtrl.$parsers.push(myValidation);
					}
				};
});
myApp.directive('passwordDir', function($rootScope) {
				return {
					require: 'ngModel',
					link: function(scope, element, attr, mCtrl) {
						function myValidation(value) {
							var patt=new RegExp("^(((?=.*[a-z])(?=.*[A-Z]))|((?=.*[a-z])(?=.*[0-9]))|((?=.*[A-Z])(?=.*[0-9])))(?=.{6,})");
							if (patt.test(value)) {
								mCtrl.$setValidity('passvalid', true);
							} else {
								mCtrl.$setValidity('passvalid', false);
							}
							return value;
						}
						mCtrl.$parsers.push(myValidation);
					}
				};
});
