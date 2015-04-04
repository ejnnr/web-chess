<?php
/**
 * This file contains the class Position and the corresponding exception class.
 *
 * This file includes square.php and Move.php.
 */

/**
 * Include necessary files
 */
require_once 'square.php';

/**
 * Include necessary files
 */
require_once 'Move.php';

/**
 * Represents an exception thrown by Position.
 * List of Exception Codes:
 *   1: Unknown internal error
 *   2: Null argument
 *   3: Wrong argument count
 *   4: Wrong argument type
 *   5: Invalid argument value (general)
 *   6: Invalid argument syntax (strings)
 *   7: Numerical argument outside of allowed range
 * 101: Trying to load impossible position (general)
 * 102: Impossible position: one or two kings are missing
 * 103: Impossible position: too many pieces of one kind
 * 104: Impossible position: side not to move is in check
 * 105: Impossible position: pawns on 1st or 8th rank
 * 106: Invalid en passant square
 * 110: Invalid FEN syntax
 * 111: Invalid FEN syntax: no castling flags
 * 112: Invalid FEN syntax: one or more ranks don't contain 8 squares
 * 119: Invalid FEN syntax: wrong number of sections TODO: remove this!
 */


class PositionException extends Exception {}

/**
 * Represents a position that could occur in a game of chess
 */

class Position
{
	private $fen;

	private $board;
	/**
	 * Constructor of Position
	 *
	 * @param mixed $position The position to load as a FEN
	 */

	function __construct($position) // TODO: support other formats than FEN
	{
		$this->loadFEN($position);
	}

	/**
	 * Returns the FEN of the position
	 *
	 * @return string The FEN
	 */

	function getFEN()
	{
		return $this->fen;
	}

	/**
	 * Returns the position as an array: [['R', 'N', 'B', ...], ['P', 'P', ...], ..., ['r', 'n', ...]]
	 *
	 * @return array The position as an 8x8 array
	 */

	function getArray()
	{

	}

