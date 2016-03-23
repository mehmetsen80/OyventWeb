$(document).ready(function(){	
 setupImageHover();
 initTimeZone();
});//ready

/**
 * Get timezone data (offset and dst)
 *
 *  Inspired by: http://goo.gl/E41sTi
 *
 * @returns {{offset: number, dst: number}}
 */
 
function initTimeZone(){
	$.ajax({
				url: '/ajax/TimeZone.php',
				data: getTimeZoneData(),
				method: 'POST',
				dataType: 'JSON'
			}).done(function(data) {
				//alert(data);				
				//$('#txtTimezone').val(data);				
			});
}
 
function getTimeZoneData() {
	var today = new Date();
  	var jan = new Date(today.getFullYear(), 0, 1);
  	var jul = new Date(today.getFullYear(), 6, 1);
  	var dst = today.getTimezoneOffset() < Math.max(jan.getTimezoneOffset(), jul.getTimezoneOffset());
  
  	return {
    	offset: -today.getTimezoneOffset() / 60,
    	dst: +dst
  	};
}


 function setupImageHover(){
 	 $('li').hover(
          function() {			
            var image = $(this).find('.media');
            var height = image.height();			
            image.stop().animate({ marginTop: -(height - 82) }, 1000);
          }, function() {
            var image = $(this).find('.media');
            var height = image.height();
            image.stop().animate({ marginTop: '0px' }, 1000);
          }
        );
 }

// JavaScript Document
function trim(stringToTrim) {	
	return stringToTrim.replace(/^\s+|\s+$/g,"");
}
function ltrim(stringToTrim) {
	return stringToTrim.replace(/^\s+/,"");
}
function rtrim(stringToTrim) {
	return stringToTrim.replace(/\s+$/,"");
}

String.prototype.ReplaceAll = function(stringToFind,stringToReplace){

    var temp = this;

    var index = temp.indexOf(stringToFind);

        while(index != -1){

            temp = temp.replace(stringToFind,stringToReplace);

            index = temp.indexOf(stringToFind);

        }

        return temp;
 }
 
 