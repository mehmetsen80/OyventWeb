// JavaScript Document// JavaScript Document
$(document).ready(function(){
initFancyBox();
initModalDialog();
//toggleMinimize();
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

function loadMoreFacebook(obj){			
			
		var pageid = $(obj).data('pageid');		
		var limit = $(obj).data('limit');	
		var fbalbumid = $("#selFBAlbum").val();	
		var albumID = $("#txtAlbumID").val();
					
		var info = "pageid="+pageid+"&limit="+limit+"&fbalbumid="+fbalbumid+"&processType=GETFACEBOOKPHOTOLIST&albumID="+albumID+"&nocache="+Math.random();
		var uri = "/ajax/FacePhotos.php";
		
		$('#divMore_Face_'+pageid).html('<img src="/images/ajax-loader.gif" >');
		
		$.ajax({
   			type: "GET",
   			url: uri,
   			data: info,
			dataType: 'json',
			cache: false,
   			success: function(data,status){					
				$('#divMore_Face_'+data.prepageid).html(data.content);				
				setupImageHover();
			}
 		});	
}

function addFacebookPhoto(obj){
	
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
		alert("Invalid album, please change your oyvent album first!");
		return;
	}
	
   
	var fbid = $(obj).data('fbid');	
	var thumburl = $(obj).data('thumburl');
	thumburl = thumburl.ReplaceAll('&','%26');
	
	var smallurl = $(obj).data('smallurl');
	smallurl = smallurl.ReplaceAll('&','%26');
	
	var mediumurl = $(obj).data('mediumurl');
	mediumurl = mediumurl.ReplaceAll('&','%26');
	
	var largeurl = $(obj).data('largeurl');	
	largeurl = largeurl.ReplaceAll('&','%26');
	
	var ownedby = $(obj).data('ownedby');
	var caption = $(obj).data('caption');	
	var contentlink = $(obj).data('contentlink');
	var createdtime = $(obj).data('createdtime');
	var latitude = $(obj).data('latitude');
	var longitude = $(obj).data('longitude');

	
	/*alert("userID:"+userID+" fbid:"+fbid+" largeurl:"+largeurl+ " mediumurl:"+mediumurl+" smallurl:"+smallurl+" thumburl:"+thumburl+"   ownedby:"+ownedby+" caption:"+caption+"contentlink:"+contentlink+" createdtime:"+createdtime+"  latitude:"+latitude+" longitude:"+longitude);*/	
		
	var info = encodeURI("userID="+userID+"&albumID="+albumID+"&fbid="+fbid+"&thumburl="+thumburl+"&smallurl="+smallurl+"&mediumurl="+mediumurl+"&largeurl="+largeurl+"&caption=&ownedby="+ownedby+"&contentlink="+contentlink+"&createdtime="+createdtime+"&latitude="+latitude+"&longitude="+longitude+"&processType=UPLOADFACEBOOKPHOTO&nocache="+Math.random());	
	var uri = encodeURI("/ajax/PhotoHandlerFace.php");
	
	$(obj).attr("disabled", "true");	
	$('#dgModal').dialog("open");
	
	$.ajax({
   			type: "GET",
   			url: uri,
   			data: info,
			dataType: 'json',
			cache: false,
   			success: function(data,status){			
				//alert(data);
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