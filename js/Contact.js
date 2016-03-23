// JavaScript Document
$(document).ready(function(){
	
	initModalDialog();	
	
});//ready

function initModalDialog(){
	
 $( "#dgModal" ).dialog({
	title       : '',
    bgiframe    : true,
    position    : 'center',
    draggable   : false,
	dialogClass : 'modalContact',
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
		  $('#loading').html('<img  src="/images/ajax-loader.gif" ><br>Please wait while we process this post!');
        },
    close : function(err) {
          $('#loading').html('');  
     }
 }); 
}


function sendMessage(){
	
	
	var name = $("#txtName").val();	
	name = encodeURIComponent(name);
	if(name == ''){
		alert('Please enter your name!'); 
		return;
	}
	
	var email = $("#txtEmail").val();	
	email = encodeURIComponent(email);
	if(email == ''){
		alert('Please enter your email!'); 
		return;
	}
	
	var message = $("#txtMessage").val();	
	message = encodeURIComponent(message);	
	if(message == ''){
		alert('Please enter your text!'); 
		return;
	}
	
		
	var info = encodeURI("nocache="+Math.random()+"&processType=SENDMESSAGE&email="+email+"&message="+message+"&name="+name);
	var uri = encodeURI("/ajax/Contact.php");
	
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
					$('#dgModal').dialog("close");
					$("#txtName").val("");		
					$("#txtEmail").val("");		
					$("#txtMessage").val("");		
					alert("Your message has been sent successfully!");
				}
				else{					 
					alert(data.error);					
					$('#dgModal').dialog("close");
				}
				
			}
		});  
}