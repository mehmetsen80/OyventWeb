// JavaScript Document

$(document).ready(function(){
	initReset();		   
});//ready


function initReset()
{
   $("#divReset").html("");    
	
	 
	$('#txtPassword').bind('click', function(){		
			var lastvalue = $('#txtPassword').val();
			if(lastvalue == 'Password'){
				$('#txtPassword').val('');
			}
			$('#txtPassword').css('color','#000000');	
 	});

	$('#txtPassword').bind('blur', function(){
    	
		var lastvalue = $('#txtPassword').val();
		lastvalue = trim(lastvalue);
		if(lastvalue == '' || lastvalue == 'Password')
		{			
			$('#txtPassword').val('Password');
			$('#txtPassword').css('color','#cccccc');	
		}
		else
		{
			$('#txtPassword').css('color','#000000');	
		}
 	});
	
	$('#txtRePassword').bind('click', function(){		
			var lastvalue = $('#txtRePassword').val();
			if(lastvalue == 'Password'){
				$('#txtRePassword').val('');
			}
			$('#txtRePassword').css('color','#000000');	
 	});

	$('#txtRePassword').bind('blur', function(){
    	
		var lastvalue = $('#txtRePassword').val();
		lastvalue = trim(lastvalue);
		if(lastvalue == '' || lastvalue == 'Password')
		{			
			$('#txtRePassword').val('Password');
			$('#txtRePassword').css('color','#cccccc');	
		}
		else
		{
			$('#txtRePassword').css('color','#000000');	
		}
 	});
	

}


function resetPassword()
{
	
	$("#btnReset").attr("disabled", "true");
	var email = $("#txtEmail").val();
	var password = $("#txtPassword").val();
   	var repassword = $("#txtRePassword").val();
	
	   
   	var info = encodeURI("nocache="+Math.random()+"&processType=RESETPASSWORD&password="+password+"&email="+email+"&repassword="+repassword);
	var uri = encodeURI("/ajax/Login.php");
	
	$('#divReset').html('<img src="/images/ajax-loader.gif" >');
	 	 
	 $.ajax({
   		type: "GET",
   		url: uri,
   		data: info,
		dataType: 'json',
		cache: false,
   		success: function(data,status){	    
						
			if(data.success)			
				$("#divReset").html(data.message);   			
   			
   			$("#btnReset").removeAttr('disabled');	    	
   		}
 	});
}

function fireResetPassword(e)
{
	 var key = e.which||e.keyCode;
	
	if(key == 13) 
	{
		$("#btnReset").click();
	}

}

function checkPassword()
{
	$("#divReset").html("");  
	 
	 var password = $("#txtPassword").val();	
	 
	 var info = encodeURI("nocache="+Math.random()+"&action=CHECKPASSWORD&password="+password);
	 var uri = encodeURI("/ajax/Login.php");
	 	 
	 $.ajax({
  		type: "GET",
  		url: uri,
  		data: info,
  		success: function(data,status){						   
	    	$("#divReset").html(data.message);
  		}
	});
}

function checkRePassword()
{
	$("#divReset").html("");  
	 
	 var repassword = $("#txtRePassword").val();
	 var password = $("#txtPassword").val();
		 
	 var info = encodeURI("nocache="+Math.random()+"&action=CHECKREPASSWORD&repassword="+repassword+"&password="+password);
	 var uri = encodeURI("/ajax/Login.php");
	 
	 $.ajax({
  		type: "GET",
  		url: uri,
  		data: info,
  		success: function(data,status){						   
	    	$("#divReset").html(data.message); 
  		}
	});
	 
}  
	 
	 
