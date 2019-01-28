<!doctype html>
<html class="no-js" lang="">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Login Register | Notika - Notika Admin Template</title>
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
    <!-- font awesome CSS
		============================================ -->
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <!-- owl.carousel CSS
		============================================ -->
    <link rel="stylesheet" href="css/owl.carousel.css">
    <link rel="stylesheet" href="css/owl.theme.css">
    <link rel="stylesheet" href="css/owl.transitions.css">
    <!-- animate CSS
		============================================ -->
    <link rel="stylesheet" href="css/animate.css">
    <!-- normalize CSS
		============================================ -->
    <link rel="stylesheet" href="css/normalize.css">
    <!-- mCustomScrollbar CSS
		============================================ -->
    <link rel="stylesheet" href="css/scrollbar/jquery.mCustomScrollbar.min.css">
    <!-- wave CSS
		============================================ -->
    <link rel="stylesheet" href="css/wave/waves.min.css">
    <!-- Notika icon CSS
		============================================ -->
    <link rel="stylesheet" href="css/notika-custom-icon.css">
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
    <script src="js/vendor/modernizr-2.8.3.min.js"></script>
    <link rel="stylesheet" href="css/custom.css">
    <script src="js/angular.js"></script>
</head>

<body ng-app="myapp">
    <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
    <!-- Login Register area Start-->
    <div class="login-content">
        <!-- Login -->
        <div class="nk-block toggled" id="l-login">
          <!-- Start my code -->
            <div><a href="index.php" class="title"><h2>Home Automation</h2></a></div>
            <hr/>
            <div><b><h3>Login</h3></b></div>
          <!-- End my code -->
          <form ng-controller="LoginController" name="loginForm" method="post" novalidate>
            <div class="nk-form">
                <div class="input-group">
                    <span class="input-group-addon nk-ic-st-pro"><i class="glyphicon glyphicon-envelope"></i></span>
                    <div class="nk-int-st">
                        <input type="text" class="form-control" name="l_email" ng-model="l_email" ng-style="emailStyle" ng-change="analyzeEmail(l_email)"  placeholder="Email Address" required email-dir>
                    </div>
                    <span style="color:red"  id="l_email" ng-show="loginForm.l_email.$dirty && loginForm.l_email.$invalid"><span ng-show="loginForm.l_email.$error.required">Email is required.</span><span ng-show="!loginForm.l_email.$error.required && loginForm.l_email.$error.emailValid">Invalid email address.</span></span>
                </div>
                <div class="input-group mg-t-15">
                    <span class="input-group-addon nk-ic-st-pro"><i class="glyphicon glyphicon-edit"></i></span>
                    <div class="nk-int-st">
                        <input type="password" class="form-control" name="l_password" ng-model="l_password" placeholder="Password" ng-style="passwordStyle" ng-change="analyzePassword(l_password)" required>
                    </div>
                    <span style="color:red"  id="l_password" ng-show="loginForm.l_password.$dirty && loginForm.l_password.$invalid"><span ng-show="loginForm.l_password.$error.required">Password is required.</span></span>
                </div>
                <div class="fm-checkbox">
                  <!--  <label><input type="checkbox" class="i-checks"> <i></i> Keep me signed in</label> -->
                </div>
                <button class="btn btn-login btn-success btn-float" ng-click="login_status()" ng-disabled="loginForm.l_email.$invalid || loginForm.l_password.$invalid"><i class="notika-icon notika-right-arrow right-arrow-ant"></i></button>
            </div>
            <div ng-show="l_status_0" class="alert alert-danger">Email or password is wrong</div><div ng-show="l_status_1" class="alert alert-success">Login in..</div>
          </form>
            <div class="nk-navigation nk-lg-ic">
                <a href="#" data-ma-action="nk-login-switch" data-ma-block="#l-register"><i class="notika-icon notika-plus-symbol"></i> <span>Register</span></a>
                <a href="#" data-ma-action="nk-login-switch" data-ma-block="#l-forget-password"><i>?</i> <span>Forgot Password</span></a>
            </div>
        </div>
        <!-- Register -->
        <div class="nk-block" id="l-register">
          <!-- Start my code -->
            <div><a href="index.php" class="title"><h2>Home Automation</h2></a></div>
            <hr/>
            <div><b><h3>Registration</h3></b></div>
          <!-- End my code -->
          <form ng-controller="SignupController" name="signupForm" method="post" novalidate>
            <div class="nk-form">
                <div class="input-group">
                    <span class="input-group-addon nk-ic-st-pro"><i class="glyphicon glyphicon-user"></i></span>
                    <div class="nk-int-st">
                        <input type="text" class="form-control" placeholder="Name" ng-model="s_name" name="s_name" ng-style="nameStyle" ng-change="analyzeName(s_name)" required  names-dir nameslen-dir>
                    </div>
                    <span style="color:red" id="s_name" ng-show="signupForm.s_name.$dirty && signupForm.s_name.$invalid">
                    <span ng-show="signupForm.s_name.$error.required">First name is required</span>
                    <span ng-show="!signupForm.s_name.$error.required && signupForm.s_name.$error.namesvalid">Enter only characters</span>
                    <span ng-show="!signupForm.s_name.$error.required && signupForm.s_name.$error.nameslenvalid">Enter more than three characters</span>
                    </span>
                </div>

                <div class="input-group mg-t-15">
                    <span class="input-group-addon nk-ic-st-pro"><i class="	glyphicon glyphicon-envelope"></i></span>
                    <div class="nk-int-st">
                        <input type="text" class="form-control" placeholder="Email Address" name="s_email" ng-model="s_email" ng-style="emailStyle" ng-change="analyzeEmail(s_email)" onKeyUp="check_exists(this.value,'s_email')" required email-dir email-exists-dir>
                    </div>
                    <span style="color:red"  id="s_email" ng-show="signupForm.s_email.$dirty && signupForm.s_email.$invalid"><span ng-show="signupForm.s_email.$error.required">Email is required.</span><span ng-show="!signupForm.s_email.$error.required && signupForm.s_email.$error.emailValid">Invalid email address.</span>
                    <span ng-show="!(signupForm.s_email.$error.required || signupForm.s_email.$error.emailValid) && signupForm.s_email.$error.emailExists">Email address already exists</span>
                    </span>
                </div>

                <div class="input-group mg-t-15">
                    <span class="input-group-addon nk-ic-st-pro"><i class="glyphicon glyphicon-edit"></i></span>
                    <div class="nk-int-st">
                        <input type="password" class="form-control" placeholder="Password" name="s_password" ng-model="s_password" ng-style="passwordStrength" ng-change="analyzePasswordStrength(s_password)" required pass-dir>
                    </div>
                    <span style="color:red" id="s_password" ng-show="signupForm.s_password.$dirty && signupForm.s_password.$invalid">
                    <span ng-show="signupForm.s_password.$error.required">Password is required</span>
                    <span ng-show="!signupForm.s_password.$error.required && signupForm.s_password.$error.passvalid">Password should contain at least one number and at least one character. Its length should be of 6 characters</span>
                    </span>
                </div>

                <div class="input-group mg-t-15">
                    <span class="input-group-addon nk-ic-st-pro"><i class="glyphicon glyphicon-book"></i></span>
                    <div class="nk-int-st">
                        <input type="text" class="form-control" placeholder="Address" name="s_address" ng-model="s_address"  ng-style="addressStyle" ng-change="analyzeAddress(s_address)" required address-dir>
                        </div><span style="color:red" id="s_address" ng-show="signupForm.s_address.$dirty && signupForm.s_address.$invalid">
                        <span ng-show="signupForm.s_address.$error.required">Address is required</span>
                        <span ng-show="!signupForm.s_address.$error.required && signupForm.s_address.$error.addressvalid">Do not use unused character.</span>
                        <span ng-show="!signupForm.s_address.$error.required && signupForm.s_address.$error.addresslengthvalid">Enter more details so we can understand</span>
                        </span>
                </div>

                <div class="input-group mg-t-15">
                    <span class="input-group-addon nk-ic-st-pro"><i class="glyphicon glyphicon-home"></i></span>
                    <div class="nk-int-st">
                        <input type="text" class="form-control" placeholder="City" name="s_city" ng-model="s_city"  ng-style="cityStyle" ng-change="analyzeCity(s_city)" required  names-dir>
                    </div>
                    <span style="color:red" id="s_city" ng-show="signupForm.s_city.$dirty && signupForm.s_city.$invalid">
                    <span ng-show="signupForm.s_city.$error.required">City is required</span>
                    <span ng-show="!signupForm.s_city.$error.required && signupForm.s_city.$error.namesvalid">Enter only characters</span>
                    </span>
                </div>

                <div class="input-group mg-t-15">
                    <span class="input-group-addon nk-ic-st-pro"><i class="glyphicon glyphicon-phone"></i></span>
                    <div class="nk-int-st">
                        <input type="text" class="form-control" placeholder="Contact" name="s_contact" ng-style="contactStyle" ng-model="s_contact" ng-change="analyzeContact(s_contact)" required contact-dir>
                    </div>
                    <span style="color:red"  id="s_email" ng-show="signupForm.s_contact.$dirty && signupForm.s_contact.$invalid"><span ng-show="signupForm.s_contact.$error.required">Contact is required.</span><span ng-show="!signupForm.s_contact.$error.required && signupForm.s_contact.$error.contactValid">Please enter valid contact</span>
                    <span ng-show="!(signupForm.s_contact.$error.required || signupForm.s_contact.$error.contactValid) && signupForm.s_contact.$error.contactExists">Contact already exists</span>
                    </span>
                </div>

                <button class="btn btn-login btn-success btn-float" ng-click="signup_status()" ng-disabled="signupForm.s_email.$invalid || signupForm.s_password.$invalid || signupForm.s_name.$invalid || signupForm.s_address.$invalid || signupForm.s_city.$invalid || signupForm.s_contact.$invalid"><i class="notika-icon notika-right-arrow right-arrow-ant"></i></button>
            </div>
            <div ng-show="s_status_0" class="alert alert-danger">Please fill details carefully!</div><div ng-show="s_status_1" class="alert alert-success">Account created :)</div>
          </form>
            <div class="nk-navigation rg-ic-stl">
                <a href="#" data-ma-action="nk-login-switch" data-ma-block="#l-login"><i class="notika-icon notika-right-arrow"></i> <span>Sign in</span></a>
                <a href="" data-ma-action="nk-login-switch" data-ma-block="#l-forget-password"><i>?</i> <span>Forgot Password</span></a>
            </div>
        </div>

        <!-- Forgot Password -->
        <div class="nk-block" id="l-forget-password">
          <!-- Start my code -->
            <div><a href="index.php" class="title"><h2>Home Automation</h2></a></div>
            <hr/>
            <div><b><h3>Forgot password?</h3></b></div>
          <!-- End my code -->
            <div class="nk-form">
                <p class="text-left">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla eu risus. Curabitur commodo lorem fringilla enim feugiat commodo sed ac lacus.</p>

                <div class="input-group">
                    <span class="input-group-addon nk-ic-st-pro"><i class="notika-icon notika-mail"></i></span>
                    <div class="nk-int-st">
                        <input type="text" class="form-control" placeholder="Email Address">
                    </div>
                </div>

                <a href="#l-login" data-ma-action="nk-login-switch" data-ma-block="#l-login" class="btn btn-login btn-success btn-float"><i class="notika-icon notika-right-arrow"></i></a>
            </div>

            <div class="nk-navigation nk-lg-ic rg-ic-stl">
                <a href="" data-ma-action="nk-login-switch" data-ma-block="#l-login"><i class="notika-icon notika-right-arrow"></i> <span>Sign in</span></a>
                <a href="" data-ma-action="nk-login-switch" data-ma-block="#l-register"><i class="notika-icon notika-plus-symbol"></i> <span>Register</span></a>
            </div>
        </div>
    </div>
    <!-- Login Register area End-->
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
    <!-- sparkline JS
    ============================================ -->
    <script src="js/sparkline/jquery.sparkline.min.js"></script>
    <script src="js/sparkline/sparkline-active.js"></script>
    <!-- flot JS
    ============================================ -->
    <script src="js/flot/jquery.flot.js"></script>
    <script src="js/flot/jquery.flot.resize.js"></script>
    <script src="js/flot/flot-active.js"></script>
    <!-- knob JS
    ============================================ -->
    <script src="js/knob/jquery.knob.js"></script>
    <script src="js/knob/jquery.appear.js"></script>
    <script src="js/knob/knob-active.js"></script>
    <!--  Chat JS
    ============================================ -->
    <script src="js/chat/jquery.chat.js"></script>
    <!--  wave JS
    ============================================ -->
    <script src="js/wave/waves.min.js"></script>
    <script src="js/wave/wave-active.js"></script>
    <!-- icheck JS
    ============================================ -->
    <script src="js/icheck/icheck.min.js"></script>
    <script src="js/icheck/icheck-active.js"></script>
    <!--  todo JS
    ============================================ -->
    <script src="js/todo/jquery.todo.js"></script>
    <!-- Login JS
    ============================================ -->
    <script src="js/login/login-action.js"></script>
    <!-- plugins JS
    ============================================ -->
    <script src="js/plugins.js"></script>
    <!-- main JS
    ============================================ -->
    <script src="js/main.js"></script>
    <script src="js/send_mail.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ngStorage/0.3.10/ngStorage.min.js"></script>
<script src="js/controllers/login_signup_controller.js"></script>
</body>

</html>