	/**
	 * parses the given FEN and loads it
	 *
	 * @param string $fen The FEN to be loaded
	 * @return true on success, false on failure
	 */
	function loadFEN($fen)
	{
		if (empty($fen)) {
			throw new PositionException('function loadFEN: fen may not be empty', 2);
		}

		if (!is_string($fen)) {
			throw new PositionException('function loadFEN: fen must be of type string', 4);
		}

		$matches = array(); // used to store the match result of the RegEx
		// the regular expression is structured as follows (spaces aren't listed here):
		//  #                            the delimiter
		//  ^                            beginning of string (to prevent that only part of the string macthes)
		//  ([1-8KQRBNPkqrbnp]{1,8}/)    one rank followd by a / ...
		//  {7}                          ... seven times
		//  [1-8KQRBNPkqrbnp]{1-8}       and the last one without a following /
		//  [wb]                         whose turn it is
		//  (?P<castling>                name the following sub expression castling
		//  K?Q?k?q?)                    the castling flags ...
		//  |(?P<noCastling>\-)          ... or no castling
		//  ([a-h][36])|\-               the en passant file or -
		//  [0-9]+                       number of half-moves since last capture or pawn move
		//  [0-9]+                       move count
		//  $                            end of string (see ^)
		//  #                            the ending delimiter
		// '=== 0' (instead of a simple '!') must be used because preg_macth returns FALSE on failure and 0 if nothing's been found
		// $matches must be used because the RegEx would allow castling flags to be omitted

		$regExResult = preg_match('#^([1-8KQRBNPkqrbnp]{1,8}/){7}[1-8KQRBNPkqrbnp]{1,8} [wb] (K?Q?k?q?|-) ([a-h][36]|-) [0-9]+ [0-9]+$#', $fen, $matches);

		if ($regExResult === FALSE) { // an error occurred, this should never happen
			throw new PositionException('function loadFEN: error parsing fen with preg_match', 1);
		}

		if ($regExResult === 0) { // RegEx doesn't match
			throw new PositionException('function loadFEN: fen syntax is invalid', 110);
		}

		// check if castling flags are missing (the RegEx doesn't do this well)
		if (empty($matches[2])) {
			throw new PositionException('function loadFEN: castling flags missing', 111);
		}

		// split fen
		$sections = explode(" ", $fen);

		// validate fen
		// this is actually unnecessary because it was already checked with the RegEx but as long as this is still in testing it might be useful in case the RegEx doesn't work
		// TODO: remove this!
		if (!(count($sections) == 6)) {
			throw new PositionException('function loadFEN: Invalid FEN syntax', 119);
		}



		// check if all kings are on the bord
		if (strpos($sections[0], "K") === FALSE || strpos($sections[0], "k") === FALSE)
		{
			throw new PositionException('Function loadFen: there must be a black and a white king on the board.', 102);
		}

		// split position into ranks
		$ranks = explode('/', $sections[0]);

		// variable to keep track of the total number of pieces of one kind
		$pieceCountOf = array(
			'K' => 0,
			'Q' => 0,
			'R' => 0,
			'B' => 0,
			'N' => 0,
			'P' => 0,
			'k' => 0,
			'q' => 0,
			'r' => 0,
			'b' => 0,
			'n' => 0,
			'p' => 0,
		);

		$this->board == array(); // clean board
		$i_rank = 0;
		$ranks = array_reverse($ranks); // FEN starts with 8th rank and ends with 1st rank so it has to be reversed
		foreach ($ranks as $rank) // go through all of the ranks (seperated by '/' in fen)
		{
			$i_file = 0;

			while (!empty($rank)) // since every square that was parsed is removed from $rank, this expression can be used to check if we are done
			{
				if (is_numeric(substr($rank, 0, 1))) // empty square(s)
				{
					$this->board[] = ''; // add an empty square to the board
					$new = ((int) substr($rank, 0, 1)) - 1; // decrease the number of empty squares by one
					if ($new == 0) // if the new number is zero, set it to ''
					{
						$new = '';
					}
					$rank = $new . substr($rank, 1); // replace the old number of empty squares with the new one (substr($renk, 1) gives $rank without the first letter)
				}
				else // there's a piece on the square
				{
					if ((substr($rank, 0, 1) == 'P' || substr($rank, 0, 1) == 'p') && ($i_rank == 0 || $i_rank == 7)) {
						throw new PositionException('function loadFEN: there is a pawn on the backrank', 105);
					}
					$pieceCountOf[substr($rank, 0, 1)]++;
					$this->board[] = substr($rank, 0, 1); // get the first character of the current rank (i.e. the current piece) and add it to board
					$rank = substr($rank, 1); // strip the piece just added from $rank
				}

				$i_file++;
			}

			if ($i_file != 8) {
				throw new PositionException('function loadFEN: each rank must have 8 files', 112);
			}
			$i_rank++;

		}


		// check if there are too many/few pieces on the board

		if ($pieceCountOf['K'] != 1) {
			throw new PositionException('function loadFEN: there must be exactly one white king on the board', 102);
		}

		if ($pieceCountOf['k'] != 1) {
			throw new PositionException('function loadFEN: there must be exactly one black  king on the board', 102);
		}

		if (($pieceCountOf['Q'] + $pieceCountOf['P']) > 9) {
			throw new PositionException('function loadFEN: too many white queens', 103);
		}

		if (($pieceCountOf['q'] + $pieceCountOf['p']) > 9) {
			throw new PositionException('function loadFEN: too many black queens', 103);
		}

		if (($pieceCountOf['R'] + $pieceCountOf['P']) > 10) {
			throw new PositionException('function loadFEN: too many white rooks', 103);
		}

		if (($pieceCountOf['r'] + $pieceCountOf['p']) > 10) {
			throw new PositionException('function loadFEN: too many black rooks', 103);
		}

		if (($pieceCountOf['B'] + $pieceCountOf['P']) > 10) {
			throw new PositionException('function loadFEN: too many white bishops', 103);
		}

		if (($pieceCountOf['b'] + $pieceCountOf['p']) > 10) {
			throw new PositionException('function loadFEN: too many black bishops', 103);
		}

		if (($pieceCountOf['N'] + $pieceCountOf['P']) > 10) {
			throw new PositionException('function loadFEN: too many white knights', 103);
		}

		if (($pieceCountOf['n'] + $pieceCountOf['p']) > 10) {
			throw new PositionException('function loadFEN: too many black knights', 103);
		}

		if ($pieceCountOf['P'] > 8) {
			throw new PositionException('function loadFEN: too many white pawns', 103);
		}

		if ($pieceCountOf['p'] > 8) {
			throw new PositionException('function loadFEN: too many black pawns', 103);
		}

		if (($pieceCountOf['Q'] + $pieceCountOf['R'] + $pieceCountOf['B'] + $pieceCountOf['N'] + $pieceCountOf['P']) > 15) {
			throw new PositionException('function loadFEN: too many white pieces', 103);
		}

		if (($pieceCountOf['q'] + $pieceCountOf['r'] + $pieceCountOf['b'] + $pieceCountOf['n'] + $pieceCountOf['p']) > 15) {
			throw new PositionException('function loadFEN: too many black pieces', 103);
		}

		if ((($pieceCountOf['Q'] > 0 ? $pieceCountOf['Q'] - 1 : 0)
		   + ($pieceCountOf['R'] > 1 ? $pieceCountOf['R'] - 2 : 0)
		   + ($pieceCountOf['B'] > 1 ? $pieceCountOf['B'] - 2 : 0)
		   + ($pieceCountOf['N'] > 1 ? $pieceCountOf['N'] - 2 : 0)
		   +  $pieceCountOf['P']) > 8) {
			throw new PositionException('function loadFEN: Too many promoted white pieces', 103);
		}

		if ((($pieceCountOf['q'] > 0 ? $pieceCountOf['q'] - 1 : 0)
		   + ($pieceCountOf['r'] > 1 ? $pieceCountOf['r'] - 2 : 0)
		   + ($pieceCountOf['b'] > 1 ? $pieceCountOf['b'] - 2 : 0)
		   + ($pieceCountOf['n'] > 1 ? $pieceCountOf['n'] - 2 : 0)
		   +  $pieceCountOf['p']) > 8) {
			throw new PositionException('function loadFEN: Too many promoted black pieces', 103);
		}

		$this->turn = $sections[1];

		/* set possible castlings */
		$this->castlings = array("K" => false, "Q" => false, "k" => false, "q" => false);

		if (strpos($sections[2], "K") !== false) /* '!== false' is necessary. See http://php.net/manual/en/function.strpos.php for further information */
		{
			$this->castlings["K"] = true;
		}

		if (strpos($sections[2], "Q") !== false)
		{
			$this->castlings["Q"] = true;
		}

		if (strpos($sections[2], "k") !== false)
		{
			$this->castlings["k"] = true;
		}

		if (strpos($sections[2], "q") !== false)
		{
			$this->castlings["q"] = true;
		}

		// set en passant square
		if ($sections[3] != "-")
		{
			if (((string2square($sections[3]) & 56) / 8) != ($this->turn == 'w' ? 2 : 5)) { // if it's white's turn, en passant sqaure mudt be on sixth rank, otherwise on third
				throw new PositionException('function loadFEN: en passant square can\'t be ' . $sections[3] . ' because it\'s ' . ($this->turn == 'w' ? 'white\'s' : 'black\'s') . ' turn', 106);
			}

			if ($this->board[string2square($sections[3]) + ($this->turn == 'w' ? 8 : (-8))] != ($this->turn == 'w' ? 'P' : 'p')) {
				throw new PositionException('function loadFEN: en passant square is invalid: no pawn that could be taken', 106);
			}
			$this->enPassant = $sections[3];
		}
		else
		{
			// no en passant square
			$this->enPassant = "";
		}

		/* set number of half-moves since the last pawn move of capture */
		$this->halfMoves = $sections[4];

		/* set move-number */
		$this->moveNumber = $sections[5];

		$this->fen = $fen;
	}

