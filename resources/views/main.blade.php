<!DOCTYPE html>
<html>
	<head>
		<title>WebChess</title>
		<meta charset="UTF8">
		<link type="text/css" rel="stylesheet" href="chessboardjs/css/chessboard-0.3.0.css">
		<link type="text/css" rel="stylesheet" href="css/main.css">
		<link type="text/css" rel="stylesheet" href="themes/default/default.css">
		<link type="text/css" rel="stylesheet" href="jquery-ui/jquery-ui.min.css">
		<script src="jquery/jquery-2.1.3.min.js"></script>
		<script src="jquery-ui/jquery-ui.min.js"></script>
		<script src="colResizable-1.5/colResizable-1.5.min.js"></script>
		<script src="chessboardjs/js/chessboard-0.3.0.js"></script>
		<script src="chessjs/chess.js"></script>
	</head>
	<body>
		<div id="mainNav"><!-- #mainNav: the navigation bar at the top with various commands such as 'New game', ... -->
			<ul>
				<li id="mainNav-openDatabaseButton">Open Database</li>
				<li id="mainNav-createDbButton">Create new database</li>
				<li id="mainNav-createGameButton">New game</li>
			</ul>
			<div class="clear"></div><!-- necessary because the list elements of #mainNav>ul are floated with css -->
		</div>

		<div id="modeNav"><!-- #modeNav: navigation bar at the right edge of the screen, used for changing between different modes -->
			<ul>
				<li>Mode 1</li>
				<li>Mode 2</li>
				<li>Mode 3</li>
			</ul>
		</div>
		
		<div id="main"><!-- #main: the container for the largest part of the page. Necessary to have a consistent padding for chessboard and windows -->
			<div id="main-chessboardWrapper"><!-- a wrapper for the chessboard and some buttons -->
				<ul id="main-chessboardWrapper-buttons">
					<li><div style="cursor:pointer" id= "flipOrientationButton"><img src ="themes/default/img/Rotatebutton.png" style = "height:50px; margin:10px"></div></li>
					<li><div style="cursor:pointer" id= "undoButton"><img src ="themes/default/img/Undobutton.png" style = "height:50px; margin:10px"></div></li>
					<li><div style="cursor:pointer" id= "saveButton"><img src ="themes/default/img/Savebutton.png" style = "height:50px; margin:10px"></div></li>
				</ul>
				<div class="clear"></div><!-- Necessary because the buttons are floated via css -->
				<div id="main-chessboardWrapper-chessboard" style="width: 400px;"></div><!-- the actual chessboard (displayed using chessboard.js) -->
			</div>
			
			<div id="main-windows"><!-- #main-windows: conatains all windows like game list, PGN, ...; windows are displayed as tabs using jquery-ui -->
				<ul>
					<!-- the order the windows are appearing in can be changed here. The first list item will be visible when you load the page -->
					<li><a href="#main-windows-pgn">PGN</a></li>
					<li><a href="#main-windows-games">Games</a></li>
					<li><a href="#main-windows-analysis">Analysis Engine</a></li>
				</ul>
				<div id="main-windows-pgn">
					<p>FEN: <span id="fen"></span></p>
					<p>PGN: <span id="pgn"></span></p>
				</div>
				<div id="main-windows-games">
					<table id="main-windows-games-gameList" width="100%"><!-- this table is filled with ajax and its columns are resizable -->
						<tr>
							<th>White</th>
							<th>Black</th>
							<th>Result</th>
							<th>ECO</th>
							<th>Site</th>
							<th>Event</th>
							<th>Round</th>
							<th>Date</th>
							<th>Tags</th>
						</tr>
					</table>
				</div>
				<div id="main-windows-analysis">

				</div>
			</div>
			
			<div class="dialog" id="openDatabaseDialog" style="width: 500px; position: absolute; top: 100px; left: 300px;">
				<div class="closeDialog"></div>
				<p style="cursor:pointer">List of Databases:</p>
				<ul id="openDatabaseDialog-databaseList">
					<!-- this list is filled via Ajax -->
				</ul>
			</div>
			
			<div class = "dialog" id="createGameDialog" style="width: 300px; position: absolute; top: 100px; left: 300px;">
				<div class="closeDialog"></div>
				<p style="cursor:pointer">Create Game</p>
				<form method=post>
					<i>White Player</i><input type="text" name="White" value=""><br>
					<i>Black Player</i><input type="text" name="Black" value=""><br>
					<i>Result</i><input type="text" name="Result" value=""><br>
					<i>Eco</i><input type="text" name="Eco" value=""><br>
					<i>Site</i><input type="text" name="Site" value=""><br>
					<i>Event</i><input type="text" name="Event" value=""><br>
					<i>Round</i><input type="text" name="Round" value=""><br>
					<i>Date</i><input type="text" name="Date" value=""><br>
					<i>Tags</i><input type="text" name="newGameTags" value=""><br>
					<input type = "button" name="dialog-createButton" value="Create" class="dialog-createButton">
				</form>
			</div>
			
			<div class = "dialog" id="createDbDialog" style="width: 300px; position: absolute; top: 100px; left: 300px;">
				<div class="closeDialog"></div>
				<p style="cursor:pointer">Create Database</p>
				<form method=post>
					<i>Database Name</i><input type="text" name="newDbName" value=""><br>
					<i>Visibility <sub style = "font-size:10px">(private/shared/public)</sub></i><input type="text" name="visibility" value=""><br>
					<i>Tags</i><input type="text" name="newDbTags" value=""><br>
					<input type = "button" name="dialog-createDbButton" value="Create" class="dialog-createButton">
				</form>
			</div>
		</div>
		
		<script>
			var board,
				game = new Chess(),
				fenEl = $('#fen'),
				pgnEl = $('#pgn');

			// do not pick up pieces if the game is over
			// only pick up pieces for the side to move
			var onDragStart = function(source, piece, position, orientation) {
				if (game.game_over() === true ||
					(game.turn() === 'w' && piece.search(/^b/) !== -1) ||
					(game.turn() === 'b' && piece.search(/^w/) !== -1)) {
					return false;
				}
			};

			var onDrop = function(source, target) {
		    	// see if the move is legal
		    	var move = game.move({
					from: source,
					to: target,
					promotion: 'q' // NOTE: always promote to a queen for example simplicity
				});

				// illegal move
				if (move === null) return 'snapback';

				updateStatus();
			};

			// update the board position after the piece snap 
			// for castling, en passant, pawn promotion
			var onSnapEnd = function() {
				board.position(game.fen());
			};

			var updateStatus = function() {
				fenEl.html(game.fen());
				pgnEl.html(game.pgn());
			};
		
			var cfg = {
				draggable: true,
				position: 'start',
				onDragStart: onDragStart,
				onDrop: onDrop,
				onSnapEnd: onSnapEnd
			};
			board = new ChessBoard('main-chessboardWrapper-chessboard', cfg);
			updateStatus();
		</script>

		<script src="js/ui.js"></script>
	</body>
</html>

