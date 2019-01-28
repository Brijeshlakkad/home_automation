var myApp = angular.module("myapp", ['ngStorage']);
myApp.controller("LoginController", function($scope,$http,$window,$localStorage,$sessionStorage) {
  if($localStorage.userID==null || $localStorage.user==null){
    $scope.$storage = $localStorage.$default({
      userID: null,
      user: null
    });
  }else{
    $window.location.href="home.php";
  }
  $scope.emailStyle = {
    "border-bottom-width":"1.45px"
  };
  $scope.analyzeEmail = function(value) {
    var pattEmail=/^[a-z0-9._]+@[a-z]+\.[a-z.]{2,5}$/;
    if(pattEmail.test(value)) {
      $scope.emailStyle["border-bottom-color"] = "green";
    }
    else {
      $scope.emailStyle["border-bottom-color"] = "red";
    }
  };
  $scope.passwordStyle = {
    "border-bottom-width":"1.45px"
  };
  $scope.analyzePassword = function(value) {
    if(value=="" || value==null) {
      $scope.passwordStyle["border-bottom-color"] = "red";
    }
    else {
      $scope.passwordStyle["border-bottom-color"] = "green";
    }
  };
  $scope.login_status=function(){
    $http({
			method : "POST",
			url : "login_data.php",
			data: "email="+$scope.l_email+"&password="+$scope.l_password,
			headers: {'Content-Type': 'application/x-www-form-urlencoded'}
		}).then(function mySuccess(response) {
			flag = response.data;
			// we should be using flag in only this block so logic in following
			if(!flag.error)
			{
				$scope.l_status_0=false;
				$scope.l_status_1=true;
        $scope.userID = flag.user.userID;
        $scope.user = flag.user.email;
        $scope.$watch('userID', function() {
            $localStorage.userID = $scope.userID;
        });
        $scope.$watch('user', function() {
            $localStorage.user = $scope.user;
        });
        /*$scope.$watch(function() {
            return angular.toJson($localStorage);
        }, function() {
            $scope.userID = $localStorage.userID;
            $scope.user = $localStorage.user;
        });*/
        $window.location.href = flag.user.location;
			}
			else
			{
				$scope.l_status_1=false;
				$scope.l_status_0=true;
			}
		}, function myError(response) {

		});
  };

});
myApp.controller("SignupController", function($scope,$http,$window) {

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
  $scope.emailStyle = {
    "border-bottom-width":"1.45px"
  };
  $scope.analyzeEmail = function(value) {
    var pattEmail=/^[a-z0-9._]+@[a-z]+\.[a-z.]{2,5}$/;
    if(pattEmail.test(value)) {
      $http({
        method : "POST",
        url : "check_data_exists.php",
        data: "email="+value,
        headers: {'Content-Type': 'application/x-www-form-urlencoded'}
      }).then(function mySuccess(response) {
        flag = response.data;
        // we should be using flag in only this block so logic in following
        if(flag.error || flag.user.emailExists)
        {
          $scope.emailStyle["border-bottom-color"] = "red";
        }
        else
        {
          $scope.emailStyle["border-bottom-color"] = "green";
        }
      }, function myError(response) {
        $scope.emailStyle["border-bottom-color"] = "red";
      });
    }
    else {
      $scope.emailStyle["border-bottom-color"] = "red";
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

  var patt = new RegExp("^[0-9]{10}$");
	$scope.contactStyle = {
		"border-bottom-width":"1.45px"
  };
  $scope.analyzeContact = function(value) {
		if(patt.test(value) && value.length>3) {
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
        $scope.emailStyle["border-bottom-color"] = "red";
      });
    }
    else{
				$scope.contactStyle["border-bottom-color"] = "red";
		}
  };

  $scope.signup_status=function(){
    $http({
			method : "POST",
			url : "signup_data.php",
			data: "email="+$scope.s_email+"&password="+$scope.s_password+"&name="+$scope.s_name+"&address="+$scope.s_address+"&city="+$scope.s_city+"&contact="+$scope.s_contact,
			headers: {'Content-Type': 'application/x-www-form-urlencoded'}
		}).then(function mySuccess(response) {
			flag = response.data;
			// we should be using flag in only this block so logic in following
			if(!flag.error)
			{
				$scope.s_status_0=false;
				$scope.s_status_1=true;
			}
			else
			{
				$scope.s_status_1=false;
				$scope.s_status_0=true;
			}
		}, function myError(response) {

		});
  };
});
myApp.directive('contactDir', function($http) {
				return {
					require: 'ngModel',
					link: function(scope, element, attr, mCtrl) {
						function myValidation(value) {
							var patt = new RegExp("^[0-9]{10}$");
							if (patt.test(value)) {
								mCtrl.$setValidity('contactValid', true);
							} else {
								mCtrl.$setValidity('contactValid', false);
							}
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
                  mCtrl.$setValidity('contactExists', false);
                }
                else
                {
                  mCtrl.$setValidity('contactExists', true);
                }
              }, function myError(response) {
                mCtrl.$setValidity('contactExists', false);
              });
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
myApp.directive('emailDir', function() {
	return {
		require: 'ngModel',
		link: function(scope, element, attr, mCtrl) {
			function myValidation(value) {
				var patt = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
				if (patt.test(value)) {
					mCtrl.$setValidity('emailValid', true);
				} else {
					mCtrl.$setValidity('emailValid', false);
				}
				return value;
			}
			mCtrl.$parsers.push(myValidation);
		}
	};
});
myApp.directive('emailExistsDir', function($http) {
	return {
		require: 'ngModel',
		link: function(scope, element, attr, mCtrl) {
			function myValidation(value) {
        $http({
    			method : "POST",
    			url : "check_data_exists.php",
    			data: "email="+value,
    			headers: {'Content-Type': 'application/x-www-form-urlencoded'}
    		}).then(function mySuccess(response) {

    			var flag = response.data;
    			// we should be using flag in only this block so logic in following
    			if(flag.error || flag.user.emailExists)
    			{
            mCtrl.$setValidity('emailExists', false);
    			}
    			else
    			{
    				mCtrl.$setValidity('emailExists', true);
    			}
    		}, function myError(response) {
          mCtrl.$setValidity('emailExists', false);
    		});
				return value;
			}
			mCtrl.$parsers.push(myValidation);
		}
	};
});
