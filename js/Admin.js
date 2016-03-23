// JavaScript Document
$(document).ready(function(){
	initFancyBox();
	initModalDeleteDialog();	
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
        scrolling: 'no',
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

function deletePhoto(obj)
{
	if(confirm("Are you sure to delete?"))
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
					$(obj).parent().parent().fadeOut("slow");
				else
					alert(data.message);
					
			 	$('#dgModalDelete').dialog("close");
   			}
 		});
	}
}

