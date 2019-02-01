myApp.controller("SignupController", function($scope,$http,$window) {
  $scope.signupStatus="";
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
        $scope.signupStatus=flag.message;
				$scope.s_status_0=false;
				$scope.s_status_1=true;
			}
			else
			{
        $scope.signupStatus="";
				$scope.s_status_1=false;
				$scope.s_status_0=true;
			}
		}, function myError(response) {

		});
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
                if(value.length>=8)
  							{
  								mCtrl.$setValidity('addresslengthvalid', true);
                  element.css({"border-bottom-width":"1.45px","border-bottom-color":'green'});
  							} else {
  								mCtrl.$setValidity('addresslengthvalid', false);
                  element.css({"border-bottom-width":"1.45px","border-bottom-color":'red'});
  							}
							} else {
								mCtrl.$setValidity('addressvalid', false);
                element.css({"border-bottom-width":"1.45px","border-bottom-color":'red'});
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
              var strongRegex = new RegExp("^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{8,})");
              var mediumRegex = new RegExp("^(((?=.*[a-z])(?=.*[A-Z]))|((?=.*[a-z])(?=.*[0-9]))|((?=.*[A-Z])(?=.*[0-9])))(?=.{6,})");
              if(strongRegex.test(value)) {
                mCtrl.$setValidity('passvalid', true);
                element.css({"border-bottom-width":"1.45px","border-bottom-color":'green'});
              } else if(mediumRegex.test(value)) {
								mCtrl.$setValidity('passvalid', true);
                element.css({"border-bottom-width":"1.45px","border-bottom-color":'orange'});
							} else {
								mCtrl.$setValidity('passvalid', false);
                element.css({"border-bottom-width":"1.45px","border-bottom-color":'red'});
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
                element.css({"border-bottom-width":"1.45px","border-bottom-color":'green'});
							} else {
								mCtrl.$setValidity('namesvalid', false);
                element.css({"border-bottom-width":"1.45px","border-bottom-color":'red'});
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
                element.css({"border-bottom-width":"1.45px","border-bottom-color":'green'});
							} else {
								mCtrl.$setValidity('nameslenvalid', false);
                element.css({"border-bottom-width":"1.45px","border-bottom-color":'red'});
							}
							return value;
						}
						mCtrl.$parsers.push(myValidation);
					}
				};
});
myApp.directive('emailDir', function($http) {
	return {
		require: 'ngModel',
		link: function(scope, element, attr, mCtrl) {
			function myValidation(value) {
				var patt = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
				if (patt.test(value)) {
					mCtrl.$setValidity('emailValid', true);
          if(typeof attr.for != 'undefined'){
            var url="check_data_exists.php";
            if(attr.for=="dealer"){
              url="dealer/check_data_exists.php";
            }
            $http({
        			method : "POST",
        			url : url,
        			data: "email="+value,
        			headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        		}).then(function mySuccess(response) {

        			var flag = response.data;
        			// we should be using flag in only this block so logic in following
        			if(flag.error || flag.user.emailExists)
        			{
                mCtrl.$setValidity('emailExists', false);
                element.css({"border-bottom-width":"1.45px","border-bottom-color":'red'});
        			}
        			else
        			{
        				mCtrl.$setValidity('emailExists', true);
                element.css({"border-bottom-width":"1.45px","border-bottom-color":'green'});
        			}
        		}, function myError(response) {
              mCtrl.$setValidity('emailExists', false);
              element.css({"border-bottom-width":"1.45px","border-bottom-color":'red'});
        		});
          }
				} else {
          element.css({"border-bottom-width":"1.45px","border-bottom-color":'red'});
					mCtrl.$setValidity('emailValid', false);
				}
				return value;
			}
			mCtrl.$parsers.push(myValidation);
		}
	};
});

myApp.directive('contactDir', function($rootScope,$http,$location) {
	return {
		require: 'ngModel',
		link: function(scope, element, attr, mCtrl) {
			function myValidation(value) {
        mCtrl.$setValidity('contactExists', true);
				var patt = /^\+?\d{10}$/;
				if (patt.test(value)) {
					mCtrl.$setValidity('contactValid', true);
          if($location.path()=="/customer/settings" && $rootScope.userDetails['contact']==value){
            mCtrl.$setValidity('contactExists', true);
            element.css({"border-bottom-width":"1.45px","border-bottom-color":'green'});
          }else{
            if(typeof attr.for != 'undefined'){
              var url="check_data_exists.php";
              if(attr.for=="dealer"){
                url="dealer/check_data_exists.php";
              }
              $http({
          			method : "POST",
          			url : url,
          			data: "contact="+value,
          			headers: {'Content-Type': 'application/x-www-form-urlencoded'}
          		}).then(function mySuccess(response) {
          			var flag = response.data;
          			// we should be using flag in only this block so logic in following
          			if(flag.user.contactExists)
          			{
                  mCtrl.$setValidity('contactExists', false);
                  element.css({"border-bottom-width":"1.45px","border-bottom-color":'red'});
          			}
          			else
          			{
          				mCtrl.$setValidity('contactExists', true);
                  element.css({"border-bottom-width":"1.45px","border-bottom-color":'green'});
          			}
          		}, function myError(response) {
                mCtrl.$setValidity('contactExists', false);
                element.css({"border-bottom-width":"1.45px","border-bottom-color":'red'});
          		});
            }
          }
				} else {
          element.css({"border-bottom-width":"1.45px","border-bottom-color":'red'});
					mCtrl.$setValidity('contactValid', false);
				}
				return value;
			}
			mCtrl.$parsers.push(myValidation);
		}
	};
});
