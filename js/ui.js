// only run code when document is ready. PUT EVERYTHING IN HERE!
$(document).ready(function() {
	$(".dialog").draggable({ containment: "body", scroll: false, handle:"p"}); // all dialogs can be dragged around but not outside of the viewport

	$(".dialog .closeDialog").click(function(event) {
		$(this).parent('div').removeClass("visible"); // make dialogs invisible when clicking the close button
	});
		
	$("#mainNav-openDatabaseButton").click(function(event) {
		$("#openDatabaseDialog").addClass("visible");// open the dialog for opening databases
	});
	
	$("#mainNav-createGameButton").click(function(event) {
		$("#createGameDialog").addClass("visible"); // open the dialog for opening databases
	});
	
	$("#mainNav-createDbButton").click(function(event) {
		$("#createDbDialog").addClass("visible"); // open the dialog for opening databases
	});
	
	
	$('#flipOrientationButton').on('click', board.flip); // board is the variable the chessboard is stored in. flip is a function provided by chessboard.js
	$('#undoButton').on('click', function() {
		game.undo();                // undo the move with chess.js
		board.position(game.fen()); // update the board
		updateStatus();             // update status elements (FEN, PGN)
	});
	
	$.get("ajax/getDatabases.php", function(response) { // get a list of available databases via Ajax
		var list = response.split(";"); // split the response into single databases
		list.forEach(function(database) {
			var fields = database.split("|");

			$('#openDatabaseDialog-databaseList').html($('#openDatabaseDialog-databaseList').html() + "<li>" + fields[0] + "</li>"); // fill the list in the dialog for opening databases
		});
	});

	$("#main-chessboardWrapper-chessboard").resizable({ // make chessboard resizable with jquery-ui
		maxHeight: 700,
		maxWidth: 700,
		minHeight: 200,
		minWidth: 200,
		containment: "#main-chessboardWrapper", // to prevent conflict with #main-windows
		aspectRatio: 1,
		resize: function (event, ui) {
			board.resize(); // redraw the board using chessboard.js
		}
	});
	
	var tabs = $("#main-windows").tabs(); // display the windows as tabs using jquery-ui
	tabs.find(".ui-tabs-nav").sortable({ // allow the user to sort the tabs
		axis: "x",
		stop: function() {
			tabs.tabs("refresh");
		}
	});
	
	tabs.find(".ui-tabs-nav").disableSelection();
	
	$.get("ajax/getGames.php", function(response) { // get a list of games
		var list = response.split(";");
		
		list.forEach(function(game) {
			
			var fields = game.split("|");
			
			var changeResult = function(dbresult) { // change the  result to more readable formats
				switch(dbresult) {
					case "0":
						return "0-1";
					case "0.5":
						return "0.5-0.5";
					case "1":
						return"1-0";
				}
			};
			$('#main-windows-games-gameList').html($('#main-windows-games-gameList').html() + "<tr><td>" + fields[6] + " (" + fields[9] + ")" + "</td><td>" + fields[7] + " (" + fields[10] + ")" + "</td><td>" + changeResult(fields[8]) + "</td><td>" + fields[11] + "</td><td>" + fields[1] + "</td><td>" + fields[2] + "</td><td>" + fields[3] + "</td><td>" + fields[4] + "</td><td>" + fields[14] + "</td></tr>"); // fill the list in #main-windows-games
		});
		$("#main-windows-games-gameList").colResizable({ // make columns resizable
				liveDrag: true,       // just looks better
				partialRefresh: true, // necessary when using Ajax
				postbackSafe: true    // save the user's layout in the browser
		});
	});
});
