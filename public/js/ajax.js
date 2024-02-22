function openReq(){
	try{
		return new XMLHttpRequest();
	}
	catch (e){
		try{
			return new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch (e){
			try{
				return new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch (e){
				return false;
			}
		}
	}
}

$("#notifications").ready(function() {
	$("#notifications").fadeOut('swing'); 
	$("#notifypic").click(function(e){
		// stop normal link click
		e.preventDefault();
		if ( $("#notifications").is(":hidden") ) $("#notifications").load('notifications.php?since=1');
		$("#notifications").fadeToggle('fast', 'swing'); 
	});
});

checkNotifications(); 
var aktiv = window.setInterval("checkNotifications()", 30000);

function checkNotifications(){
	// send request
	$.post("notifications.php", function(xml) {
		var notifypic = ""; 
		if ( $("pms", xml).text() + $("posts", xml).text() == 0 ) notifypic = "_out"; 
		$("#notifypic").html('<img src="notifications' + notifypic + '.png" alt="' + $("pms", xml).text() + ' Neue Nachrichten, ' + $("posts", xml).text() + ' Neue Beitr&auml;ge." border=0 height=25 width=36>');
	});
}

function addQuote(postid){
	$.post("quote.php", {id: postid}, function(xml) {
		$("#message").val($("#message").val() + '[q=' + $("user", xml).text() + ']' + $("text", xml).text() + '[/q]\n'); 
	});
}

function showIP(postid){
	$.post("misc2.php", {postid: postid, action: "ip"}, function(xml) {
		$("#ip-"+postid).html("[IP&nbsp;" + $("ip", xml).text() + "]"); 
	});
}