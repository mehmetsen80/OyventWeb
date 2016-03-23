// JavaScript Document
$(document).ready(function(){		
	initFancyBox();
	initModalDeleteDialog();
	initModalDialog();
	initScrollWindow();
	initTabs();
	
});//ready

function initTabs(){
	 $( "#detailsTabs" ).tabs({
		collapsible: true,
		active: false
	});
		
	/*Not used right now, no need, just some code here*/
	//var collapsible = $( "#detailsTabs" ).tabs( "option", "collapsible" );// Getter	
	//$( "#detailsTabs" ).tabs( "option", "collapsible", true );// Setter
}

function report(photoID){

	if(photoID == null || photoID =='')
		return;
		
	var userID =  $("#txtUserID").val();	
	if(userID == null || userID == 0 || userID == '')
	{
		window.location = '/login/';
		return;
	}		

	
	var albumID = $("#txtAlbumID").val();
	var report = $("#txtReport").val();	
	report = trim(report);
	report = encodeURIComponent(report);	
	
	var info = encodeURI("nocache="+Math.random()+"&processType=ADDREPORT&userID="+userID+"&albumID="+albumID+"&report="+report+"&photoID="+photoID);
	var uri = encodeURI("/ajax/Comment.php");
	
	
	$('#btnReport').attr("disabled", "true");	
	$('#dgModal').dialog("open");
	
	
     
  	  $.ajax({
  		  type: "POST",
  			url: uri,
  			data: info,
			dataType: 'json',
			cache: false,
  			success: function(data,status){	
				$('#btnReport').removeAttr('disabled');	
				//alert(data);
				if(data.success){
					$('#loading').html('<img src="/images/ok.png" >');		
					$('#dgModal').dialog("close");					
					$("#txtReport").val("");										
				}
				else{					 
					alert(data.error);					
					$('#dgModal').dialog("close");
				}
				
			}
		});	
}

function alertRadius(){
	alert('Not within radius, post is disabled!');
}

function addComment(photoID){
	
	if(photoID == null || photoID =='')
		return;
	
	var userID =  $("#txtUserID").val();	
	if(userID == null || userID == 0 || userID == '')
	{
		window.location = '/login/';
		return;
	}		

	var comment = $("#txtMessage").val();	
	comment = trim(comment);
	comment = encodeURIComponent(comment);	
	if(comment == ''){
		alert('Please enter comment!'); 
		return;
	}
	
	var latitude = $("#txtLatitude").val();
	var longitude = $("#txtLongitude").val();
	var owneremail = $("#txtOwnerEmail").val();
	var ownername = $("#txtOwnerName").val();
	
	
	//alert("photoID:"+photoID+" comment:"+comment+" userID:"+userID+" latitude:"+latitude+" longitude:"+longitude);
	//return;
	
	var info = encodeURI("nocache="+Math.random()+"&processType=ADDCOMMENT&userID="+userID+"&owneremail="+owneremail+"&ownername="+ownername+"&latitude="+latitude+"&longitude="+longitude+"&comment="+comment+"&photoID="+photoID);
	var uri = encodeURI("/ajax/Comment.php");
	
	$('#btnAdd').attr("disabled", "true");	
	$('#dgModal').dialog("open");
     
  	  $.ajax({
  		  type: "POST",
  			url: uri,
  			data: info,
			dataType: 'json',
			cache: false,
  			success: function(data,status){	
				$('#btnAdd').removeAttr('disabled');	
				//alert(data);
				if(data.success){
					$('#loading').html('<img src="/images/ok.png" >');		
					$('#dgModal').dialog("close");					
					$("#txtMessage").val("");	
					$('#divCommentPanel').html(data.message);	
					//renderPhotoThumbs();
					//window.location = '/'+albumUsername;
					//location.reload();
				}
				else{					 
					alert(data.error);					
					$('#dgModal').dialog("close");
				}
				
			}
		});  
}

function deleteComment(pkCommentID){
		
	if(confirm("Are you sure to delete this comment?"))
	{	
		var info = 'processType=DELETECOMMENT&nocache='+Math.random()+'&pkCommentID=' + pkCommentID;
		var uri = "/ajax/Comment.php";

		$('#dgModalDelete').dialog("open");


 		$.ajax({
   			type: "GET",
   			url: uri,
   			data: info,
			dataType: 'json',
			cache: false,
   			success: function(data,status){				
								
				if(data.success){
					$('#loadingDelete').html('<img src="/images/ok.png" >');	
					$('#dgModalDelete').dialog("close");				
					location.reload();
				}
				else{
					alert(data.error);
				}			 	
   			}
 		});
	}
}


