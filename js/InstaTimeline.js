// JavaScript Document
// JavaScript Document
$(document).ready(function(){	
 initFancyBox();
 //toggleMinimize();
 initModalDialog();
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
 

function loadMoreInstagram(obj){			
			
		var maxid = $(obj).data('maxid');
		var albumID = $("#txtAlbumID").val();
		
		//alert(maxid);	
			
		var info = "max_id="+maxid+"&processType=GETUSERFEEDLIST&albumID="+albumID+"&nocache="+Math.random();
		var uri = "/ajax/InstaTimeline.php";
		
		$('#divMore_'+maxid).html('<img src="/images/ajax-loader.gif" >');
		
		$.ajax({
   			type: "GET",
   			url: uri,
   			data: info,
			dataType: 'json',
			cache: false,
   			success: function(data,status){			
				
				$('#divMore_'+data.premaxid).html(data.content);				
				setupImageHover();				
			}
 		});	
}

 function addInstagramPhoto(obj){
	 
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

	var instagramid = $(obj).data('instagramid');	
	var smallurl = $(obj).data('smallurl');
	var mediumurl = $(obj).data('mediumurl');
	var largeurl = $(obj).data('largeurl');	
	var ownedby = $(obj).data('ownedby');
	var caption = $(obj).data('caption');	
	var createdtime = $(obj).data('createdtime');
	var likes = $(obj).data('likes');
	var contentlink = $(obj).data('contentlink');
	var contenttype = $(obj).data('contenttype');
	var tags = $(obj).data('tags');
	var latitude = $(obj).data('latitude');
	var longitude = $(obj).data('longitude');

	
	//alert("userID:"+userID+" instagramid:"+instagramid+" largeurl:"+largeurl+ " mediumurl:"+mediumurl+" smallurl:"+smallurl+"  ownedby:"+ownedby+" caption:"+caption+" link:"+contentlink+" contenttype:"+contenttype+" createdtime:"+createdtime+" tags:"+tags+" longitude:"+longitude+" latitude:"+latitude+" likes:"+likes);
	
	
	var info = encodeURI("userID="+userID+"&albumID="+albumID+"&instagramid="+instagramid+"&thumburl=''&smallurl="+smallurl+"&mediumurl="+mediumurl+"&largeurl="+largeurl+"&caption=&createdtime="+createdtime+"&likes="+likes+"&contentlink="+contentlink+"&contenttype="+contenttype+"&tags="+tags+"&latitude="+latitude+"&longitude="+longitude+"&ownedby="+ownedby+"&processType=UPLOADPHOTO&nocache="+Math.random());
	var uri = encodeURI("/ajax/PhotoHandler.php");
	
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
					$('#loading').html('<img src="/images/ok.png" >');
					$("#footer").css("height",80);
					$("#footer").fadeIn("slow");
					$('#dgModal').dialog("close");
					renderPhotoThumbs();
					//window.location = '/'+albumUsername;
				}
				else{					 
					alert(data.error);
					$("#footer").css("height",15);
					$("#footer").fadeIn("slow");
					$('#dgModal').dialog("close");
				}
			}
 	});	
	
}
 
 
 

