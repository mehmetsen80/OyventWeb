// JavaScript Document
$(document).ready(function(){
});

function chooseAlbum(){
	var albumID = $("#selAlbum").val();	
		
	if(albumID == null || albumID == 0 || albumID == ''){
		alert("Please select an album first!");
		return;
	}
	
	window.location = "?albumID="+albumID;
}