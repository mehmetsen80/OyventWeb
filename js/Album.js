// JavaScript Document
$(document).ready(function(){
	initFancyBox();
	initModalDeleteDialog();
	initModalDeleteSelectedDialog();
	initDownloadDialog();
	initCheckBoxes();
	//$('#cbxAll').prop('checked', true);
	//selectAll();
	
	//loadMap();//enable this if needed in the future
	//initScrollWindow();
});//ready

var photos_array = new Array();

Array.prototype.indexAt = function(value){
	
  for(var i = 0; i < this.length; i++){
    if(this[i]===value)
      return i;
  };
  return -1;
};

function initCheckBoxes(){
	
	$('.icon-left').on('click',function() {
		
		var photoId = $(this).val();		
		var ischecked = $(this).is(':checked');	
		var ind = photos_array.indexAt(photoId);
		
		if(!ischecked)
			$('#cbxAll').prop('checked', false);		
   });	
}


function selectAll(){
	
	$("input[name = cbxphoto]").each(function()
	{	
		var ischecked = $('#cbxAll').is(':checked');		
			
		if(ischecked)
			$(this).prop('checked', true);
		else
			$(this).prop('checked', false);
	});	
}

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
		alert("Please select at least 1 photo to download!");
		return
	}
	
	$("#hdPhotos").val(photos_array);
	$("#formDownload").submit();
	
	
	/*var info = encodeURI('processType=DOWNLOADPHOTOS&nocache='+Math.random()+'&albumName='+albumName+'&photos_array=' + photos_array+'&userID='+userID);
	var uri = encodeURI("/ajax/Zip.php");
	
	$('#dgDownload').dialog("open");
	
	$.ajax({
   			type: "GET",
   			url: uri,
   			data: info,
			dataType: 'json',
			cache: false,
			context: document.body,
   			success: function(data,status){				
				alert("path:"+data.path);
				if(data.path){
					//var dlif = $('<iframe/>',{'src':data.path}).hide();
					//this.append(dlif);
					$("#ifFile").attr('src', data.path);
				} else if (data.error) {
           			 alert(data.error);
       			} else {
            		alert('Unexpected error, please try again!');
        		}
				
   			}
 		});*/
	

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
		  $('#loading').html('<img  src="/images/ajax-loader.gif" ><br>Please wait while we delete the photo!');
        },
    close : function() {
          $('#loading').html('');		 
     }
 }); 
}


function initModalDeleteSelectedDialog(){
	
 $( "#dgModalDeleteSelected" ).dialog({
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
		  $('#loadingselected').html('<img  src="/images/ajax-loader.gif" ><br>Please be patient and wait while we delete the selected photos!');
        },
    close : function() {
          $('#loadingselected').html('');		 
     }
 }); 
}


function deleteSelectedPhotos(){

	var userID =  $("#txtUserID").val();
	
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
		alert("Please select at least 1 photo to delete!");
		return
	}
	
	$("#hdPhotos").val(photos_array);
	
	//alert("hdPhotos:"+$("#hdPhotos").val());
	
	

	$('#dgModalDeleteSelected').dialog("open");
		
		var info = 'processType=DELETEPHOTOS&nocache='+Math.random()+'&photos=' +  $("#hdPhotos").val() +'&userID='+userID;
		var uri = "/ajax/PhotoHandler.php";

 		$.ajax({
   			type: "GET",
   			url: uri,
   			data: info,
			dataType: 'json',
			cache: false,
   			success: function(data,status){	
				alert(data.message);			
				$('#dgModalDeleteSelected').dialog("close");
			 	window.location = self.location;
   			}
 		});
		
}



function initDownloadDialog(){
	
 $( "#dgDownload" ).dialog({
	title       : '',
    bgiframe    : true,
    position    : 'center',
    draggable   : false,
	dialogClass : 'modal',
	autoOpen: false,
	modal: false,
	height: 150,
	width: 400,
	resizable:false,
	stack:true,
	closeOnEscape:true,
	zIndex: 9999999,
	hide:'highlight',
	show:'highlight',	
	open  : function() {		 
          //$(".ui-dialog-titlebar").hide();
		  $('#lbldownload').html('<img  src="/images/ajax-loader.gif" ><br>Please wait while we prepare the photos!');
        },
    close : function() {
          $('#lbldownload').html('');
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
				
				if(data.success)		
					$(obj).parent().parent().parent().fadeOut("slow");
				else
					alert(data.message);
					
			 	$('#dgModalDelete').dialog("close");
   			}
 		});
	}
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

function gotoHashTagPage(){
	var albumID =  $("#txtAlbumID").val();	
	window.location = "/admin/hashtag.php?albumID="+albumID;
}

function gotoInstaTagPage(){
	var albumID =  $("#txtAlbumID").val();	
	window.location = "/admin/instaphotos.php?albumID="+albumID;
}

function gotoTwitTagPage(){
	var albumID =  $("#txtAlbumID").val();	
	window.location = "/admin/twitphotos.php?albumID="+albumID;
}

function gotoFacePage(){
	var albumID =  $("#txtAlbumID").val();	
	window.location = "/admin/facephotos.php?albumID="+albumID;
}

