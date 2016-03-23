// JavaScript Document
$(document).ready(function(){	

 initInputs();
	
});//ready

function initInputs(){

	$('#txtAlbumName').bind('click', function(){		
			var lastvalue = $('#txtAlbumName').val();
			if(lastvalue == 'Album Name'){
				$('#txtAlbumName').val('');
			}
			$('#txtAlbumName').css('color','#000000');	
 	});

	$('#txtAlbumName').bind('blur', function(){
    	
		var lastvalue = $('#txtAlbumName').val();
		lastvalue = trim(lastvalue);
		if(lastvalue == '' || lastvalue == 'Album Name')
		{			
			$('#txtAlbumName').val('Album Name');
			$('#txtAlbumName').css('color','#cccccc');	
		}
		else
		{
			$('#txtAlbumName').css('color','#000000');	
		}
 	});
	
	$('#txtUsername').bind('click', function(){		
			var lastvalue = $('#txtUsername').val();
			if(lastvalue == 'url'){
				$('#txtUsername').val('');
			}
			$('#txtUsername').css('color','#000000');	
 	});

	$('#txtUsername').bind('blur', function(){
    	
		var lastvalue = $('#txtUsername').val();
		lastvalue = trim(lastvalue);
		if(lastvalue == '' || lastvalue == 'url')
		{			
			$('#txtUsername').val('url');
			$('#txtUsername').css('color','#cccccc');	
		}
		else
		{
			$('#txtUsername').css('color','#000000');	
		}
 	});	
	
}



function executeAlbum(action){
	
	var albumID = $('#txtAlbumID').val();	
	var userID = $("#txtUserID").val();
	var albumname = $("#txtAlbumName").val();
	albumname = trim(albumname);
	
		
	if(albumname == "" || albumname == "Album Name"){
		alert("Please enter an album name!");
		return;
	}
	
	var username = $("#txtUsername").val();	
	username = trim(username);
	if(username=='' || username == 'url'){
		alert("Please enter an album address!");
		return;
	}
	
	if(username.length < 4 || username.length > 20){
		alert("Album address must be between 4-20 characters!");
		return;
	}
		
		
	
	var privacy = $("#cmbPrivacy").val();
	privacy = trim(privacy);
		
		
	var info = encodeURI("userID="+userID+"&albumname="+albumname+"&privacy="+privacy+"&albumID="+albumID+"&username="+username+"&processType="+action+"&nocache="+Math.random());
	var uri = encodeURI("/ajax/Album.php");
	
	
		
	$('#divLoading').html('<img src="/images/ajax-loader.gif" >');
	
	$.ajax({
   			type: "GET",
   			url: uri,
   			data: info,
			dataType: 'json',
			cache: false,
   			success: function(data,status){						
								//alert(data);
				if(data.success){
					if(action == 'CREATEALBUM')
						$('#divLoading').html("Added Successfully!");
					else if(action == 'UPDATEALBUM')
						$('#divLoading').html("Updated Successfully!");
						
					window.location = '/album/?albumID='+data.pkAlbumID;
				}
				else{
					$('#divLoading').html(data.error);
				}
							
			}
 		});	
}

 
 