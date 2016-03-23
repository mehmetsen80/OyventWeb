// JavaScript Document
$(document).ready(function(){		
	initRegister();	
});//ready


function signUpUser()
{   
  var fullname = $("#txtFullName").val();
  var email = $("#txtEmail2").val();  
  var password = $("#txtPassword2").val();  
  
  $("#btnCreate").attr("disabled", "true");
  $("#divSignUpMessage").html('<img src="/images/ajax-loader.gif" >');
  
  var info = encodeURI("nocache="+Math.random()+"&processType=SIGNUPUSERVIAWEB&fullname="+fullname+"&email="+email+"&password="+password);
	  var uri = encodeURI("/ajax/Register.php");
     
  	  $.ajax({
  		  type: "GET",
  			url: uri,
  			data: info,
			dataType: 'json',
			cache: false,
  			success: function(data,status){			
				$("#btnCreate").removeAttr('disabled');
				//$("#divSignUpMessage").html('');
				//alert("success:"+data.success+"  message:"+data.message);				
								
				if(data.success)								
					window.location = "/admin/";
				else						  			
		  			$("#divSignUpMessage").html(data.message);								  			
  			}
		});  
} 

function fireRegisterUser(e)
{
	 var key = e.which||e.keyCode;
	
	if(key == 13) 
	{
		$("#btnCreate").click();
	}

}



function validateEmailSignUpUser()
{
   var validEmail =/\w+([\.-]?\w+)*@\w+([\.-]?\w+)*\.(\w{2}|(com|co|me|tv|net|org|edu|int|mil|gov|arpa|biz|aero|name|coop|info|pro|museum))*/;
	
	var email = $("#txtEmail2").val();	
	
	if (!email.match(validEmail))
   {
		$("#txtEmail2").css("color","#FF0000");
	}
	else
	{
		$("#txtEmail2").css("color","#000000");	
	}	
}


function initRegister()
{ 
  $("#divSignUpMessage").html(""); 
  
  $('#txtFullName').bind('click', function(){		
			var lastvalue = $('#txtFullName').val();
			if(lastvalue == 'Full Name'){
				$('#txtFullName').val('');
			}
			$('#txtFullName').css('color','#000000');	
 	});

	$('#txtFullName').bind('blur', function(){
    	
		var lastvalue = $('#txtFullName').val();
		lastvalue = trim(lastvalue);
		if(lastvalue == '' || lastvalue == 'Full Name')
		{			
			$('#txtFullName').val('Full Name');
			$('#txtFullName').css('color','#cccccc');	
		}
		else
		{
			$('#txtFullName').css('color','#000000');	
		}
 	});
	
	
	$('#txtEmail2').bind('click', function(){		
			var lastvalue = $('#txtEmail2').val();
			if(lastvalue == 'Email'){
				$('#txtEmail2').val('');
			}
			$('#txtEmail2').css('color','#000000');	
 	});

	$('#txtEmail2').bind('blur', function(){
    	
		var lastvalue = $('#txtEmail2').val();
		lastvalue = trim(lastvalue);
		if(lastvalue == '' || lastvalue == 'Email')
		{			
			$('#txtEmail2').val('Email');
			$('#txtEmail2').css('color','#cccccc');	
		}
		else
		{
			$('#txtEmail2').css('color','#000000');	
		}
 	});
	
	$('#txtPassword2').bind('click', function(){		
			var lastvalue = $('#txtPassword2').val();
			if(lastvalue == 'Password'){
				$('#txtPassword2').val('');
			}
			$('#txtPassword2').css('color','#000000');	
 	});

	$('#txtPassword2').bind('blur', function(){
    	
		var lastvalue = $('#txtPassword2').val();
		lastvalue = trim(lastvalue);
		if(lastvalue == '' || lastvalue == 'Password')
		{			
			$('#txtPassword2').val('Password');
			$('#txtPassword2').css('color','#cccccc');	
		}
		else
		{
			$('#txtPassword2').css('color','#000000');	
		}
 	});
  
}
