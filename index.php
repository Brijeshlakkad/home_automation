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
    <link rel="stylesheet" type="text/css" href="css/vertical_tab.css" />
    <link rel="stylesheet" type="text/css" href="css/please_wait.css" />
    <script src="js/vendor/modernizr-2.8.3.min.js"></script>
    <script src="js/angular.js"></script>
</head>
<style>
* {
  margin: 0;
}
html, body {
  height: 100%;
}
.page-wrap {
  min-height: 100%;
  /* equal to footer height */
  margin-bottom: -20px;
}
.page-wrap:after {
  content: "";
  display: block;
}
 .footerB .page-wrap:after {
   height: 130px;
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
                        <a href="#!/" class="title"><h2>Home Automation</h2></a>
                    </div>
                </div>
                <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                  <div class="row" style="margin:15px;">
                    <div class="pull-right">
                      <button class="btn btn-default" ng-if="isLoggedIn" ng-click="logout()">Logout</button>
                    </div>
                  </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Header Top Area -->
    <div class="please_wait_modal"> </div>
    <div ng-view class="page-wrap"></div>
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
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.2.28//angular-route.min.js"></script>
    <script>
    var myApp = angular.module("myapp", ['ngCookies','ngStorage','ngRoute']);
    myApp.config(function ($routeProvider) {
      $routeProvider.when('/', {
          templateUrl: 'home_page/home_page.html',
          controller: 'HomePageController'
      }).when('/login', {
          templateUrl: 'login.html',
          controller: 'LoginController'
      }).when('/register', {
          templateUrl: 'register.html',
          controller: 'SignupController'
      }).when('/forget_password', {
          templateUrl: 'forget_password.html'
      }).when('/home', {
          templateUrl: 'home.html',
          controller: 'HomeController'
      }).when('/home/:homeID', {
          templateUrl: 'room.html',
          controller: 'RoomController'
      }).when('/home/:homeID/:roomID', {
          templateUrl: 'hardware.html',
          controller: 'HardwareController'
      }).when('/home/:homeID/:roomID/:hwID', {
          templateUrl: 'device.html',
          controller: 'DeviceController'
      }).when('/home/:homeID/:roomID/:hwID/:dvID', {
          templateUrl: 'device_status.html',
          controller: 'DeviceStatusController'
      }).when('/settings', {
          templateUrl: 'settings.html',
          controller: 'SettingsController'
      }).when('/subscription', {
          templateUrl: 'subscription.html',
          controller: 'SubscriptionController'
      }).when('/404-not-found', {
          templateUrl: '404.html',
      }).otherwise({
          redirectTo: "/404-not-found"
      });
    });
    </script>
    <script src="js/controllers/session_controller.js"></script>
    <script src="js/controllers/login_controller.js"></script>
    <script src="js/controllers/signup_controller.js"></script>
    <script src="js/controllers/home_page_controller.js"></script>
    <script src="js/controllers/home_controller.js"></script>
    <script src="js/controllers/room_controller.js"></script>
    <script src="js/controllers/hardware_controller.js"></script>
    <script src="js/controllers/device_controller.js"></script>
    <script src="js/controllers/settings_controller.js"></script>
    <script src="js/controllers/subscription_controller.js"></script>
    <script src="js/controllers/device_status_controller.js"></script>
</body>
</html>
