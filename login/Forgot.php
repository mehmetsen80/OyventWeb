<?php 

@session_start();

if (isset($_SESSION['userObject']) & !empty($_SESSION['userObject']))
	header("Location: /admin/");

?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Oyvent</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>
        <link href="http://fonts.googleapis.com/css?family=Roboto:100,300,100italic,400,300italic" rel="stylesheet" type="text/css">
        
     
        
        <link rel="stylesheet" href="/css/main.css">
        <script src="/js/vendor/modernizr-2.6.2.min.js"></script>
        
        
        <link href="/css/custom-theme/jquery-ui-1.10.4.custom.min.css" rel="stylesheet">
		<!-- javascript -->
		<script src="/js/lib/jquery-1.10.2.js"></script>
		<script src="/js/lib/jquery-ui-1.10.4.custom.min.js"></script>
		<script src="/js/General.js"></script>
		<script src="/js/Forgot.js"></script>
        
    </head>
    <body>
        <!--[if lt IE 7]>
            <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <header id="mainHeader">
            <div class="container">
                <a href="/" id="mainLogo"></a>
                <ul id="mainNav">                    
                    
                </ul>
            </div>
        </header>

        <section id="homeTop">
            <div class="container">
                <div class="row">
                    <div id="welcome" class="col-sm-8">
                        <h1>Send password to your email</h1>
                        <h3>Please enter your email to get the password reset link</h3>
                        
                    </div>
                    <div class="col-sm-4">
                        <div class="panel">
                        
                        <h4>Forgot Password</h4>
                            
                            <input id="txtEmail" value="Email" onkeyup="validateEmail()" onKeyPress="fireForgotPassword(event)" name="txtEmail" placeholder="Email"    type="text"  class="txt"   /><br>
    <input type="submit" onclick="forgotPassword()" id="btnForgot" name="btnForgot"    value="Send Password"/><br>
    <div id="divForgotMessage"  class="red" ></div>
    
                                
                           
                        </div>                     
                    </div>
                </div>
            </div>
        </section>

       

        <footer id="mainFooter">
            <div class="container">
                <p>&copy; Copyright 2014 | <a href="http://oyvent.com">Oyvent</a></p>
            </div>
        </footer>
        

    </body>
</html>