function deleteAlbum(){
	
	if(confirm("Are you sure to delete this album?"))
	{
		var albumID =  $("#txtAlbumID").val();
		var userID =  $("#txtUserID").val();
	
		var info = 'processType=DELETEALBUM&nocache='+Math.random()+'&albumID=' + albumID+'&userID='+userID;
		var uri = "/ajax/Album.php";
		
		$.ajax({
   			type: "GET",
   			url: uri,
   			data: info,
			dataType: 'json',
			cache: false,
   			success: function(data,status){				
				
				if(data.success){
					alert("Album successfully deleted!");
					window.location = "/admin/myalbums.php";
				}
				else{
					alert(data.error);
				}
			}
 		});
		
	}
}

function showMoreAlbumPhotos(rowat){	
	
	var albumID =  $("#txtAlbumID").val();
	var userID =  $("#txtUserID").val();
	var latitude =  $("#txtLatitude").val();
	var longitude =  $("#txtLongitude").val();
	var subjectID =  $("#txtSubjectID").val();
	var ismine = $("#txtisMine").val();
	var ismostrated = $("#txtisMostRated").val();	
	
	//alert('albumID:'+albumID+' userID:'+userID+' latitude:'+latitude+' longitude:'+longitude+' subjectID:'+subjectID+'  ismine:'+ismine+' ismostrated:'+ismostrated);
	
	//return;
		
	var info='processType=SHOWMOREALBUMPHOTOS&nocache='+Math.random()+'&albumID='+albumID+'&userID='+userID+'&rowat='+rowat+'&latitude='+latitude+'&longitude='+longitude+'&subjectID='+subjectID+'&ismine='+ismine+"&ismostrated="+ismostrated;
	var uri = "/ajax/Album.php";
	
	//alert(info);
	
	$('#load_'+rowat).html('<img src="/images/ajax-loader.gif" >');
	
	$.ajax({
   			type: "GET",
   			url: uri,
   			data: info,
			dataType: 'json',
			cache: false,
   			success: function(data,status){			
			//alert(data.message);
				$('#load_'+rowat).html(data.message);			
			}
 		});
	
}

function filterSubject(){
	
	//var albumID =  $("#txtAlbumID").val();	
	var albumUsername = $("#txtAlbumUserName").val();	
	var subjectID = $("#selSubject").val();	
	
	if(subjectID == -1)
		window.location = '/'+albumUsername+'/';
	else	
		window.location = '/'+albumUsername+'/'+subjectID;
	
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
       $('.loadmorebtn').click(); //enable this when ready
    }

});
}



 var customIcons = {
      restaurant: {
        icon: 'http://labs.google.com/ridefinder/images/mm_20_blue.png'
      },
      bar: {
        icon: 'http://labs.google.com/ridefinder/images/mm_20_red.png'
      }
    };

    function loadMap() {
	var latitude =  document.getElementById("txtAlbumLatitude").value;
	var longitude =  document.getElementById("txtAlbumLongitude").value;	
	//alert("latitude:"+latitude+" longitude:"+longitude);
		
      var map = new google.maps.Map(document.getElementById("map"), {
        center: new google.maps.LatLng(latitude,longitude),
        zoom: 12,
        mapTypeId: 'roadmap'
      });
      var infoWindow = new google.maps.InfoWindow;

		var albumID =  document.getElementById("txtAlbumID").value;
		//alert("albumID:"+albumID);
      // Change this depending on the name of your PHP file
      downloadUrl("/admin/getmarkers.php?albumID="+albumID, function(data) {
        var xml = data.responseXML;
        var markers = xml.documentElement.getElementsByTagName("marker");	
		//alert("markers length:"+markers.length);	
        for (var i = 0; i < markers.length; i++) {			
          var name = markers[i].getAttribute("name");		
		  var photoid =  markers[i].getAttribute("photoid");
          var address = markers[i].getAttribute("address");
          var postdate = markers[i].getAttribute("postdate");
		  var type = markers[i].getAttribute("type");
		  var urlthumb = markers[i].getAttribute("urlthumb");	
		  var urlsmall = markers[i].getAttribute("urlsmall");
          var point = new google.maps.LatLng(
              parseFloat(markers[i].getAttribute("lat")),
              parseFloat(markers[i].getAttribute("lng")));
          var html = "<div style='height:160px;width:240px;color:#000000;'>posted by <b>" + name + "</b><br><a href='/admin/feed.php?photoID="+photoid+"' title='go to this post' alt='go to this post' ><img height='120' width='120' src='"+urlsmall+"' ></a></div>";
          var icon = customIcons[type] || {};
          var marker = new google.maps.Marker({
            map: map,
            position: point,
            icon: icon.icon
          });
          bindInfoWindow(marker, map, infoWindow, html);
        }
      });
    }

    function bindInfoWindow(marker, map, infoWindow, html) {
      google.maps.event.addListener(marker, 'click', function() {
        infoWindow.setContent(html);
        infoWindow.open(map, marker);
      });
    }

    function downloadUrl(url, callback) {
      var request = window.ActiveXObject ?
          new ActiveXObject('Microsoft.XMLHTTP') :
          new XMLHttpRequest;

      request.onreadystatechange = function() {
        if (request.readyState == 4) {
          request.onreadystatechange = doNothing;
          callback(request, request.status);
        }
      };

      request.open('GET', url, true);
      request.send(null);
    }

    function doNothing() {}
