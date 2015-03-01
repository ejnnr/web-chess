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

<<<<<<< HEAD
	$('undoButton').on('click', undo());
=======
	$('#undoButton').on('click', function() {
		game.undo();
		board.position(game.fen());
	});
>>>>>>> 13fdddd8fea0dc986ecd83116506f82524d36df5

	$("#chessboard").resizable({
		maxHeight: 700,
	    maxWidth: 700,
	    minHeight: 200,
	    minWidth: 200,
	    aspectRatio: 1,
	    resize: function (event, ui) {
			board.resize();
		}
	});

});
