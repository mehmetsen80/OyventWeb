<?php 
    include($_SERVER['DOCUMENT_ROOT']."/Settings.php");	
	include($_SERVER['DOCUMENT_ROOT']."/db/DbConnection.php");
	include($_SERVER['DOCUMENT_ROOT']."/class/Regex.class.php");
	include($_SERVER['DOCUMENT_ROOT']."/Tools.php");	
	
	@session_start();

	$userObject = $_SESSION['userObject'];

	/*if(isset($userObject))
 		header("Location: ".$sitepath);*/
	
   
    /* 1- check if valid and existing email
	   2- check if code matches with database code
	   3- check expiration date of the link
	*/
	
	$message = "";
	
	//date_default_timezone_set("America/Los_Angeles");
	date_default_timezone_set("America/Chicago");
	
	$email=$_GET["email"];
	$code = $_GET["code"];
	
	
	
	if(!Regex::isValidEmailAddress($email) == 1)
 	{
   		$message = "Invalid email address, please try again!";   		
 	} 
	else
	{ 	
    	// if email exists
  		$query = "  SELECT FORGOTCODE,FORGOTEXPIREDATE FROM TBLUSER ";
		$query .= " WHERE EMAIL='".$email."' ";
		$result = executeQuery($query);		
	
		if(mysql_num_rows($result) == 0) // if email does not exists
		{
			$message = "This email does not exist!";		
		}
		else
		{
			$row = mysql_fetch_array($result);		
			
			$forgotCode=$row["FORGOTCODE"]; // forgot code
			$forgotExpireDate=$row["FORGOTEXPIREDATE"]; // forgot code expire date
			
			if($code != $forgotCode) // if code matches with db forgot code
			{
				$message = "Invalid attempt, please try again or contact oyvent team!";
			}
			else
			{	
			    $today = date("Y-m-d h:i:s");
				
				if($today > $forgotExpireDate)
				{
					$message =  "Reset link expired, ";
					$message .= "please try again <a href='Forgot.php'>Forgot Username or Password?</a>";
				}
				else
				{
				 	//everything ok
					$message = "SUCCESSFUL";	 				
				}				
			
			}
	     }//else if email exists
	
	}//else - invalid email
	
	//for test purposes, comment this when in production
	/*$message = "SUCCESSFUL";		
	$username = "mehmetsen80";
	$email = "mehmetsen80@hotmail.com"*/
?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Create New Password</title>
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
		<script src="/js/Reset.js" ></script>
        
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
                        <h1>Reset Password</h1>
                        <h3>Change your password, then please go to <a class="btn-link" href="/login/">login page.</a></h3>
                        
                    </div>
                    <div class="col-sm-4">
                        <div class="panel">
                        
                        <h4>Reset Password</h4>
                        
                        <?php if($message != "SUCCESSFUL"){ 					
							 		echo "<h3> $message </h3>"; ?>	  
						<?php } else { ?> 
									
                                    <input type="text"  id="txtEmail" placeholder="Email" value="<?php echo $email;  ?>" ><br>
                                    <input   name="txtPassword"   id="txtPassword" placeholder="Password" onFocus="initReset()" onKeyUp="checkPassword()"   maxlength="40"  type="password" ><br>
                                    <input   name="txtRePassword"  id="txtRePassword" placeholder="RePassword" onFocus="checkRePassword()" onKeyUp="checkRePassword()"   maxlength="40"  type="password" onKeyPress="fireResetPassword(event)" ><br>
                                    <input name="btnReset"   id="btnReset" onClick="resetPassword()"  style="width:100%;" type="Submit"  value="Change Password" ><br>
                                    <div class="red" id="divReset" ></div>

						<?php } ?>
                           
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