	/**
	 * Sets the position to the starting position
	 */

	function reset()
	{

	}

	/**
	 * Returns true if the game is draw by fifty moves rule
	 *
	 * Fifty moves must have been played without a piece being captured or a pawn beeing moved by either side
	 *
	 * @return boolean true if draw, false if not
	 */

	function isFiftyMoves()
	{

	}


	/**
	 * Returns true if the game is drawn by stalemate
	 *
	 * True if the side to move hasn't got any valid moves but isn't in check
	 *
	 * @return boolean true if draw, false if not
	 */

	function isStaleMate()
	{

	}


	/**
	 * Returns true if the side to move is checkmated
	 *
	 * @return boolean true if checkmate, false if not
	 */

	function isMate()
	{

	}


	/**
	 * Returns true if the side to move is in check
	 *
	 * @return boolean true if in check, false if not
	 */

	function inCheck()
	{

	}


	/**
	 * Check if the given move is legal
	 *
	 *
	 * @param Move $move the move to check
	 * @return boolean true if move is legal, false if not
	 */

	function isLegalMove($move)
	{

	}

	/**
	 * Checks if the move is possible for a king (geometrically)
	 *
	 * @param  integer $departure   the square of departure
	 * @param  integer $destination the square of destination
	 * @return boolean true if possible, otherwise false
	 */
	private function possibleKingMove($departure, $destination)
	{
		if (!validateSquare($departure)) {
			throw new PositionException('function possibleKingMove: invalid departure', 7);
		}

		if (!validateSquare($destination)) {
			throw new PositionException('function possibleKingMove: invalid destination', 7);
		}

		return ((abs(getFile($departure) - getFile($destination)) == 1) && (abs(getRank($departure) - getRank($destination)) == 1));
	}

