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

<div style="width:100%; clear:both;">
           
            	<div style=" width:20%; text-align:left; float:left; ">
                <a href="/" > <img src="/images/oyvent_logo_medium.png" width="140" title="Oyvent" alt="Oyvent"></a>
                </div>
                          
                
                <div style="text-align:right; padding-right:10px;">
                
                 <?php if(isset($userObject)){ ?>
                 
                 <a href="/" class="btn btn-default btn-lg" title="Home" alt="Home" ><i class="icon_menu-square_alt"></i> Home</a>
                 
                 <a href='/album/createalbum.php' title="Create Album" alt="Create Album" class='btn' ><i class='icon_plus'></i> Create Album</a>
                 
                 <a href="/album/myalbums.php" class="btn btn-default btn-lg"   title="My Albums" alt="My Albums" ><i class="icon_images"></i> My Albums</a>
                 
                 <a href="/admin/" class="btn btn-default btn-lg"   title="Import Photos from Social Media" alt="Import Photos from Social Media" ><i class="icon_cogs"></i> Import</a>
                 
				<a href="/logout.php?id=logout"  class="btn btn-default btn-lg">Logout <i class="arrow_right-up"></i></a>
            <?php } else {?>
            
            	<a href="/Explore.php" class="btn btn-default btn-lg" title="Explore" alt="Explore" ><i class="icon_star"></i> Explore</a>
                <a href="/login/" class="btn btn-default btn-lg" title="Sign in" alt="Sign in" ><i class="icon_pin_alt"></i> Sign in</a>
                <a href="/login/" class="btn btn-default btn-lg" title="Register" alt="Register" ><i class="icon_pencil"></i> Register</a>
                <?php } ?>
                
                </div>
            
            </div>