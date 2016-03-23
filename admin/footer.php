

<footer style='text-align:center;margin:20px; color:#333333; width:100%;clear:both;'>
            <div style='margin-right: auto; margin-left: auto; padding: 20px; text-align:center;'>
                <p>&copy; Copyright 2015 | <a class="blue" href="/">Oyvent</a> | <a class="blue" href="/Contact.php">Contact</a></p>
            </div>
</footer>

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