	/**
	 * Checks if the move is possible for a queen (geometrically)
	 *
	 * @param  integer $departure   the square of departure
	 * @param  integer $destination the square of destination
	 * @return boolean true if possible, otherwise false
	 */
	private function possibleQueenMove($departure, $destination)
	{
		if (!validateSquare($departure)) {
			throw new PositionException('function possibleQueenMove: invalid departure', 7);
		}

		if (!validateSquare($destination)) {
			throw new PositionException('function possibleQueenMove: invalid destination', 7);
		}

		return (possibleRookMove($departure, $destination) || possibleBishopMove($departure, $destination));
	}

	/**
	 * Checks if the move is possible for a rook (geometrically)
	 *
	 * @param  integer $departure   the square of departure
	 * @param  integer $destination the square of destination
	 * @return boolean true if possible, otherwise false
	 */
	private function possibleRookMove($departure, $destination)
	{
		if (!validateSquare($departure)) {
			throw new PositionException('function possibleRookMove: invalid departure', 7);
		}

		if (!validateSquare($destination)) {
			throw new PositionException('function possibleRookMove: invalid destination', 7);
		}

		return ((((getRank($departure) - getRank($destination)) == 0) && ((getFile($departure) - getFile($destination)) != 0)) || (((getRank($departure) - getRank($destination)) != 0) && ((getFile($departure) - getFile($destination)) == 0)));
	}

	/**
	 * Checks if the move is possible for a bishop (geometrically)
	 *
	 * @param  integer $departure   the square of departure
	 * @param  integer $destination the square of destination
	 * @return boolean true if possible, otherwise false
	 */
	private function possibleBishopMove($departure, $destination)
	{
		if (!validateSquare($departure)) {
			throw new PositionException('function possibleBishopMove: invalid departure', 7);
		}

		if (!validateSquare($destination)) {
			throw new PositionException('function possibleBishopMove: invalid destination', 7);
		}

		return ((abs(getFile($departure) - getFile($destination)) == abs(getRank($departure) - getRank($destination))) && (($getFile($departure) - getFile($destination)) == 0));
	}

	/**
	 * Checks if the move is possible for a knight (geometrically)
	 *
	 * @param  integer $departure   the square of departure
	 * @param  integer $destination the square of destination
	 * @return boolean true if possible, otherwise false
	 */
	private function possibleKnightMove($departure, $destination)
	{
		if (!validateSquare($departure)) {
			throw new PositionException('function possibleKnightMove: invalid departure', 7);
		}

		if (!validateSquare($destination)) {
			throw new PositionException('function possibleKnightMove: invalid destination', 7);
		}

		return (((abs(getFile($departure) - getFile($destination)) == 2) && (abs(getRank($departure) - getRank($destination)) == 1)) || ((abs(getFile($departure) - getFile($destination)) == 1) && (abs(getRank($departure) - getRank($destination)) == 2)));
	}

