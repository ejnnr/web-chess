<?php
	class ChessGameException extends Exception {}
	
	/*
		Exception codes:
		1 - 100: General errors (empty arguments, ...)
			1: Unknown general error
			2: Empty/null argument
			3: Wrong argument type
			4: Invalid argument
	*/
	
	/**
	* A class representing a game of chess
	*/
	
	class ChessGame
	{
		private $fen; // stores the current fen
		private $board; /* stores the current position on the board:
						   acces via $this->board[$filenumber][$ranknumber] (with both file and rank number of course starting with 0
						   So e4 would be $board[4][3]
						   The actual value stored is the piece on this square
						 */
		private $whitesTurn; // true if it's white's turn, false if it's black's turn
		private $castlings; /* stores which castlings are allowed:
							   array("K" => <white kingside>, "Q" => <white queenside>, "k" => <black kingside>, "q" => <black queenside>)
							 */
		private $enPassant; // stores the current en passant file as a letter; '' if there is no en passant file
		private $halfMoves; // number of half-moves since the last pawn move of capture
		private $moveNumber; // the current move number
		
		define(WHITE_PIECES, array("P", "N", "B", "R", "Q", "K"));
		define(BLACK_PECES), array("p", "n", "b", "r", "q", "k"));
		
		private $moves; // store the game as an array of Move objects
		
		function __construct($fen = "rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1")
		{	
			if (empty($fen) {
				throw new ChessGameException("__construct: fen may not be null", 2);
			}
			
			$this->loadFen($fen);	
		}
	}
?>
