// JavaScript Document

$(document).ready(function(){	
	initEmail();				   
});//ready

function initEmail(){
	
	$("#divForgotMessage").html("");
	 
	 $('#txtEmail').bind('click', function(){		
			var lastvalue = $('#txtEmail').val();
			if(lastvalue == 'Email'){
				$('#txtEmail').val('');
			}
			$('#txtEmail').css('color','#000000');	
 	});

	$('#txtEmail').bind('blur', function(){
    	
		var lastvalue = $('#txtEmail').val();
		lastvalue = trim(lastvalue);
		if(lastvalue == '' || lastvalue == 'Email')
		{			
			$('#txtEmail').val('Email');
			$('#txtEmail').css('color','#cccccc');	
		}
		else
		{
			$('#txtEmail').css('color','#000000');	
		}
 	});
}

function fireForgotPassword(e)
{
	 var key = e.which||e.keyCode;
	
	if(key == 13) 
	{
		$("#btnForgot").click();
	}

}

function forgotPassword()
{
	$("#btnForgot").attr("disabled", "true");
	var email = $("#txtEmail").val();	
	
	   	
	var info = "nocache="+Math.random()+"&processType=FORGOTPASSWORD&email="+email;
	var uri = "/ajax/Login.php";
	 	 
	 $.ajax({
   		type: "GET",
   		url: uri,
   		data: info,
		dataType: 'json',
		cache: false,
   		success: function(data,status){			
		   			
			$("#divForgotMessage").html(data.message);   			
			$("#btnForgot").removeAttr('disabled');	    		   
   		}
 	});
}




function validateEmail()
 {
    var validEmail =/\w+([\.-]?\w+)*@\w+([\.-]?\w+)*\.(\w{2}|(com|net|org|edu|int|mil|gov|arpa|biz|aero|name|coop|info|pro|museum))*/;
	
	var email = $("#txtEmail").val();
	 
	if (!email.match(validEmail))
    {
		$("#txtEmail").css("color","#FF0000");
	}
	else
	{
		$("#txtEmail").css("color","#00FF00");
	}
 }