	/**
	 * Checks if a certain piece attacks a certain square
	 *
	 * This function checks the following things:
	 *  * If the piece in question can move to the square (geometrically)
	 *  * If there is any piece in the way
	 *  * If the square is attakced en passant
	 * It does NOT check:
	 *  * Whose turn it is or by what piece the square is occupied
	 *  * If moving the piece to that square would leave the king in check
	 *  * For pawns: if the pawn could MOVE onto that square (since pawns move differently depending on whether or not it's a capture
	 *  * Castling (since castling is a never a capturing move)
	 * 
	 * @param  integer $departure   the square currently occupied by the piece
	 * @param  integer $destination the square that is (not) attackes
	 * @param  array   $board       the board to use for the checks (equal to $this->board if left out)
	 * @return boolean true if the piece attacks the square, otherwise false
	 */

	private function attacks($departure, $destination, $board = null)
	{
		// default for $board is $this->board
		if (empty($board)) {
			$board = $this->board;
		}

		// check if bith squares are valid
		if (!validateSquare($departure)) {
			throw new PositionException('function attacks: departure is no valid square', 7);
		}
		if (!validateSquare($destination)) {
			throw new PositionException('function attacks: destination is no valid square', 7);
		}

		// check if $board has 64 squares
		if (!count($board) == 64) {
			throw new PositionException('function attacks: board must have 64 squares', 5);
		}

		// validate each square
		foreach ($board as $square) {
			// check if the square is a string
			if (!is_string($square)) {
				throw new PositionException('function attacks: there are non-string values in board', 4);
			}
			// check if the square has a valid value
			if (!in_array($square, ['K', 'Q', 'R', 'B', 'N', 'P', 'k', 'q', 'r', 'b', 'n', 'p', ''])) {
				throw new PositionException('function attacks: there are invalid values in board', 5);
			}
		}

		// depending on which type of piece is on departure different things need to be checked
		switch ($board[$departure]) {
		case '':
			throw new PositionException('function attacks: departure square doesn\'t contain a piece', 5);
		case 'K':
		case 'k':
			return possibleKingMove($departure, $destination);
		case 'N':
		case 'n':
			return possibleKnightMove($departure, $destination);
		case 'R':
		case 'r':
			if (!possibleRookMove($departure, $destination)) {
				return FALSE;
			}

			// get the direction of movement (-1, 0 or 1)
			if ((getFile($destination) - getFile($departure)) == 0) {
				$fileMovement = 0;
			} elseif ((getFile($destination) - getFile($departure)) > 0) {
				$fileMovement = 1;
			} elseif ((getFile($destination) - getFile($departure)) < 0) {
				$fileMovement = -1;
			}
			if ((getRank($destination) - getRank($departure)) == 0) {
				$rankMovement = 0;
			} elseif ((getRank($destination) - getRank($departure)) > 0) {
				$rankMovement = 1;
			} elseif ((getRank($destination) - getRank($departure)) < 0) {
				$rankMovement = -1;
			}

			$file = getFile($departure) + $fileMovement;
			$rank = getRank($departure) + $rankMovement;
			// go ine square at a time in the direction of the move until hitting another piece or reaching destination
			while (($board[array2square($file, $rank)] == '') && array2square([$file, $rank]) != $destination) {
				$file += $fileMovement;
				$rank += $rankMovement;
			}
			
			// if we've reached destination, the move is possible, otherwise there is something in the way
			return (array2square([$file, $rank]) == $destination);
		case 'B':
		case 'b':
			if (!possibleBishopMove($departure, $destination)) {
				return FALSE;
			}

			if ((getFile($destination) - getFile($departure)) == 0) {
				$fileMovement = 0;
			} elseif ((getFile($destination) - getFile($departure)) > 0) {
				$fileMovement = 1;
			} elseif ((getFile($destination) - getFile($departure)) < 0) {
				$fileMovement = -1;
			}
			if ((getRank($destination) - getRank($departure)) == 0) {
				$rankMovement = 0;
			} elseif ((getRank($destination) - getRank($departure)) > 0) {
				$rankMovement = 1;
			} elseif ((getRank($destination) - getRank($departure)) < 0) {
				$rankMovement = -1;
			}

			$file = getFile($departure) + $fileMovement;
			$rank = getRank($departure) + $rankMovement;
			while (($board[array2square($file, $rank)] == '') && array2square([$file, $rank]) != $destination) {
				$file += $fileMovement;
				$rank += $rankMovement;
			}
			
			return (array2square([$file, $rank]) == $destination);
		case 'Q':
		case 'q':
			if (!possibleQueenMove($departure, $destination)) {
				return FALSE;
			}

			if ((getFile($destination) - getFile($departure)) == 0) {
				$fileMovement = 0;
			} elseif ((getFile($destination) - getFile($departure)) > 0) {
				$fileMovement = 1;
			} elseif ((getFile($destination) - getFile($departure)) < 0) {
				$fileMovement = -1;
			}
			if ((getRank($destination) - getRank($departure)) == 0) {
				$rankMovement = 0;
			} elseif ((getRank($destination) - getRank($departure)) > 0) {
				$rankMovement = 1;
			} elseif ((getRank($destination) - getRank($departure)) < 0) {
				$rankMovement = -1;
			}

			$file = getFile($departure) + $fileMovement;
			$rank = getRank($departure) + $rankMovement;
			while (($board[array2square($file, $rank)] == '') && array2square([$file, $rank]) != $destination) {
				$file += $fileMovement;
				$rank += $rankMovement;
			}
			
			return (array2square([$file, $rank]) == $destination);
		case 'P':
			// we only have to check for capturing movement
			return ((abs(getFile($departure) - getFile($destination)) == 1)
				&& ((getRank($departure) - getRank($destination)) == -1));
		case 'p':
			return ((abs(getFile($departure) - getFile($destination)) == 1)
				&& ((getRank($departure) - getRank($destination)) == 1));
		}
	}

