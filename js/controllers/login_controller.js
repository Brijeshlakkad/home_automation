myApp.controller("LoginController", function($rootScope,$scope,$http,$window,$localStorage,$sessionStorage) {
  if($localStorage.userID!=null && $localStorage.user!=null){
    $window.location.href="#!customer/home";
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
        $localStorage.userID = flag.user.userID;
        $localStorage.user = flag.user.email;
        /*$scope.$watch('userID', function() {
            $localStorage.userID = $scope.userID;
        });
        $scope.$watch('user', function() {
            $localStorage.user = $scope.user;
        });
        $scope.$watch(function() {
            return angular.toJson($localStorage);
        }, function() {
            $scope.userID = $localStorage.userID;
            $scope.user = $localStorage.user;
        });*/
        $window.location.href = flag.user.location;
        $rootScope.isLoggedIn=true;
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
