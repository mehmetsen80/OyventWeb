$(document).ready(function(){		
	initLogin();
});//ready

function loginUser()
{	
	$("#btnLogin").attr("disabled", "true");
	var email = $("#txtEmail").val();
	var password = $("#txtPassword").val(); 
	
	$('#divLoginMessage').html('<img src="/images/ajax-loader.gif" >');
	
	//alert("email:"+email+"  password:"+password);
  
    var info = encodeURI("nocache="+Math.random()+"&processType=LOGINUSERVIAWEB&email="+email+"&password="+password);
	var uri = encodeURI("/ajax/Login.php");	
	
	$.ajax({
  		type: "GET",
  		url: uri,
  		data: info,
		dataType: 'json',
		cache: false,
  		success: function(data,status){		
			$("#btnLogin").removeAttr('disabled');	
  			$('#divLoginMessage').html('');
					
				
			//alert("success:"+data.success+"  message:"+data.message);		
			
			
			if(data.success)
				window.location = "/admin/";
			else
				$('#divLoginMessage').html(data.message);
			
			
  			/*data = eval("("+ data +")"); //Parse JSON				
			
			if(data.success && data.message == "OK")			
				window.location = "Redirect.php";			
			else if(!data.success && data.message == "GOTODEACTIVATEPAGE")
				window.location = "../profile/Deactivate.php";
			else
				$("#divLoginMessage").html(data.message);*/
		
		    
  		}
	});
}

function fireLoginUser(e)
{
	 var key = e.which||e.keyCode;
	
	if(key == 13) 
	{
		$("#btnLogin").click();
	}

}

function initLogin()
{
	 $("#divLoginMessage").html("");
	 
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
	
	
	
}
