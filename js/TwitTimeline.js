// JavaScript Document// JavaScript Document
$(document).ready(function(){	

initFancyBox();
initModalDialog();
toggleMinimize();
renderPhotoThumbs();  

});//ready

function initFancyBox(){
	$('.fancybox-thumb')
    .attr('rel', 'image-gallery')
    .fancybox({
        prevEffect : 'elastic',
        nextEffect : 'elastic',
        closeEffect : 'elastic',
        closeClick : true,
        closeBtn  : true,
        arrows    : true,
        nextClick : false,		
        autoWidth: true,
        autoHeight: true,
        autoResize: true,
        autoCenter: true,
        scrolling: 'yes',
        scrollOutside: true,
       
        iframe: {
            scrolling : 'auto',
            preload   : true
        },

        helpers : {
			title	: {
				type: 'outside'
			},
            thumbs : {
                width  : 70,
                height : 50
            }
        }
    });
}

function toggleMinimize(){
	
  var height = $("#footer").height(); 
  
  if(height> 15){
  	$("#footer").css("height",15);
	$("#footer").fadeIn("slow");
	
  }else{
	  $("#footer").css("height",80);
	  $("#footer").fadeIn("slow");
  }
}

function initModalDialog(){
	
 $( "#dgModal" ).dialog({
	title       : '',
    bgiframe    : true,
    position    : 'center',
    draggable   : false,
	dialogClass : 'modal',
	autoOpen: false,
	modal: true,
	height: 150,
	width: 400,
	resizable:false,
	stack:true,
	closeOnEscape:false,
	zIndex: 9999999,
	hide:'highlight',
	show:'highlight',	
	open  : function() {		 
          $(".ui-dialog-titlebar").hide();
		  $('#loading').html('<img  src="/images/ajax-loader.gif" ><br>Please wait while we add it to the album!');
        },
    close : function(err) {
          $('#loading').html('');  
     }
 }); 
}

function renderPhotoThumbs()
 {
	 /*var albumID = $("#selAlbum").val();*/
	 var userID =  $("#txtUserID").val();

	 if(userID == '' || userID == null) return;
	 
	 var limit = ($(window).width()-5)/66;
	 limit = Math.floor(limit);
	
	 var uri = "/ajax/Album.php";
	 var info = "userID="+userID+"&processType=GETLATESTPHOTOTHUMBS&limit="+limit+"&nocache="+Math.random();	
	 
	 $.ajax({
   			type: "GET",
   			url: uri,
   			data: info,
			dataType: 'json',
			cache: false,
   			success: function(data,status){				
				$("#thumbs").html(data.message);
				$('#dgModal').dialog("close");				
			}
 	});	
 }

function loadMoreTwitter(obj){			
			
		var maxid = $(obj).data('maxid');
		var albumID = $("#txtAlbumID").val();
					
		var info = "max_id="+maxid+"&processType=GETTWIITTERTIMELINEPHOTOLIST&albumID="+albumID+"&nocache="+Math.random();
		var uri = "/ajax/TwitTimeline.php";
		
		$('#divMore_Twit_'+maxid).html('<img src="/images/ajax-loader.gif" >');
		
		$.ajax({
   			type: "GET",
   			url: uri,
   			data: info,
			dataType: 'json',
			cache: false,
   			success: function(data,status){							
				$('#divMore_Twit_'+data.premaxid).html(data.content);				
				setupImageHover();
			}
 		});	
}

function addTwitterPhoto(obj){

	var userID =  $("#txtUserID").val();
	var albumID = $("#txtAlbumID").val();
	var albumUsername = $("#txtAlbumUsername").val();
	var eligible = $("#txtEligible").val();
	
	if(userID == null || userID == 0 || userID == '')
	{
		window.location = '/login/';
		return;
	}	
	
	if(eligible == 0){
		alert("You are not within radious!");
		return;
	}
	
	if(albumID == null || albumID == 0 || albumID == ''){
		alert("Invalid album, please change your album first!");
		return;
	}
	
   
	var twitterid = $(obj).data('twitterid');	
	var thumburl = $(obj).data('thumburl');
	var smallurl = $(obj).data('smallurl');
	var mediumurl = $(obj).data('mediumurl');
	var largeurl = $(obj).data('largeurl');	
	var ownedby = $(obj).data('ownedby');
	var caption = $(obj).data('caption');	
	var contentlink = $(obj).data('contentlink');
	var favs = $(obj).data('favoritecount');
	var rts = $(obj).data('retweetcount');
	var createdtime = $(obj).data('createdtime');
	var latitude = $(obj).data('latitude');
	var longitude = $(obj).data('longitude');

	
	//alert("userID:"+userID+" twitterid:"+twitterid+" largeurl:"+largeurl+ " mediumurl:"+mediumurl+" smallurl:"+smallurl+"  ownedby:"+ownedby+" caption:"+caption+"  favs:"+favs+"  rts:"+rts+"  createdtime:"+createdtime+"  latitude:"+latitude+" longitude:"+longitude);
	
	//return;
	
	var info = "userID="+userID+"&albumID="+albumID+"&twitterid="+twitterid+"&thumburl="+thumburl+"&smallurl="+smallurl+"&mediumurl="+mediumurl+"&largeurl="+largeurl+"&caption=&contentlink="+contentlink+"&ownedby="+ownedby+"&createdtime="+createdtime+"&latitude="+latitude+"&longitude="+longitude+"&favs="+favs+"&rts="+rts+"&processType=UPLOADTWITTERPHOTO&nocache="+Math.random();
	var uri = "/ajax/PhotoHandlerTwit.php";	
	
	$(obj).attr("disabled", "true");	
	$('#dgModal').dialog("open");
	
	$.ajax({
   			type: "GET",
   			url: uri,
   			data: info,
			dataType: 'json',
			cache: false,
   			success: function(data,status){			
				
				$(obj).removeAttr('disabled');			
				
				if(data.success){
					$('#loading').html('<img  src="/images/ok.png" >');
					$("#footer").css("height",80);
					$("#footer").fadeIn("slow");
					renderPhotoThumbs();
					//window.location = '/'+albumUsername;
				}
				else{
					$('#loading').html('');					 
					alert(data.error);
					$("#footer").css("height",15);
					$("#footer").fadeIn("slow");
					$('#dgModal').dialog("close");
				}
			}
 	});	
	
} 