	/**
	 * Checks if a certain square is attacked bz at least one piece of a certain color
	 *
	 * This function uses attacks() for every piece of the color specified with $turn.
	 * Be careful: even if the square is occupied by a piece of the attacking color, this function may still return true.
	 *
	 * @param integer $square The square to check
	 * @param string  $turn   The color to attack the square; if left out, this defaults to $this->turn
	 * @param array   $board  The board to use; defaults to $this->board
	 * @return boolean true if it is attacked, otherwise false
	 */
	private function isAttacked($square, $turn = null, $board = null)
	{
		if (empty($turn)) {
			$turn = $this->turn;
		}
		if (empty($board)) {
			$board = $this->board;
		}

		if (!validateSquare($square)) {
			throw new PositionException('function isAttacked: square is no valid square', 7);
		}

		// check if $board has 64 squares
		if (!count($board) == 64) {
			throw new PositionException('function isAttacked: board must have 64 squares', 5);
		}

		// validate each square
		foreach ($board as $square) {
			// check if the square is a string
			if (!is_string($square)) {
				throw new PositionException('function isAttacked: there are non-string values in board', 4);
			}
			// check if the square has a valid value
			if (!in_array($square, ['K', 'Q', 'R', 'B', 'N', 'P', 'k', 'q', 'r', 'b', 'n', 'p', ''])) {
				throw new PositionException('function isAttacked: there are invalid values in board', 5);
			}
		}

		// validate turn
		if (($turn != 'w') && ($turn != 'b')) {
			throw new PositionException('function isAttacked: turn is invalid', 6);
		}

		foreach($board as $index=>$currentSquare) {
			if ($turn == 'w') {
				if (in_array($currentSquare, ['K', 'Q', 'R', 'B', 'N', 'P'])) {
					if (attacks($index, $square)) {
						return TRUE;
					}
				}
			} else {
				if (in_array($currentSquare, ['k', 'q', 'r', 'b', 'n', 'p'])) {
					if (attacks($index, $square)) {
						return TRUE;
					}
				}
			}
		}
		return FALSE;
	}
}
?>
