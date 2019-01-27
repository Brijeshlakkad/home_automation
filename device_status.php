<!doctype html>
<html class="no-js" lang="">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Admin Panel | Home Automation</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- favicon
		============================================ -->
    <link rel="shortcut icon" type="image/x-icon" href="img/favicon.ico">
    <!-- Google Fonts
		============================================ -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,700,900" rel="stylesheet">
    <!-- Bootstrap CSS
		============================================ -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <!-- Bootstrap CSS
		============================================ -->
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <!-- owl.carousel CSS
		============================================ -->
    <link rel="stylesheet" href="css/owl.carousel.css">
    <link rel="stylesheet" href="css/owl.theme.css">
    <link rel="stylesheet" href="css/owl.transitions.css">
    <!-- meanmenu CSS
		============================================ -->
    <link rel="stylesheet" href="css/meanmenu/meanmenu.min.css">
    <!-- animate CSS
		============================================ -->
    <link rel="stylesheet" href="css/animate.css">
    <!-- normalize CSS
		============================================ -->
    <link rel="stylesheet" href="css/normalize.css">
    <!-- mCustomScrollbar CSS
		============================================ -->
    <link rel="stylesheet" href="css/scrollbar/jquery.mCustomScrollbar.min.css">
    <!-- jvectormap CSS
		============================================ -->
    <link rel="stylesheet" href="css/jvectormap/jquery-jvectormap-2.0.3.css">
    <!-- notika icon CSS
		============================================ -->
    <link rel="stylesheet" href="css/notika-custom-icon.css">
    <!-- wave CSS
		============================================ -->
    <link rel="stylesheet" href="css/wave/waves.min.css">

    <link rel="stylesheet" href="css/wave/button.css">
    <!-- main CSS
		============================================ -->
    <link rel="stylesheet" href="css/main.css">
    <!-- style CSS
		============================================ -->
    <link rel="stylesheet" href="style.css">
    <!-- responsive CSS
		============================================ -->
    <link rel="stylesheet" href="css/responsive.css">
    <!-- modernizr JS
		============================================ -->
    <link rel="stylesheet" href="css/dialog/sweetalert2.min.css">
    <link rel="stylesheet" href="css/dialog/dialog.css">
    <link rel="stylesheet" href="css/custom.css">
    <script src="js/vendor/modernizr-2.8.3.min.js"></script>
    <script src="js/angular.js"></script>
</head>
<style>
.footerB
 {
     position: absolute;
     bottom: 0;
     padding: 1rem;
     text-align: center;
 }

 .grid-container {
   display: grid;
   grid-column-gap: 50px;
   grid-row-gap: 50px;
   grid-template-columns: auto auto auto;
   padding: 10px;
 }

 .grid-item {
   padding: 20px;
   font-size: 30px;
   border: 1px solid rgba(200, 200, 200, 0.8);
   text-align: center;
 }
 .card {
  /* Add shadows to create the "card" effect */
  box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
  transition: 0.3s;
  height:150px;
}

/* On mouse-over, add a deeper shadow */
.card:hover {
  box-shadow: 0 16px 16px 0 rgba(0,0,0,0.2);
  background-color: rgba(210, 210, 210, 0.8);
}

.redDot {
  height: 8px;
  width: 8px;
  background-color: rgba(250,0,0,1);
  border-radius: 50%;
  display: inline-block;
}
.greenDot {
  height: 8px;
  width: 8px;
  background-color: rgba(0,250,0,1);
  border-radius: 50%;
  display: inline-block;
}
 </style>
