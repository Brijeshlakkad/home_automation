<?php
if(isset($_REQUEST['state']) && isset($_REQUEST['client_id']) && isset($_REQUEST['response_type']) && isset($_REQUEST['scope']) && isset($_REQUEST['redirect_uri'])){
  $state=$_REQUEST['state'];
  $client_id=$_REQUEST['client_id'];
  $response_type=$_REQUEST['response_type'];
  $scope=$_REQUEST['scope'];
  $redirect_uri=$_REQUEST['redirect_uri'];
}
else{
  $redirect_uri="404.html";
}
?>
<html>
<head>
  <title>Login</title>
  <!-- favicon
		============================================ -->
  <link rel="shortcut icon" type="image/x-icon" href="img/favicon.ico">
  <!-- Google Fonts
		============================================ -->
  <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,700,900" rel="stylesheet">
  <!-- animate CSS
		============================================ -->
  <link rel="stylesheet" href="css/animate.css">
  <!-- summernote CSS
		============================================ -->
  <link rel="stylesheet" href="css/summernote/summernote.css">
  <!-- Bootstrap CSS
    ============================================ -->
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <!-- style CSS
		============================================ -->
  <link rel="stylesheet" href="style.css">
  <!-- main CSS
		============================================ -->
  <link rel="stylesheet" href="css/main.css">
  <!-- responsive CSS
		============================================ -->
  <link rel="stylesheet" href="css/responsive.css">
  <!-- modernizr JS
		============================================ -->
  <script src="js/vendor/modernizr-2.8.3.min.js"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.9/angular.min.js"></script>
</head>
<body ng-app="myapp" ng-controller="AlexaLoginController">
  <div class="login-content">
    <!-- Login -->
    <div class="nk-block toggled" id="l-login">
      <!-- Start my code -->
      <div><b><h3>Customer Login</h3></b></div>
      <hr />
      <!-- End my code -->
      <form name="loginForm" method="post" novalidate>
        <div class="nk-form">
          <div class="input-group">
            <span class="input-group-addon nk-ic-st-pro"><i class="glyphicon glyphicon-envelope"></i></span>
            <div class="nk-int-st">
              <input type="text" class="form-control" name="l_email" ng-model="l_email" placeholder="Email Address" required email-dir>
            </div>
            <span style="color:red" id="l_email" ng-show="loginForm.l_email.$dirty && loginForm.l_email.$invalid"><span ng-show="loginForm.l_email.$error.required">Email is required.</span><span ng-show="!loginForm.l_email.$error.required && loginForm.l_email.$error.emailValid">Invalid
                email address.</span></span>
          </div>
          <div class="input-group mg-t-15">
            <span class="input-group-addon nk-ic-st-pro"><i class="glyphicon glyphicon-edit"></i></span>
            <div class="nk-int-st">
              <input type="password" class="form-control" name="l_password" ng-model="l_password" placeholder="Password" ng-style="passwordStyle" ng-change="analyzePassword(l_password)" required>
            </div>
            <span style="color:red" id="l_password" ng-show="loginForm.l_password.$dirty && loginForm.l_password.$invalid"><span ng-show="loginForm.l_password.$error.required">Password is required.</span></span>
          </div>
          <div class="fm-checkbox">
            <!--  <label><input type="checkbox" class="i-checks"> <i></i> Keep me signed in</label> -->
          </div>
        </div>
        <div ng-show="l_status_0" class="alert alert-danger">Email or password is wrong</div>
        <div ng-show="l_status_1" class="alert alert-success">Login in..</div>
        <button class="btn btn-login btn-success" ng-click="login_status()" ng-disabled="loginForm.l_email.$invalid || loginForm.l_password.$invalid">Login</button>
      </form>
    </div>
  </div>
<script>
var myApp = angular.module("myapp",[]);
myApp.controller("AlexaLoginController", function($rootScope, $scope, $http, $window) {
  $scope.redirectURI= "<?php echo $redirect_uri; ?>";
  $scope.state= "<?php echo $state; ?>";
  $scope.passwordStyle = {
    "border-bottom-width": "1.45px"
  };
  $scope.analyzePassword = function(value) {
    if (value == "" || value == null) {
      $scope.passwordStyle["border-bottom-color"] = "red";
    } else {
      $scope.passwordStyle["border-bottom-color"] = "green";
    }
  };
  $scope.login_status = function() {
    $http({
      method: "POST",
      url: "login_data.php",
      data: "email=" + $scope.l_email + "&password=" + $scope.l_password,
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      }
    }).then(function mySuccess(response) {
      var data = response.data;
      if (!data.error) {
        $scope.l_status_0 = false;
        $scope.l_status_1 = true;
        $window.location.href=$scope.redirectURI+"?code="+data.user.userID+"&state="+$scope.state;
      } else {
        $scope.l_status_1 = false;
        $scope.l_status_0 = true;
      }
    }, function myError(response) {

    });
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
              url="dealer_distributor/check_data_exists.php";
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
          }else{
						element.css({"border-bottom-width":"1.45px","border-bottom-color":'green'});
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
</script>
<!-- jquery
  ============================================ -->
<script src="js/vendor/jquery-1.12.4.min.js"></script>
<!-- bootstrap JS
  ============================================ -->
<script src="js/bootstrap.min.js"></script>
<!-- wow JS
  ============================================ -->
<!-- plugins JS
  ============================================ -->
<script src="js/plugins.js"></script>
</body>
</html>
