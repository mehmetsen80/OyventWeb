<?php 

require_once $_SERVER['DOCUMENT_ROOT'].'/class/Mobile.Detect.class.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/class/Track.class.php';
require_once($_SERVER['DOCUMENT_ROOT']."/class/UserInfo.class.php");


	

@session_start();
$timezone = stripslashes($_SESSION['usertimezone']);
$timezone = str_replace('"',"",$timezone);	

$userObject = $_SESSION['userObject'];	

	
	// Your default site layouts.
// Update this array if you have fewer layout types.
function layoutTypes()
{
    return array('classic', 'mobile', 'tablet');
}

function OSTypes()
{
	return array('','','');
}

function initLayoutType()
{
    // Safety check.
	
	if(empty($_SESSION['layoutType']) || empty($_SESSION["userAgent"])){
	
    	if (!class_exists('Mobile_Detect')) { return 'classic'; }

    	$detect = new Mobile_Detect;
    	$isMobile = $detect->isMobile();
   	 	$isTablet = $detect->isTablet();
    	$layoutTypes = layoutTypes();

    
		if(empty($_SESSION["userAgent"])){
			$_SESSION["userAgent"] = $_SERVER['HTTP_USER_AGENT'];
		}
	
    	if (empty($_SESSION['layoutType'])) {
         	$layoutType = (($isMobile && !$isTablet) ? ($isTablet ? 'tablet' : 'mobile') : 'classic');
		 	// Fallback. If everything fails choose classic layout.
    	 	if ( !in_array($layoutType, $layoutTypes) ) { $layoutType = 'classic'; }
    	 	// Store the layout type for future use.
    	 	$_SESSION['layoutType'] = $layoutType;		 
    	}    

    
		if(empty($_SESSION["layoutInfo"])){			
			if($_SESSION["layoutType"] != 'classic'){			
				$layoutInfo = "";
				foreach($detect->getRules() as $name => $regex){
        			$layoutInfo .=   "is".$name."=";
					$check = $detect->{'is'.$name}();
					$layoutInfo .= ($check)?"1":"0";					
					$layoutInfo .= ";";
				}	
				$_SESSION["layoutInfo"] = $layoutInfo;
			}			
		} 	
	}
}

/**
 *  End helper functions.
 */

// Let's roll. Call this function!

if(empty($_SESSION['layoutType']) || empty($_SESSION["userAgent"]))
	initLayoutType();
	

if(isset($userObject) && !$userObject->isAdmin && $_SERVER['REQUEST_URI'] != '/admin/'){
	$track = new Track($userObject->userID,$_SERVER['REMOTE_ADDR'],$_SERVER['REQUEST_URI'],
	$_SERVER['HTTP_REFERER'],$_SESSION['layoutType'],$_SESSION["layoutInfo"],$_SESSION["userAgent"]);	
	$track->logUser();
}

?>

		
		<header class="headroom">
			
            <button id="showLeftPush">
				<span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span>
			</button>
<a href="/admin/" id="mainLogo" ></a>  Beta
			<?php if(isset($userObject)){ ?>
           	 	<a href="/logout.php?id=logout" id="logout" class="btn">Logout <i class="arrow_right-up"></i></a>
            	<a href="javascript:history.go(-1)" id="back" title="back" class="btn"><i class="arrow_left"></i> Back</a>            	
            <?php } ?>
            
		</header>
        
		<nav class="menu" id="menu">			
         <br /><br /><br />
        	<!--<h3><a href="/" id="mainLogo" ></a>  Beta</h3>-->
           <h2><?php echo $userObject->fullname ?>
             <?php if($userObject->isVerified)
			 		 	echo "<img src='/images/ok.png' title='Verified User' width='16' height='16' >";
			  ?>
            </h2>
          
			<ul>
            
				<!--<li><a href="/"><i class="icon_house_alt"></i> Home</a></li>-->
                <?php  if($_SESSION["region"] == "AR"){ ?>                
                			<li><a href="/ualr"><i class="icon_map_alt"></i> UALR</a></li>
                <?php }else if($_SESSION["region"] == "NY"){ ?>   
                 			<li><a href="/ualbany"><i class="icon_map_alt"></i> UALBANY</a></li>
                <?php } ?> 
                  
                <!--<li><a href='/album/createalbum.php' class='btn' ><i class='icon_plus'></i> Create Album</a></li>-->
                <li><a href='/admin/'  ><i class='icon_menu-square_alt'></i> Home</a></li>
                <li><a href='/admin/add.php'  ><i class='icon_plus'></i> Add Post</a></li>
                
                <!--<li><a href="/admin/myalbums.php"><i class="icon_folder-alt"></i> My Albums</a></li>-->             				
				
			</ul>
            
            
            <h3>Live Hashtags</h3>
            <ul>
            	<li><a href="/admin/hashtag.php?social=0"><i class="social_instagram"></i> Instagram Photos</a></li>
                <li><a href="/admin/hashtag.php?social=1"><i class="social_twitter"></i> Twitter Photos</a></li>
            </ul>
            
			<h3>Import My Content</h3>
			<ul>            	
				<li><a href="/admin/instaphotos.php"><i class="social_instagram"></i> My Instagram Photos</a></li>
                <li><a href="/admin/instatimeline.php"><i class="social_instagram"></i> My Instagram Timeline</a></li>         
                <li><a href="/admin/twitphotos.php"><i class="social_twitter"></i> My Twitter Photos</a></li>
                <li><a href="/admin/twittimeline.php"><i class="social_twitter"></i> My Twitter Timeline</a></li>			
                <li><a href="/admin/facephotos.php"><i class="social_facebook"></i> My Facebook Photos</a></li>
			</ul>           
			
		</nav>
        
        
        