<body ng-app="myapp" ng-controller="userController">
    <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
    <!-- Start Header Top Area -->
    <div class="header-top-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                    <div class="logo-area">
                        <a href="#" class="title"><h2>Home Automation</h2></a>
                    </div>
                </div>
                <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">

                </div>
            </div>
        </div>
    </div>
    <!-- End Header Top Area -->
    <!-- Mobile Menu start -->
    <div class="mobile-menu-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="mobile-menu">
                        <nav id="dropdown">
                            <ul class="mobile-menu-nav">
                                <li><a data-toggle="collapse" data-target="#Charts" href="profile.php">Home</a>
                                </li>
                                <li><a href="subscription.php">Subscription</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Mobile Menu end -->
    <!-- Main Menu area start-->
    <div class="main-menu-area mg-tb-40">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <ul class="nav nav-tabs notika-menu-wrap menu-it-icon-pro">
                        <li class="active"><a data-toggle="tab" href="profile.php"><i class="notika-icon notika-house"></i> Home</a>
                        </li>
                        <li><a  href="subscription.php"><i class="notika-icon notika-form"></i> Subscription</a>
                        </li>
                        <li><a href="settings.php"><i class="notika-icon notika-settings"></i> Settings</a>
                        </li>
                    </ul>
                    <div class="tab-content custom-menu-content">

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Main Menu area End-->
    <div class="container" ng-controller="DeviceStatusController" id="deviceStatusModificationCtrl">
      <div class="row">
        <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12">
          <button class="btn btn-primary"><span class="glyphicon glyphicon-arrow-left" onclick="javascript:window.history.go(-1)"></span></button>
        </div>
        <div class="col-lg-11 col-md-11 col-sm-12 col-xs-12">
          <div class="row" style="padding:15px;"><h2>Device: {{device.dvName}}</h2></div>
          <button ng-class="addClass" ng-model="deviceStatus" ng-click="changeDeviceStatus(deviceStatus)">{{deviceStatusPrint}}</button>
          <div class="row" ng-show="deviceStatus==1 && deviceSlider!='null'" style="padding:20px;">
            <form name="sliderForm" novalidate>
              <div class="row">
              <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12"><input class="form-control" type="number" id="deviceSliderValue" name="deviceSliderValue" min="0" max="5" ng-model="deviceSliderValue" ng-change="changeDeviceSlider(deviceSliderValue)" slider-dir/></div><div class="col-lg-3 col-md-3 col-sm-3 col-xs-12"><button type="submit" class="btn btn-success" ng-disabled="sliderForm.deviceSliderValue.$invalid">></button></div>
              </div>
              <span style="color:red;" ng-show="sliderForm.deviceSliderValue.$dirty && sliderForm.deviceSliderValue.$invalid">
              <span ng-show="sliderForm.deviceSliderValue.$error.required">Please enter valid value</span>
              <span ng-show="!sliderForm.deviceSliderValue.$error.required && sliderForm.deviceSliderValue.$error.sliderValid">Please enter valid value</span>
              </span>
            </form>
          </div>
        </div>
    </div>
    </div>

      <div id="extraDiv"></div>
    <!-- Start Footer area-->
    <div class="footer-copyright-area footerB" style="width:100%">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="footer-copy-right">
                        <p>Copyright © 2018
