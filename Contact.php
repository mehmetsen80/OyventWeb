<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en" class="no-js"> 
<head>
	<title>Contact Oyvent</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">    
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">  
     
    
    <link href="/css/icons.css"  rel="stylesheet">
    <link rel="stylesheet" href="/css/main.css">        
    <link href="/css/custom-theme/jquery-ui-1.10.4.custom.min.css" rel="stylesheet">
    <!-- javascript -->
    <script src="/js/lib/jquery-1.10.2.js"></script>
    <script src="/js/lib/jquery-ui-1.10.4.custom.min.js"></script>
    <script src="/js/General.js"></script>   
    <script src="/js/modernizr.custom.js"></script>    
    <script src="/js/bootstrap.js"></script>
                
    <script src="/js/modernizr.custom.js"></script>       
        
    <!-- bxSlider Javascript file -->
	<script src="/js/lib/jquery.bxslider/jquery.bxslider.min.js"></script>
	<!-- bxSlider CSS file -->
	<link href="/js/lib/jquery.bxslider/jquery.bxslider.css" rel="stylesheet" />
    
    <script src="/js/Contact.js"></script>
        
	
  </head>
<body>

 

 <!--[if lt IE 7]>
            <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <header id="mainHeader">
            <div class="container">
                <a href="/" id="mainLogo"></a><span style='padding-left:20px; font-size:16px; margin-top:20px;width:60px;'>Beta</span>
                <ul id="mainNav">                    
                    <li><a class="btn btn-default" href="/">Home</a></li>
                </ul>
            </div>
        </header>

	<ul class="bxslider">
  	 <li>
        <section id="homeTop" style='height:490px;'>
            
            	 <div class="container">
                                 
                  <div class="row">
                    <div id="welcome" class="col-sm-8" >
                        <h1>Contact Oyvent</h1>                       
                        <h3>Please share with us your ideas, suggestions and complaints. Do not forget to put your name and email!</h3>      
                        
                        <h2>&nbsp;</h2><br>
                       <input type="text" class="txt"  id="txtName" style='width:100%;margin-bottom:6px;'  placeholder="Enter your name">
                 <input type="text" class="txt" id="txtEmail" style='width:100%;margin-bottom:6px;margin-top:0px;'  placeholder="Enter your email">
                 <textarea id="txtMessage" class="txtarea" style='width:100%;margin-top:0px;'  cols="20" rows="8"  placeholder="Enter your text" ></textarea><br>
                        <input type="submit" class="btn" style='width:100%height:40px;font-size:16px;margin-top:7px;' onClick="sendMessage()" id="btnAdd" value="Send Message" >
                                          
                    </div>
                     <div class="col-sm-4">
                        <div class="panel">                        
                              <h4>Oyvent Team</h4>
                              <h5><a href="mailto:mehmetsen80@gmail.com" >Mehmet Sen</a><br>Co-Founder, Developer</h5>
                              <h5><a href="mailto:amac03123@gmail.com" >Adam MacDonald</a><br>Co-Founder, Product Specialist</h5>
                              <h5><a href="mailto:ashifrankedesigns@gmail.com" >Ashi Franke</a><br>Graphic Design Studio BFA at UALR</h5>
                              <h5><a href="mailto:ryoung1994@gmail.com" >Richard Young</a><br>Information Science BS at UALR</h5>
                              <br>
                              <h4>General Questions</h4>    
                              <h5><a href="mailto:info@oyvent.com" >info@oyvent.com</a></h5>
                                          
                        </div>                      
                        
                         
                          
                    </div> 
                    
                   
                    
                 </div>
                 
                 
              </div>
              
               <?php  include("admin/footer.php") ?>
               
              
         </section>
         
		</li>
        
       
        
      </ul>
      
       
      
<div class="modalContact" id="dgModal"><div  id="loading"></div></div>

</body>
</html>