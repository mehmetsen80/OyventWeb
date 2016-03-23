<!DOCTYPE html>
<html lang="en" class="no-js">
	<head>
		<meta charset="UTF-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> 
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		
		
		<link href="http://fonts.googleapis.com/css?family=Roboto:100,300,100italic,400,300italic" rel="stylesheet" type="text/css">
		
        <link href="/test/default.css" type="text/css" rel="stylesheet">
		<script src="/js/modernizr.custom.js"></script>
	</head>

	<body class="menu-push">

		<?php  include("../admin/menu.php"); ?>


		<div class="container">
			<header class="headroom">
				<button id="showLeftPush">
					<span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span>
				</button>
				<a href="#" id="mainLogo" ></a><!-- change link -->
			</header>
			<div class="main">
				<section id="contentArea"><!-- This is where all the content goes -->
					<img src="http://placehold.it/2050x150">
					<img src="http://placehold.it/2050x150">
					<img src="http://placehold.it/2050x450">
					<img src="http://placehold.it/2050x150">
					<img src="http://placehold.it/2050x150">
					<img src="http://placehold.it/2050x1050">
					<img src="http://placehold.it/2050x150">
					<img src="http://placehold.it/2050x450">
					<img src="http://placehold.it/2050x150">
					<img src="http://placehold.it/2050x150">
					<img src="http://placehold.it/2050x1050">
					<img src="http://placehold.it/2050x150">
				</section>
			</div>
		</div>


		<script src="/js/classie.js"></script>
		<script src="/js/headroom.min.js"></script>
		<script>
			var menuLeft = document.getElementById( 'menu' ),
				showLeftPush = document.getElementById( 'showLeftPush' ),
				body = document.body;

			
			showLeftPush.onclick = function() {
				classie.toggle( this, 'active' );
				classie.toggle( body, 'menu-push-toright' );
				classie.toggle( menuLeft, 'menu-open' );
				disableOther( 'showLeftPush' );
			};

			function disableOther( button ) {
				if( button !== 'showLeftPush' ) {
					classie.toggle( showLeftPush, 'disabled' );
				}
			}

			// headroom
			var myElement = document.querySelector(".headroom");
			var headroom  = new Headroom(myElement);
			headroom.init();
		</script>
	</body>
</html>
