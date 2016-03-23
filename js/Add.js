// JavaScript Document
$(document).ready(function(){		
	
	//renderPhotoThumbs();
	//toggleMinimize();
	initModalDialog();
	initScrollWindow();
	initFileUpload();	
});//ready

function alertRadius(){
	alert('Not within radius, post is disabled!');
}

function initFileUpload(){
	
	var userID =  $("#txtUserID").val();	
	if(userID == null || userID == 0 || userID == '')
	{
		window.location = '/login/';
		return;
	}
	
	var albumID = $("#txtAlbumID").val();
	var latitude = $("#txtLatitude").val();
	var longitude = $("#txtLongitude").val();
	var picUUID = $("#txtPicUUID").val();
	
	var settings = {
    	url: "/ajax/PhotoHandlerWeb.php?nocache="+Math.random()+"&processType=UPLOADTEMPPHOTO&userID="+userID+"&latitude="+latitude+"&longitude="+longitude+"&albumID="+albumID+"&picUUID="+picUUID,
    	dragDrop:true,
		multiple:false,
		autoSubmit:true,
    	fileName: "file",
    	allowedTypes:"jpg,jpeg,png,gif",	
    	returnType:"json",
		showStatusAfterSuccess:false,
	 	onSuccess:function(files,data,xhr)
    	{
			//alert(data);
			//alert(data.success+' '+data.error+' '+data.urlmedium);
			
			if(data.success){
						
				var img = document.createElement('img');
				img.src = data.urlthumb+"?"+(new Date()).getTime();					
				img.border = 0;			
				img.setAttribute("align", "right"); 
				
				var a = document.createElement('a');
				var linkText = document.createTextNode("X");
				a.appendChild(linkText);
				a.title = "Delete this Image";
				a.className='red';
				a.href = "#";				
				a.addEventListener('click', deleteImage, false)
				
			
				var myTable = document.createElement("table");		
				myTable.setAttribute("align", "center"); 
				var myTbody = document.createElement("tbody");
				var myRow = document.createElement("tr");		
				var myCell = document.createElement("td");
				var myRow2 = document.createElement("tr");		
				var myCell2 = document.createElement("td");
		
				myTable.appendChild(myTbody);
				myTbody.appendChild(myRow);
				myRow.appendChild(myCell);
				myCell.appendChild(img);
				
				myTbody.appendChild(myRow2);
				myRow.appendChild(myCell2);
				myCell2.appendChild(a);
								
				$('#divPhoto').html(myTable);
				
				
			}else
			{
				alert(data.error);
			}
			
			
       		// alert((data));
    	},
    	showDelete:true,
    	deleteCallback: function(data,pd)
		{		
			$('#divPhoto').html('');
			pd.statusbar.hide();
			
    		/*for(var i=0;i<data.length;i++)
    		{
        		$.post("delete.php",{op:"delete",name:data[i]},
        		function(resp, textStatus, jqXHR)
        		{
            		//Show Message  
            		$("#status").append("<div>File Deleted</div>");      
        		});
     		}      
   			pd.statusbar.hide(); //You choice to hide/not.*/
		}
	}
	
	var uploadObj = $("#file").uploadFile(settings);

}

function deleteImage(){
		
	var userID =  $("#txtUserID").val();		
	var picUUID = $("#txtPicUUID").val();	
	
	
	var uri = encodeURI("/ajax/PhotoHandlerWeb.php");
	var info = encodeURI("nocache="+Math.random()+"&processType=DELETETEMPPHOTO&picUUID="+picUUID+"&userID="+userID);
	
	$('#dgModal').dialog("open");
	
	$.ajax({
  		type: "POST",
  		url: uri,
  		data: info,
		dataType: 'json',
		cache: false,
  		success: function(data,status){			
			$('#divPhoto').html('');
			$('#loading').html('<img src="/images/ok.png" >');
			$('#dgModal').dialog("close");
		}
	});  
}

function addFeed(){
		
	var userID =  $("#txtUserID").val();	
	if(userID == null || userID == 0 || userID == '')
	{
		window.location = '/login/';
		return;
	}	
		

	var hasphoto = $("#divPhoto").html() != ''?'1':'0';	
		
		
	var subject = $("#selSubject").val();
	subject = encodeURIComponent(subject);
	var caption = $("#txtMessage").val();	
	caption = trim(caption);
	caption = encodeURIComponent(caption);	
	var albumID = $("#txtAlbumID").val();
	var albumUsername = $("#txtAlbumUsername").val();
	var latitude = $("#txtLatitude").val();
	var longitude = $("#txtLongitude").val();
	var picUUID = $("#txtPicUUID").val();
	
	
	if(hasphoto == '0' && caption == ''){
		alert('Please at least enter text or select image!'); 
		return;
	}
	
	//alert("subject:"+subject+" caption:"+caption+" userID:"+userID+" albumID:"+albumID+" latitude:"+latitude+" longitude:"+longitude);
	//return;
	
	var info = encodeURI("nocache="+Math.random()+"&processType=UPLOADPHOTO&userID="+userID+"&latitude="+latitude+"&longitude="+longitude+"&albumID="+albumID+"&caption="+caption+"&subject="+subject+"&picUUID="+picUUID+'&hasphoto='+hasphoto);
	var uri = encodeURI("/ajax/PhotoHandlerWeb.php");
	
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
				
				if(data.success){
					$('#loading').html('<img src="/images/ok.png" >');
					//$("#footer").css("height",80);
					//$("#footer").fadeIn("slow");
					$('#dgModal').dialog("close");
					$("#txtMessage").val("");		
					//renderPhotoThumbs();
					window.location = '/'+albumUsername;
					//to do: in the future you might use the below one
					//window.location = '/admin/feed.php?photoID='+data.pkPhotoID;
				}
				else{					 
					alert(data.error);
					//$("#footer").css("height",15);
					//$("#footer").fadeIn("slow");
					$('#dgModal').dialog("close");
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
		  $('#loading').html('<img  src="/images/ajax-loader.gif" ><br>Please wait while we run the process!');
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