function initModalDeleteDialog(){
	
 $( "#dgModalDelete" ).dialog({
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
		  $('#loadingDelete').html('<img  src="/images/ajax-loader.gif" ><br>Please wait while we delete the photo!');
        },
    close : function() {
          $('#loadingDelete').html('');		 
     }
 }); 
}

function deletePhoto(obj)
{
	if(confirm("Are you sure to delete this photo?"))
	{
		var userID =  $("#txtUserID").val();
		var pkPhotoID = $(obj).data('photoid');
		
		//alert(userID+'   '+pkPhotoID);
		
		var info = 'processType=DELETEPHOTO&nocache='+Math.random()+'&pkPhotoID=' + pkPhotoID+'&userID='+userID;
		var uri = "/ajax/PhotoHandler.php";

		$('#dgModalDelete').dialog("open");

 		$.ajax({
   			type: "GET",
   			url: uri,
   			data: info,
			dataType: 'json',
			cache: false,
   			success: function(data,status){				
				
				
				if(data.success){
					$('#dgModalDelete').dialog("close");
					alert("Successfully deleted!");
					window.location = "/";
				}
				else{
					alert(data.message);
				}
			 	
   			}
 		});
	}
}


var photos_array = new Array();

Array.prototype.indexAt = function(value){
	
  for(var i = 0; i < this.length; i++){
    if(this[i]===value)
      return i;
  };
  return -1;
};

function downloadPhotos(){	
	var userID =  $("#txtUserID").val();
	var albumName = $("#txtAlbumName").val();	
	
	$("input[name = cbxphoto]").each(function()
	{
		var ischecked = $(this).is(':checked');		
		var photoId = $(this).val();
		var ind = photos_array.indexAt(photoId);
		
		if(ischecked)
		{					
			if(ind == -1)							
				photos_array.push(photoId);					
		}
		else
		{			
			if(ind != -1)			
				photos_array.splice(ind,1);						
		}		
	});	
	
	if(photos_array.length <= 0)
	{
		alert("Please select at least 1 photo!");
		return
	}
	
	$("#hdPhotos").val(photos_array);
	$("#formDownload").submit();
}


function votedown(obj){
	var userID =  $("#txtUserID").val();
	var pkPhotoID = $(obj).data('photoid');			
	
	var info = 'processType=VOTEDOWN&nocache='+Math.random()+'&pkPhotoID=' + pkPhotoID+'&userID='+userID;
	var uri = "/ajax/Album.php";
	
	$.ajax({
   			type: "GET",
   			url: uri,
   			data: info,
			dataType: 'json',
			cache: false,
   			success: function(data,status){				
				
				if(!data.success){
					alert(data.message);
				}else{
					if(data.already)
						alert("You have already voted down!");
					else
						$("#oys_"+pkPhotoID).html(data.message);
				}	
   			}
 		});
}

function voteup(obj){
	var userID =  $("#txtUserID").val();
	var pkPhotoID = $(obj).data('photoid');	
	
	var info = 'processType=VOTEUP&nocache='+Math.random()+'&pkPhotoID=' + pkPhotoID+'&userID='+userID;
	var uri = "/ajax/Album.php";
	
	$.ajax({
   			type: "GET",
   			url: uri,
   			data: info,
			dataType: 'json',
			cache: false,
   			success: function(data,status){				
				
				if(!data.success){
					alert(data.message);
				}else{					
					if(data.already)
						alert("You have already voted up!");
					else
						$("#oys_"+pkPhotoID).html(data.message);
				}
   			}
 		});
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
	height: 140,
	width: 370,
	resizable:false,
	stack:true,
	closeOnEscape:false,
	zIndex: 9999999,
	hide:'highlight',
	show:'highlight',	
	open  : function() {		 
          $(".ui-dialog-titlebar").hide();
		  $("#loading").html('<img  src="/images/ajax-loader.gif" ><br>Please wait while we process this post!');
        },
    close : function(err) {
          $("#loading").html('');  
     }
 }); 
}



function initScrollWindow()
{
	$(document).scroll(function(e){

    // grab the scroll amount and the window height
    var scrollAmount = $(window).scrollTop();
    var documentHeight = $(document).height();

    // calculate the percentage the user has scrolled down the page
    var scrollPercent = (scrollAmount / documentHeight) * 100;

    if(scrollPercent > 65) {
        // run a function called doSomething
      // $('.loadmore').click(); //enable this when ready
    }

});
}


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