. All rights reserved. Template by <a href="https://colorlib.com">Colorlib</a>.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Footer area-->

    <!-- jquery
		============================================ -->
    <script src="js/vendor/jquery-1.12.4.min.js"></script>
    <!-- bootstrap JS
		============================================ -->
    <script src="js/bootstrap.min.js"></script>
    <!-- wow JS
		============================================ -->
    <script src="js/wow.min.js"></script>
    <!-- price-slider JS
		============================================ -->
    <script src="js/jquery-price-slider.js"></script>
    <!-- owl.carousel JS
		============================================ -->
    <script src="js/owl.carousel.min.js"></script>
    <!-- scrollUp JS
		============================================ -->
    <script src="js/jquery.scrollUp.min.js"></script>
    <!-- meanmenu JS
		============================================ -->
    <script src="js/meanmenu/jquery.meanmenu.js"></script>
    <!-- counterup JS
		============================================ -->
    <script src="js/counterup/jquery.counterup.min.js"></script>
    <script src="js/counterup/waypoints.min.js"></script>
    <script src="js/counterup/counterup-active.js"></script>
    <!-- mCustomScrollbar JS
		============================================ -->
    <script src="js/scrollbar/jquery.mCustomScrollbar.concat.min.js"></script>
    <!-- jvectormap JS
		============================================ -->
    <script src="js/jvectormap/jquery-jvectormap-2.0.2.min.js"></script>
    <script src="js/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
    <script src="js/jvectormap/jvectormap-active.js"></script>
    <!-- sparkline JS
		============================================ -->
    <script src="js/sparkline/jquery.sparkline.min.js"></script>
    <script src="js/sparkline/sparkline-active.js"></script>
    <!-- sparkline JS
		============================================ -->
    <script src="js/flot/jquery.flot.js"></script>
    <script src="js/flot/jquery.flot.resize.js"></script>
    <script src="js/flot/curvedLines.js"></script>
    <script src="js/flot/flot-active.js"></script>
    <!-- knob JS
		============================================ -->
    <script src="js/knob/jquery.knob.js"></script>
    <script src="js/knob/jquery.appear.js"></script>
    <script src="js/knob/knob-active.js"></script>
    <!--  wave JS
		============================================ -->
    <script src="js/wave/waves.min.js"></script>
    <script src="js/wave/wave-active.js"></script>
    <!--  todo JS
		============================================ -->
    <script src="js/todo/jquery.todo.js"></script>
    <!-- plugins JS
		============================================ -->
    <script src="js/plugins.js"></script>
    <script src="js/dialog/sweetalert2.min.js"></script>
    <script src="js/dialog/dialog-active.js"></script>
	<!--  Chat JS
		============================================ -->
    <script src="js/chat/moment.min.js"></script>
    <script src="js/chat/jquery.chat.js"></script>
    <!-- main JS
		============================================ -->
    <script src="js/main.js"></script>
	<!-- tawk chat JS
		============================================ -->
    <script src="js/tawk-chat.js"></script>
    <script src="js/angular-cookies.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ngStorage/0.3.10/ngStorage.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.6.9/angular-sanitize.js"></script>
    <script>

    var myApp = angular.module("myapp", ['ngCookies','ngStorage']);
    myApp.controller("DeviceStatusController", function($rootScope,$scope,$http,$window,$sce,$timeout,$cookies) {
      $scope.user=$rootScope.$storage.user;
      $scope.userID=$rootScope.$storage.userID;
      $scope.homeID=$cookies.get('homeID');
      $scope.roomID=$cookies.get('roomID');
      $scope.hwID=$cookies.get('hwID');
      $scope.dvID=$cookies.get('dvID');
      $rootScope.device="";
      $rootScope.deviceSlider="";
      $scope.deviceSliderValue=0;
      $scope.dvName="";
      $scope.addClass={"btn":true,"btn-danger":false,"btn-primary":false};
      $scope.deviceStatusPrint="Connection Problem!";
      $scope.getDevice=function(){
        $http({
          method: "POST",
          url: "device_actions.php",
          data: "action=5&email="+$scope.user+"&deviceID="+$scope.dvID,
          headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).then(function mySuccess(response){
          var data=response.data;
          if(!data.error){
            if(typeof data.user=='undefined'){
              $rootScope.device="";
            }
            else{
              $rootScope.device=data.user;
              $rootScope.deviceSlider=data.user.deviceSlider;
              $scope.deviceStatus=$rootScope.device.dvStatus;
              if($rootScope.deviceSlider!="null"){
                $scope.amountp=$rootScope.deviceSlider.value;
                $scope.deviceSliderValue=$rootScope.deviceSlider.value;
                $("#deviceSliderValue").val($rootScope.deviceSlider.value);
              }
              if($scope.deviceStatus==1){
                $scope.deviceStatusPrint="ON";
                $scope.addClass['btn-primary']=true;
                $scope.addClass['btn-danger']=false;
              }else{
                $scope.deviceStatusPrint="OFF";
                $scope.addClass['btn-primary']=false;
                $scope.addClass['btn-danger']=true;
              }
            }
          }else{
          }
        },function myError(response){

        });
      };
      $scope.getDevice();
      $scope.changeDeviceStatus=function(val){
        if(val==0){
          val=1;
        }else{
          val=0;
        }
        $http({
          method: "POST",
          url: "device_actions.php",
          data: "action=6&email="+$scope.user+"&deviceID="+$scope.dvID+"&status="+val,
          headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).then(function mySuccess(response){
          var data=response.data;
          if(!data.error){
            if(typeof data.user=='undefined'){
              $rootScope.device="";
            }
            else{
              $rootScope.device=data.user;
              $rootScope.deviceSlider=data.user.deviceSlider;
              $scope.deviceStatus=$rootScope.device.dvStatus;
              if($rootScope.deviceSlider!="null"){
                $scope.amountp=$rootScope.deviceSlider.value;
                $scope.deviceSliderValue=$rootScope.deviceSlider.value;
              }
              if($scope.deviceStatus==1){
                $scope.deviceStatusPrint="ON";
                $scope.addClass['btn-primary']=true;
                $scope.addClass['btn-danger']=false;
              }else{
                $scope.deviceStatusPrint="OFF";
                $scope.addClass['btn-primary']=false;
                $scope.addClass['btn-danger']=true;
              }
            }
          }else{
          }
        },function myError(response){

        });
      };
      $scope.changeDeviceSlider=function(val){
        if(typeof val == 'undefined' || val==null){

        }else{
          $http({
            method: "POST",
            url: "device_actions.php",
            data: "action=7&email="+$scope.user+"&deviceID="+$scope.dvID+"&value="+val,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
          }).then(function mySuccess(response){
            var data=response.data;
            if(!data.error){
              $scope.getDevice();
            }else{
            }
          },function myError(response){

          });
        }
      };
    });
    myApp.directive("sliderDir",function($rootScope,$http){
      return{
        require: 'ngModel',
        link: function(scope, element, attr, mCtrl){
          function myValidation(val){
            if(typeof val == 'undefined' || val==null || val<0 || val>5){
              mCtrl.$setValidity('sliderValid',false);
            }else{
              mCtrl.$setValidity('sliderValid',true);
            }
            return val;
          }
          mCtrl.$parsers.push(myValidation);
        }
      };
    });

    </script>
    <script src="js/session_controller.js"></script>
</body>
</html>
