 <div style="width:100%; clear:both;">
           
            	<div style=" width:20%; text-align:left; float:left; ">
                <a href="/" > <img src="/images/oyvent_logo_medium.png" width="140" title="Oyvent" alt="Oyvent"></a>
                </div>
                          
                
                <div style="text-align:right; padding-right:10px;">
                
                 <?php if(isset($userObject)){ ?>
                 
                 <a href="/" class="btn btn-default btn-lg" title="Home" alt="Home" ><i class="icon_menu-square_alt"></i> Home</a>
                 
                 <a href='/album/createalbum.php' title="Create Album" alt="Create Album" class='btn' ><i class='icon_plus'></i> Create Album</a>
                 
                 <a href="/album/myalbums.php" class="btn btn-default btn-lg"   title="My Albums" alt="My Albums" ><i class="icon_images"></i>My Albums</a>
                 
                 <a href="/admin/" class="btn btn-default btn-lg"   title="Import Photos from Social Media" alt="Import Photos from Social Media" ><i class="icon_cogs"></i>Import</a>
                 
				<a href="/logout.php?id=logout"  class="btn btn-default btn-lg">Logout <i class="arrow_right-up"></i></a>
            <?php } else {?>
            
            	<a href="/Explore.php" class="btn btn-default btn-lg" title="Explore" alt="Explore" ><i class="icon_star"></i> Explore</a>
                <a href="/login/" class="btn btn-default btn-lg" title="Sign in" alt="Sign in" ><i class="icon_pin_alt"></i> Sign in</a>
                <a href="/login/" class="btn btn-default btn-lg" title="Register" alt="Register" ><i class="icon_pencil"></i> Register</a>
                <?php } ?>
                
                </div>
            
            </div>