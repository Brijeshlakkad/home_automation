myApp.controller("DealerSignupController", function($scope,$http,$window) {
  $scope.typeList=[{
    "key":"0",
    "value":"Dealer"
  },
  {
    "key":"1",
    "value":"Distributor"
  }];
  $scope.s_type=$scope.typeList[0];
	$scope.contactStyle = {
		"border-bottom-width":"1.45px"
  };
  $scope.analyzeContact = function(value) {
    $http({
      method : "POST",
      url : "check_data_exists.php",
      data: "contact="+value,
      headers: {'Content-Type': 'application/x-www-form-urlencoded'}
    }).then(function mySuccess(response) {
      flag = response.data;
      // we should be using flag in only this block so logic in following
      if(flag.error)
      {
        $scope.showContactStatus=flag.errorMessage;
        $scope.contactStyle["border-bottom-color"] = "red";
      }
      else
      {
        $scope.showContactStatus="";
        $scope.contactStyle["border-bottom-color"] = "green";
      }
    }, function myError(response) {
      $scope.showContactStatus="";
      $scope.contactStyle["border-bottom-color"] = "red";
    });
  };

  $scope.signup_status=function(){
    $http({
			method : "POST",
			url : "dealer/signup_data.php",
			data: "email="+$scope.s_email+"&password="+$scope.s_password+"&name="+$scope.s_name+"&address="+$scope.s_address+"&city="+$scope.s_city+"&contact="+$scope.s_contact+"&type="+$scope.s_type,
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
