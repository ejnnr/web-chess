$(document).ready(function() {
	$(".dialog").draggable({ containment: "body", scroll: false});
	$(".dialog .closeDialog").click(function(event) {
		$(".dialog").removeClass("visible");
	});
		
	$("#mainNav-openDatabaseButton").click(function(event) {
		$("#openDatabaseDialog").addClass("visible");
	});
	
	$('#flipOrientationButton').on('click', board.flip);
	
	$.get("ajax/getDatabases.php", function(response) {
        var list = response.split(";");
        list.forEach(function(game) {
            var fields = game.split("|");
            $('#openDatabaseDialog-databaseList').html($('#openDatabaseDialog-databaseList').html() + "<li>" + fields[0] + "</li>");
        });
	});
	
	$("#chessboard").resizable({
	    minHeight: 200,
	    minWidth: 200,
	    maxHeight: 700,
	    maxWidth: 700,
	    aspectRatio: 1
	});
});
