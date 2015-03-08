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


	$('#undoButton').on('click', function() {
		game.undo();
		board.position(game.fen());
		// update Status elements (FEN, PGN)
		updateStatus();
	});


	$("#main-chessboardWrapper-chessboard").resizable({
		maxHeight: 700,
		maxWidth: 700,
		minHeight: 200,
		minWidth: 200,
		containment: "#main-chessboardWrapper",
		aspectRatio: 1,
		resize: function (event, ui) {
			board.resize();
		}
	});
	
	var tabs = $("#main-windows").tabs();
	tabs.find(".ui-tabs-nav").sortable({
		axis: "x",
		stop: function() {
			tabs.tabs("refresh");
		}
	});
	
	$.get("ajax/getGames.php", function(response) {
	var list = response.split(";");
	list.forEach(function(game) {
		var fields = game.split("|");
		$('#main-windows-games-gameList').html($('#main-windows-games-gameList').html() + "<tr><td>" + fields[6] + " (" + fields[9] + ")" + "</td><td>" + fields[7] + " (" + fields[10] + ")" + "</td><td>" + fields[8] + "</td><td>" + fields[11] + "</td><td>" + fields[1] + "</td><td>" + fields[2] + "</td><td>" + fields[3] + "</td><td>" + fields[4] + "</td><td>" + fields[14] + "</td></tr>");
	});
	$("#main-windows-games-gameList").colResizable({
			liveDrag: true,
			partialRefresh: true,
			postbackSafe: true
		});
	});
});
