<?php
include("functions.php");
checkSession();
$id=$_SESSION['Userid'];
$email=$_SESSION['User'];
?>
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

}

/* On mouse-over, add a deeper shadow */
.card:hover {
  box-shadow: 0 16px 16px 0 rgba(0,0,0,0.2);
  background-color: rgba(210, 210, 210, 0.8);
}
 </style>
<body ng-app="myapp">
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
    <div class="container" ng-controller="HomeController" id="homeModificationCtrl">
      <button class="btn btn-lg notika-btn-lightblue btn-reco-mg btn-button-mg" data-toggle="modal" data-target="#addHome"><span class="glyphicon glyphicon-plus"></span> Add Home</button>
      <div class="row" ng-bind-html="showAllHome">

      </div>
      <div class="modal fade" id="addHome" role="dialog">
          <div class="modal-dialog modal-sm">
              <div class="modal-content">
                  <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                  </div>
                  <div class="modal-body">
                    <h2>Create new home</h2>
                    <form name="homeNameForm" novalidate>
                    <div class="row">
                      <div class="nk-int-st">
                        <input class="form-control" name="homeName" data-ng-model="homeName" type="text" placeholder="Home Name" ng-style="homeNameStyle" ng-change="analyzeHomeName(homeName)" required home-name-dir/>
                      </div>
                      <span style="color:red;" id="homeName" ng-show="homeNameForm.homeName.$dirty && homeNameForm.homeName.$invalid">
                      <span ng-show="homeNameForm.homeName.$error.required">Please enter home name</span>
                      <span ng-show="!homeNameForm.homeName.$error.required && homeNameForm.homeName.$error.homeNameValid">Please enter only alphabetics and digits</span>
                      <span ng-show="!homeNameForm.homeName.$error.required && !homeNameForm.homeName.$error.homeNameValid && homeNameForm.homeName.$error.homeNameLenValid">Please enter only more than 3 characters</span>
                      <span ng-show="homeNameForm.homeName.$error.homeNameExistsValid">Home name already exists</span>
                      </span>
                    </div>
                  </form>
                  </div>
                  <div class="modal-footer" style="margin:10px;">
                      <button type="button" class="btn btn-default" data-dismiss="modal" ng-disabled="homeNameForm.homeName.$invalid" ng-click="addHome()">Create</button>
                      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                  </div>
              </div>
          </div>
      </div>
      <div class="modal fade" id="renameHome" role="dialog">
          <div class="modal-dialog modal-sm">
              <div class="modal-content">
                  <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                  </div>
                  <div class="modal-body">
                    <h2>Modify your home</h2>
                    <form name="homeReNameForm" novalidate>
                    <div class="row">
                      <div class="nk-int-st">
                        <input class="form-control" name="homeReName" ng-model="homeReName" type="text" placeholder="Home Name" ng-style="homeReNameStyle" ng-change="analyzeHomeName(homeReName)" required home-name-dir/>
                      </div>
                      <span style="color:red;" id="homeReName" ng-show="homeReNameForm.homeReName.$dirty && homeReNameForm.homeReName.$invalid">
                      <span ng-show="homeReNameForm.homeReName.$error.required">Please enter home name</span>
                      <span ng-show="!homeReNameForm.homeReName.$error.required && homeReNameForm.homeReName.$error.homeNameValid">Please enter only alphabetics and digits</span>
                      <span ng-show="!homeReNameForm.homeReName.$error.required && !homeReNameForm.homeReName.$error.homeNameValid && homeReNameForm.homeReName.$error.homeNameLenValid">Please enter only more than 3 characters</span>
                      <span ng-show="homeReNameForm.homeReName.$error.homeNameExistsValid">Home name already exists</span>
                      </span>
                    </div>
                  </form>
                  </div>
                  <div class="modal-footer" style="margin:10px;">
                      <button type="button" class="btn btn-default" data-dismiss="modal" ng-disabled="homeReNameForm.homeReName.$invalid" ng-click="modifyHome()">Modify</button>
                      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                  </div>
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
    <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.6.9/angular-sanitize.js"></script>
    <script>
    var myApp = angular.module("myapp", ['ngCookies']);
    myApp.controller("HomeController",function($rootScope,$scope,$http,$window,$sce,$timeout,$cookies) {
      $scope.user="<?php echo $email; ?>";
      $scope.homeReName="";
      $scope.beforeHomeName="";
      $scope.homeID="";
      $rootScope.homeList="";
      $scope.userID="<?php echo $id; ?>";
      $scope.homeNameStyle={
        "border-bottom-width":"2px"
      };
      $scope.analyzeHomeName=function(val){
        var patt_home=new RegExp("^[a-zA-Z0-9]+$");
        if(patt_home.test(val) && val.length>3){
          $scope.homeNameStyle['border-bottom-color']="green";
        }else{
          $scope.homeNameStyle['border-bottom-color']="red";
        }
      };
      $scope.addHome=function(){
        $http({
          method: "POST",
          url: "home_actions.php",
          data: "action=1&email="+$scope.user+"&homeName="+$scope.homeName,
          headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).then(function mySuccess(response){
          var data=response.data;
          $scope.homeNameForm.$setPristine();
          $scope.homeName="";
          if(!data.error){
            $scope.showSuccessDialog("Home Created");
            $scope.getAllHome();
          }else{
            $scope.showErrorDialog(data.errorMessage);
          }
        },function myError(response){

        });
      };
      $scope.getHomeList=function(){
        $http({
          method: "POST",
          url: "home_actions.php",
          data: "action=4&email="+$scope.user,
          headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).then(function mySuccess(response){
          var data=response.data;
          if(!data.error){
            $rootScope.homeList=data.user.home;
          }else{
          }
        },function myError(response){

        });
      };
      $scope.getHomeList();
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
      $scope.getAllHome=function(){
        $http({
          method: "POST",
          url: "home_actions.php",
          data: "action=0&email="+$scope.user,
          headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        }).then(function mySuccess(response){
          var data=response.data;
          if(!data.error){
            var showAllHome=data.user.allHome;
            $scope.showAllHome=$sce.trustAsHtml(showAllHome);
            $scope.getHomeList();
          }else{
            $scope.showErrorDialog(data.errorMessage);
          }
        },function myError(response){

        });
      };
      $scope.getAllHome();
      $scope.deleteHome = function(val){
        swal({
    			title: "Are you sure?",
    			text: "You will not be able to recover this home!",
    			type: "warning",
    			showCancelButton: true,
    			confirmButtonText: "Yes, delete it!",
    		}).then(function(){
          $http({
            method: "POST",
            url: "home_actions.php",
            data: "action=2&email="+$scope.user+"&id="+val,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
          }).then(function mySuccess(response){
            var data=response.data;
            if(!data.error){
              swal("Deleted!", "Your home has been deleted.", "success");
              $scope.getAllHome();
            }else{
              $scope.showErrorDialog(data.errorMessage);
            }
          });
    		});
      };
      $scope.setHomeName=function(id,homeName,callback){
        $scope.beforeHomeName=homeName;
        $scope.homeReName=homeName;
        $scope.homeID=id;
        $timeout(callback,10);
      };
      $scope.editHome = function(id,homeName){
        $scope.setHomeName(id,homeName,function(){
          $("#renameHome").modal("show");
        });
      };

      $scope.modifyHome=function(){
        if($scope.beforeHomeName!=$scope.homeReName){
          $http({
            method: "POST",
            url: "home_actions.php",
            data: "action=3&email="+$scope.user+"&homeName="+$scope.homeReName+"&id="+$scope.homeID,
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
          }).then(function mySuccess(response){
            var data=response.data;
            $scope.homeReNameForm.$setPristine();
            $scope.beforeHomeName="";
            $scope.homeReName="";
            if(!data.error && (typeof data.error != 'undefined')){
              $scope.showSuccessDialog("Home Name Modified");
              $scope.getAllHome();
            }else{
              $scope.showErrorDialog(data.errorMessage);
            }
          },function myError(response){

          });
        }
      };

      $scope.gotoHome = function(id){
        $cookies.remove('homeID');
        $cookies.put('homeID',id);
        $window.location.href="room.php";
      };

    });
    function deleteHome(id){
      angular.element($("#homeModificationCtrl")).scope().deleteHome(id);
    }
    function editHome(id,homeName){
      angular.element($("#homeModificationCtrl")).scope().editHome(id,homeName);
    }
    function gotoHome(id){
      angular.element($("#homeModificationCtrl")).scope().gotoHome(id);
    }
    myApp.directive("homeNameDir",function($rootScope,$http){
      return{
        require: 'ngModel',
        link: function(scope, element, attr, mCtrl){
          function myValidation(value){
            var patt_home=/^[a-zA-Z0-9]+$/;
            if(patt_home.test(value)){
              mCtrl.$setValidity('homeNameValid',true);
            }else{
              mCtrl.$setValidity('homeNameValid',false);
            }
            if(value.length>3){
              mCtrl.$setValidity('homeNameLenValid',true);
            }else{
              mCtrl.$setValidity('homeNameLenValid',false);
            }
            var i,flag=0;
            var len=$rootScope.homeList.length;
            for(i=0;i<len;i++){
              if($rootScope.homeList[i].homeName==value){
                flag=1;
              }
            }
            if(flag==1){
              mCtrl.$setValidity('homeNameExistsValid',false);
            }else{
              mCtrl.$setValidity('homeNameExistsValid',true);
            }
            return value;
          }
          mCtrl.$parsers.push(myValidation);
        }
      };
    });

    </script>
</body>

</html>
