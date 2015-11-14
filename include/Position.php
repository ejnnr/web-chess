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
 *
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
 * 120: Trying to play illegal move
 * 130: Invalid SAN move syntax
 * 131: SAN move is ambiguous
 * 132: Illegal SAN move
 */


class PositionException extends Exception {}

/**
 * Represents a position that could occur in a game of chess
 */

class Position
{
	/**
 	 * An array of the letters of all white pieces
 	 */
	const WHITE_PIECES = ['K', 'Q', 'R', 'B', 'N', 'P'];

	/**
 	 * An array of the letters of all black pieces
 	 */
	const BLACK_PIECES = ['k', 'q', 'r', 'b', 'n', 'p'];

	/**
 	 * An array of the letters of all pieces
 	 */
	const ALL_PIECES = ['K', 'Q', 'R', 'B', 'N', 'P', 'k', 'q', 'r', 'b', 'n', 'p', ''];

	/**
 	 * the internal board representation
 	 *
 	 * This is an array of strings. Each element is either an empty string or the piece letter of the piece occupying that square.
 	 * As indices integers from 0 (a1) to 63 (h8) are used. See squares.php for more information.
 	 */
	private $board;
	
	/**
 	 * An array saving which castlings are allowed
 	 */
	private $castlings = ['K' => false,
	                      'Q' => false,
	                      'k' => false,
	                      'q' => false];
	
	/**
 	 * the side whose turn it is
 	 *
 	 * Either true for white or false for black
 	 */
	private $turn;

	/**
 	 * the number of half-moves since the last pawn move or capture
 	 */
	private $halfMoves;

	/**
 	 * th current move number
 	 */
	private $moveNumber;

	/**
 	 * the currently possible en passant square
 	 */
	private $enPassant;

	/**
	 * Constructor of Position
	 *
	 * @param mixed $position The position to load as a FEN
	 */

	function __construct($position = 'rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1')
	{
		$this->loadFEN($position);
	}

	/**
	 * Returns the FEN of the position
	 *
	 * @return string the FEN
	 */

	function getFEN()
	{
		$boardString = ''; // this is where the string will be put together
		$emptySquares = 0; // this is a counter that is used because empty square need to be grouped together, i.e. '3' instead of '111'

		// loop through a two-dimensional array returned by getArray()
		// array_reverse is necessary because FEN starts with the 8th rank
		foreach (array_reverse($this->getArray()) as $index=>$rank) {
			// Don't add a / at the beginning of the FEN
			if ($index != 0) {
				$boardString .= ($emptySquares != 0 ? (string)$emptySquares : '') . '/'; // add the remaining empty squares to the old rank and start a new one
				$emptySquares = 0;
			}
			foreach ($rank as $square) {
				if (empty($square)) { // empty square
					$emptySquares++;
				} else { // a piece is on this square
					$boardString .= ($emptySquares != 0 ? (string)$emptySquares : '') . $square; // add the remaining empty squares as a number
					$emptySquares = 0;
				}
			}
		}
		$boardString .= ($emptySquares != 0 ? (string)$emptySquares : ''); // add the remaining empty squares to the old rank

		$castlingString = ($this->castlings['K'] ? 'K' : '')
		                . ($this->castlings['Q'] ? 'Q' : '')
		                . ($this->castlings['k'] ? 'k' : '')
		   	            . ($this->castlings['q'] ? 'q' : '');
		if (empty($castlingString)) {
			$castlingString = '-';
		}

		return $boardString . ' ' . $this->turnColor() . ' ' . $castlingString . ' ' . (empty($this->enPassant) ? '-' : $this->enPassant) . ' ' . (string)$this->halfMoves . ' ' . (string)$this->moveNumber;
	}

	/**
	 * Returns the position as an array: [['R', 'N', 'B', ...], ['P', 'P', ...], ..., ['r', 'n', ...]]
	 *
	 * @return array The position as an 8x8 array
	 */

	function getArray()
	{
		// the return value
		$ret = array();

		// iterate through the board
		foreach ($this->board as $index=>$square) {
			if ($index % 8 == 0) { // start of a new rank
				$ret[] = array(); // add a new rank (array) to the return value
			}
			$ret[getRank($index)][] = $square;
		}

		return $ret;
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

		$matches = array(); // used to store the match result of the RegExp
		// the regular expression is structured as follows (spaces aren't listed here):
		//  #                            the delimiter
		//  ^                            beginning of string (to prevent that only part of the string macthes)
		//  ([1-8KQRBNPkqrbnp]{1,8}/)    one rank followed by a / ...
		//  {7}                          ... seven times
		//  [1-8KQRBNPkqrbnp]{1-8}       and the last one without a following /
		//  [wb]                         whose turn it is
		//  (K?Q?k?q?|-)                 the castling flags or -
		//  ([a-h][36]|\-)               the en passant file or -
		//  [0-9]+                       number of half-moves since last capture or pawn move
		//  [0-9]+                       move count
		//  $                            end of string (see ^)
		//  #                            the ending delimiter
		// '=== 0' (instead of a simple '!') must be used because preg_macth returns FALSE on failure and 0 if nothing's been found
		// $matches must be used because the RegExp would allow castling flags to be omitted

		$regExResult = preg_match('#^([1-8KQRBNPkqrbnp]{1,8}/){7}[1-8KQRBNPkqrbnp]{1,8} [wb] (K?Q?k?q?|-) ([a-h][36]|-) [0-9]+ [0-9]+$#', $fen, $matches);

		if ($regExResult === FALSE) { // an error occurred, this should never happen
			throw new PositionException('function loadFEN: error parsing fen with preg_match', 1);
		}

		if ($regExResult === 0) { // RegEx doesn't match
			throw new PositionException('function loadFEN: fen syntax is invalid', 110);
		}

		// check if castling flags are missing (the RegEx doesn't check this)
		if (empty($matches[2])) {
			throw new PositionException('function loadFEN: castling flags missing', 111);
		}

		// split fen
		$sections = explode(' ', $fen);

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

		$boardTemp = array();
		$i_rank = 0;
		$ranks = array_reverse($ranks); // FEN starts with 8th rank and ends with 1st rank so it has to be reversed
		foreach ($ranks as $rank) // go through all of the ranks (seperated by '/' in fen)
		{
			$i_file = 0;

			while (!empty($rank)) // since every square that was parsed is removed from $rank, this expression can be used to check if we are done
			{
				if (is_numeric(substr($rank, 0, 1))) // empty square(s)
				{
					$boardTemp[] = ''; // add an empty square to the board
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
					$boardTemp[] = substr($rank, 0, 1); // get the first character of the current rank (i.e. the current piece) and add it to board
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

		$turn = ($sections[1] == 'w');

		// set en passant square
		if ($sections[3] != '-')
		{
			if (getRank(string2square($sections[3])) != ($turn ? 5 : 2)) { // if it's white's turn, en passant square must be on sixth rank, otherwise on third
				throw new PositionException('function loadFEN: en passant square can\'t be ' . $sections[3] . ' because it\'s ' . ($turn ? 'white\'s' : 'black\'s') . ' turn', 106);
			}

			if ($boardTemp[string2square($sections[3]) + ($turn ? (-8) : 8)] != ($turn ? 'p' : 'P')) {
				throw new PositionException('function loadFEN: en passant square is invalid: no pawn that could be taken', 106);
			}
			$this->enPassant = $sections[3];
		}
		else
		{
			// no en passant square
			$this->enPassant = '';
		}

		$this->turn = ($sections[1] == 'w');

		// set possible castlings
		$this->castlings = array('K' => false, 'Q' => false, 'k' => false, 'q' => false);

		if (strpos($sections[2], 'K') !== false) // '!== false' is necessary. See http://php.net/manual/en/function.strpos.php for further information
		{
			$this->castlings['K'] = true;
		}

		if (strpos($sections[2], 'Q') !== false)
		{
			$this->castlings['Q'] = true;
		}

		if (strpos($sections[2], 'k') !== false)
		{
			$this->castlings['k'] = true;
		}

		if (strpos($sections[2], 'q') !== false)
		{
			$this->castlings['q'] = true;
		}

		// set number of half-moves since the last pawn move of capture
		$this->halfMoves = (int)$sections[4];

		// set move-number
		$this->moveNumber = (int)$sections[5];

		$this->board = $boardTemp;
	}

	/**
	 * Sets the position to the starting position
	 */

	function reset()
	{
		$this->loadFEN('rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1');
	}

	/**
	 * Returns true if the side to move is in check
	 *
	 * @return boolean true if in check, false if not
	 */

	function inCheck()
	{
		return $this->internalInCheck($this->turn, $this->board);
	}
	
	/**
	 * Returns true if the side given in $side is in check
	 *
	 * @param  boolean $side  The side to be checked (true for white, false for black)
	 * @param  array   $board The board used for checking
	 * @return boolean true if in check, false if not
	 */

	private function internalInCheck($side, $board)
	{
		foreach ($board as $index=>$currentSquare) {
			if ($currentSquare == ($side ? 'K' : 'k')) {	
				return $this->isAttacked($index, (!$side), $board); // call isAttacked; the second parameter is the attacking color which is the opposite of the color in check
			}
		}
		// should never happen
		throw new PositionException('function internalInCheck: A king is missing. This is probably a bug.', 1);
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
		if (!($move instanceof Move)) {
			throw new PositionException('function isLegal: move is no instance of class Move', 4);
		}

		$departure = $move->getDeparture();
		$destination = $move->getDestination();

		// if the piece on the departure square is not of the right color or if there is no piece at all, return false
		if ($this->turn) {
			if (!in_array($this->board[$departure],self::WHITE_PIECES)) {
				return FALSE;
			}
		} else {
			if (!in_array($this->board[$departure],self::BLACK_PIECES)) {
				return FALSE;
			}
		}

		switch ($this->board[$departure]) {
			case 'Q':
			case 'q':
			case 'R':
			case 'r':
			case 'B':
			case 'b':
			case 'N':
			case 'n':
				// return false if the piece doesn't even attack the destination square
				if (!$this->attacks($departure, $destination)) {
					return FALSE;
				}
				break;
			case 'P':
				// if none of the types of moves a pawn can make fits, return false
				if (!((($departure - $destination == -8) && ($this->board[$destination] == '')) // normal pawn move: one square forward
				   || (($departure - $destination == -16) && ($this->board[$destination] == '') && ($this->board[$destination - 8] == '') && (getRank($departure) == 1)) // double step if pawn is still on starting position
				   || ((abs(getFile($departure) - getFile($destination)) == 1) && (getRank($departure) - getRank($destination) == -1) && (in_array($this->board[$destination], ['q', 'r', 'b', 'n', 'p']))) // normal capture
				   || ((abs(getFile($departure) - getFile($destination)) == 1) && (getRank($departure) - getRank($destination) == -1) && ($destination == string2square($this->enPassant))))) { // en passant capture
					return FALSE;
				}
				break;
			case 'p':
				// if none of the types of moves a pawn can make fits, return false
				if (!((($departure - $destination == 8) && ($this->board[$destination] == '')) // normal pawn move: one square forward
				   || (($departure - $destination == 16) && ($this->board[$destination] == '') && ($this->board[$destination + 8] == '') && (getRank($departure) == 6)) // double step if pawn is still on starting position
				   || ((abs(getFile($departure) - getFile($destination)) == 1) && (getRank($departure) - getRank($destination) == 1) && (in_array($this->board[$destination], ['Q', 'R', 'B', 'N', 'P']))) // normal capture
				   || ((abs(getFile($departure) - getFile($destination)) == 1) && (getRank($departure) - getRank($destination) == 1) && ($destination == string2square($this->enPassant))))) { // en passant capture
					return FALSE;
				}
				break;
			case 'K':
				// normal king move
				if ($this->attacks($departure, $destination)) {
					break;
				}

				// from here on everything is castling

				// if departure isn't e1, we can return FALSE right away
				if ($departure != SQUARE_E1) {
					return FALSE;
				}

				// kingside castling
				if ($destination == SQUARE_G1) {
					// check if castling rights are present
					if (!$this->castlings['K']) {
						return FALSE;
					}
					// check if in check
					if ($this->isAttacked(SQUARE_E1, false)) {
						return FALSE;
					}
					// check if castling would be through check
					if ($this->isAttacked(SQUARE_F1, false)) {
						return FALSE;
					}
					// To ease things a little bit later on it is also checked right away if the king would be in check on destination.
					// That the rook changes its position during castling is irrelevant, since there's no possible position in which this plazs anz role.
					if ($this->isAttacked(SQUARE_G1, false)) {
						return FALSE;
					}
					// check if all squares are empty
					if (($this->board[SQUARE_F1] != '')
			         || ($this->board[SQUARE_G1] != '')
					 || ($this->board[SQUARE_H1] != 'R')) {
						return FALSE;
					}

				// queenside castling
				} elseif ($destination == SQUARE_C1) {
					if (!$this->castlings['Q']) {
						return FALSE;
					}
					if ($this->isAttacked(SQUARE_E1, false)) {
						return FALSE;
					}
					if ($this->isAttacked(SQUARE_D1, false)) {
						return FALSE;
					}
					// To ease things a little bit later on it is also checked right away if the king would be in check on destination.
					// That the rook changes its position during castling is irrelevant, since there's no possible position in which this plazs anz role.
					if ($this->isAttacked(SQUARE_C1, false)) {
						return FALSE;
					}
					if (($this->board[SQUARE_D1] != '')
			         || ($this->board[SQUARE_C1] != '')
					 || ($this->board[SQUARE_B1] != '')
					 || ($this->board[SQUARE_A1] != 'R')) {
						return FALSE;
					}

				// neither kingside nor queenside castling => return FALSE
				} else {
					return FALSE;
				}
				// checking if the move leaves the king in check can be skipped since this is already done
				return TRUE;
				break; // actually unnecessary but something might be changed and then this will become useful
			case 'k':
				// normal king move
				if ($this->attacks($departure, $destination)) {
					break;
				}

				// from here on everything is castling

				// if departure isn't e8, we can return FALSE right away
				if ($departure != SQUARE_E8) {
					return FALSE;
				}

				// kingside castling
				if ($destination == SQUARE_G8) {
					// check if castling rights are present
					if (!$this->castlings['k']) {
						return FALSE;
					}
					// check if in check
					if ($this->isAttacked(SQUARE_E8, true)) {
						return FALSE;
					}
					// check if castling would be through check
					if ($this->isAttacked(SQUARE_F8, true)) {
						return FALSE;
					}
					// To ease things a little bit later on it is also checked right away if the king would be in check on destination.
					// That the rook changes its position during castling is irrelevant, since there's no possible position in which this plazs anz role.
					if ($this->isAttacked(SQUARE_G8, true)) {
						return FALSE;
					}
					// check if all squares are empty
					if (($this->board[SQUARE_F8] != '')
			         || ($this->board[SQUARE_G8] != '')
					 || ($this->board[SQUARE_H8] != 'r')) {
						return FALSE;
					}

				// queenside castling
				} elseif ($destination == SQUARE_C8) {
					if (!$this->castlings['q']) {
						return FALSE;
					}
					if ($this->isAttacked(SQUARE_E8, true)) {
						return FALSE;
					}
					if ($this->isAttacked(SQUARE_D8, true)) {
						return FALSE;
					}
					// To ease things a little bit later on it is also checked right away if the king would be in check on destination.
					// That the rook changes its position during castling is irrelevant, since there's no possible position in which this plazs anz role.
					if ($this->isAttacked(SQUARE_C8, true)) {
						return FALSE;
					}
					if (($this->board[SQUARE_D8] != '')
			         || ($this->board[SQUARE_C8] != '')
					 || ($this->board[SQUARE_B8] != '')
					 || ($this->board[SQUARE_A8] != 'r')) {
						return FALSE;
					}

				// neither kingside nor queenside castling => return FALSE
				} else {
					return FALSE;
				}
				// checking if the move leaves the king in check can be skipped since this is already done
				return TRUE;
				break; // actually unnecessary but something might be changed and then this will become useful
		}

		// create a copy of board and do the move on that copy to see if it would leave the king in check
		$boardTemp = $this->board;
		$boardTemp[$departure] = '';
		$boardTemp[$destination] = $this->board[$departure];
		
		// check if the move would leave the king in check
		return !($this->internalInCheck($this->turn, $boardTemp));
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

		// King moves:
		// format: abs(file movement), abs(rank, movement)
		// 1,1 | 0,1 | 1,1
		// ---------------
		// 1,0 |  K  | 1,0
		// ---------------
		// 1,1 | 0,1 | 1,1
		// so for a valid king move the movement must be either 1,1 or 0,1 or 1,0

		return (((abs(getFile($departure) - getFile($destination)) == 1) && (abs(getRank($departure) - getRank($destination)) == 1))
		     || ((abs(getFile($departure) - getFile($destination)) == 1) && (abs(getRank($departure) - getRank($destination)) == 0))
		     || ((abs(getFile($departure) - getFile($destination)) == 0) && (abs(getRank($departure) - getRank($destination)) == 1)));
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

		// a queen can move like a rook and like a bishop, so just call these two functions
		return ($this->possibleRookMove($departure, $destination) || $this->possibleBishopMove($departure, $destination));
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

		// Rook moves:
		// 
		//     |     | 0,2 |     |
		// ---------------------------
		//     |     | 0,1 |     |
		// ---------------------------
		// 2,0 | 1,0 |  R  | 1,0 | 2,0
		// ---------------------------
		//     |     | 0,1 |     |
		// ---------------------------
		//     |     | 0,2 |     |
		//
		// so a valid rook move means that either the file movement or the rank movement is not equal to zero and the other one is.

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


		// bishop moves:
		// 
		// 2,2 |     |     |     | 2,2
		// ---------------------------
		//     | 1,1 |     | 1,1 |
		// ---------------------------
		//     |     |  B  |     |
		// ---------------------------
		//     | 1,1 |     | 1,1 |
		// ---------------------------
		// 2,2 |     |     |     | 2,2
		//
		// so for a valid bishop moves, abs() of both movement directions must be equal and they must not be zero
		
		return ((abs(getFile($departure) - getFile($destination)) == abs(getRank($departure) - getRank($destination))) && ((getFile($departure) - getFile($destination)) != 0));
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

		// knight moves:
		// 
		//     | 1,2 |     | 1,2 |
		// ---------------------------
		// 2,1 |     |     |     | 2,1
		// ---------------------------
		//     |     |  N  |     |
		// ---------------------------
		// 2,1 |     |     |     | 2,1
		// ---------------------------
		//     | 1,2 |     | 1,2 |
		//
		// so for a valid knight move one movement direction must be 2, the other one 1

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

		// check if both squares are valid
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
			if (!in_array($square, self::ALL_PIECES)) {
				throw new PositionException('function attacks: there are invalid values in board', 5);
			}
		}

		// depending on which type of piece is on departure different things need to be checked
		switch ($board[$departure]) {
			case '':
				throw new PositionException('function attacks: departure square doesn\'t contain a piece', 5);
			case 'K':
			case 'k':
				return $this->possibleKingMove($departure, $destination);
			case 'N':
			case 'n':
				return $this->possibleKnightMove($departure, $destination);
			case 'R':
			case 'r':
				if (!$this->possibleRookMove($departure, $destination)) {
					return FALSE;
				}

				// the rest of this function basically checks if there is any piece between departure and destination because rooks cannot jump

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
				// go one square at a time in the direction of the move until hitting another piece or reaching destination
				while (($board[array2square([$file, $rank])] == '') && array2square([$file, $rank]) != $destination) {
					$file += $fileMovement;
					$rank += $rankMovement;
				}

				// if we've reached destination, the move is possible, otherwise there is something in the way
				return (array2square([$file, $rank]) == $destination);
			case 'B':
			case 'b':
				if (!$this->possibleBishopMove($departure, $destination)) {
					return FALSE;
				}

				// the rest of this function basically checks if there is any piece between departure and destination because bishops cannot jump

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
				while (($board[array2square([$file, $rank])] == '') && array2square([$file, $rank]) != $destination) {
					$file += $fileMovement;
					$rank += $rankMovement;
				}

				return (array2square([$file, $rank]) == $destination);
			case 'Q':
			case 'q':
				if (!$this->possibleQueenMove($departure, $destination)) {
					return FALSE;
				}

				// the rest of this function basically checks if there is any piece between departure and destination because queens cannot jump

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
				while (($board[array2square([$file, $rank])] == '') && array2square([$file, $rank]) != $destination) {
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
	 * Checks if a certain square is attacked by at least one piece of a certain color
	 *
	 * This function uses attacks() for every piece of the color specified with $turn.
	 * Be careful: even if the square is occupied by a piece of the attacking color, this function may still return true.
	 *
	 * @param integer $square The square to check
	 * @param string  $turn   The color to attack the square (true for white, false for black); if left out, this defaults to $this->turn
	 * @param array   $board  The board to use; defaults to $this->board
	 * @return boolean true if it is attacked, otherwise false
	 */
	private function isAttacked($square, $turn = null, $board = null)
	{
		if (is_null($turn)) {
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
		foreach ($board as $currentSquare) {
			// check if the square is a string
			if (!is_string($currentSquare)) {
				throw new PositionException('function isAttacked: there are non-string values in board', 4);
			}
			// check if the square has a valid value
			if (!in_array($currentSquare, self::ALL_PIECES)) {
				throw new PositionException('function isAttacked: there are invalid values in board', 5);
			}
		}

		// validate turn
		if (!is_bool($turn)) {
			throw new PositionException('function isAttacked: turn is invalid', 6);
		}

		foreach($board as $index=>$currentSquare) {
			if ($turn == 'w') { // white is the attacking color
				if (in_array($currentSquare, self::WHITE_PIECES)) {
					if ($this->attacks($index, $square, $board)) {
						return TRUE;
					}
				}
			} else { // black is the attacking color
				if (in_array($currentSquare, self::BLACK_PIECES)) {
					if ($this->attacks($index, $square, $board)) {
						return TRUE;
					}
				}
			}
		}
		return FALSE;
	}

	/**
 	 * Plays the move and updates the position
 	 *
 	 * @param Move $move The move to be played
 	 */

	public function doMove($move) {
		if (!($move instanceof Move)) {
			throw new PositionException('function doMove: move is no instance of class Move', 4);
		}

		if (!$this->isLegalMove($move)) {
			throw new PositionException('function doMove: move is illegal', 120);
		}

		$departure = $move->getDeparture();
		$destination = $move->getDestination();
		$this->halfMoves++;
		$preserveEnPassant = FALSE;
		switch ($this->board[$departure]) {
			case 'Q':
			case 'q':
			case 'B':
			case 'b':
			case 'N':
			case 'n':
				if ($this->board[$destination] != '') {
					// the move is a capturing move, therefore the 50-moves-counter must be reset
					$this->halfMoves = 0;
				}

				// update the board
				$this->board[$destination] = $this->board[$departure];
				$this->board[$departure] = '';
				break;

			case 'R':
				if ($this->board[$destination] != '') {
					// the move is a capturing move, therefore the 50-moves-counter must be reset
					$this->halfMoves = 0;
				}

				// update the board
				$this->board[$destination] = $this->board[$departure];
				$this->board[$departure] = '';

				// update castling rights
				if ($departure == SQUARE_H1) {
					$this->castlings['K'] = false;
				}
				if ($departure == SQUARE_A1) {
					$this->castlings['Q'] = false;
				}
				break;

			case 'r':
				if ($this->board[$destination] != '') {
					// the move is a capturing move, therefore the 50-moves-counter must be reset
					$this->halfMoves = 0;
				}
				$this->board[$destination] = $this->board[$departure];
				$this->board[$departure] = '';

				// update castling rights
				if ($departure == SQUARE_H8) {
					$this->castlings['k'] = false;
				}
				if ($departure == SQUARE_A8) {
					$this->castlings['q'] = false;
				}
				break;

			case 'P':
				// since this is a pawn move, the 50-moves-counter must be reset
				$this->halfMoves = 0;

				if (abs(getRank($departure) - getRank ($destination)) == 2) { // double step
					$this->enPassant = substr(square2string($destination), 0, 1) . '3'; // add en passant square
					$preserveEnPassant = TRUE; // make sure en passant square doesn't get overwritten
				}
				if ($destination === string2square($this->enPassant)) { // en passant capture
					$this->board[$destination - 8] = '';
				}
				$this->board[$destination] = $this->board[$departure];
				$this->board[$departure] = '';
				if (getRank($destination) == 7) { // promotion
					switch ($move->getPromotion()) {
					case PROMOTION_QUEEN:
						$this->board[$destination] = 'Q';
						break;
					case PROMOTION_ROOK:
						$this->board[$destination] = 'R';
						break;
					case PROMOTION_BISHOP:
						$this->board[$destination] = 'B';
						break;
					case PROMOTION_KNIGHT:
						$this->board[$destination] = 'N';
						break;
 					}
				}
				break;
			case 'p':
				$this->halfMoves = 0;
				if (abs(getRank($departure) - getRank ($destination)) == 2) { // double step
					$this->enPassant = substr(square2string($destination), 0, 1) . '6'; // add en passant square
					$preserveEnPassant = TRUE; // make sure en passant square doesn't get overwritten
				}
				if ($destination === string2square($this->enPassant)) { // en passant capture
					$this->board[$destination + 8] = '';
				}
				$this->board[$destination] = $this->board[$departure];
				$this->board[$departure] = '';
				if (getRank($destination) == 0) { // promotion
					switch ($move->getPromotion()) {
					case PROMOTION_QUEEN:
						$this->board[$destination] = 'q';
						break;
					case PROMOTION_ROOK:
						$this->board[$destination] = 'r';
						break;
					case PROMOTION_BISHOP:
						$this->board[$destination] = 'b';
						break;
					case PROMOTION_KNIGHT:
						$this->board[$destination] = 'n';
						break;
 					}
				}
				break;
			case 'K':
				$this->castlings['K'] = FALSE;
				$this->castlings['Q'] = FALSE;
				if ($this->possibleKingMove($departure, $destination)) { // regular king move
					if ($this->board[$destination] != '') {
						// the move is a capturing move, therefore the 50-moves-counter must be reset
						$this->halfMoves = 0;
					}
					$this->board[$destination] = $this->board[$departure];
					$this->board[$departure] = '';
					break;
				}
				// from here on everything is castling

				if ($destination == SQUARE_G1) { // kingside castling
					$this->board[$destination] = $this->board[$departure];
					$this->board[$departure] = '';
					$this->board[SQUARE_F1] = 'R';
					$this->board[SQUARE_H1] = '';
				} elseif ($destination == SQUARE_C1) { // queenside castling
					$this->board[$destination] = $this->board[$departure];
					$this->board[$departure] = '';
					$this->board[SQUARE_D1] = 'R';
					$this->board[SQUARE_A1] = '';
				} else {
					throw new PositionException('function doMove: Wrong king position. This is probably a bug.' . $destination, 1);
				}
				break;
			case 'k':
				$this->castlings['k'] = FALSE;
				$this->castlings['q'] = FALSE;
				if ($this->possibleKingMove($departure, $destination)) { // regular king move
					if ($this->board[$destination] != '') {
						// the move is a capturing move, therefore the 50-moves-counter must be reset
						$this->halfMoves = 0;
					}
					$this->board[$destination] = $this->board[$departure];
					$this->board[$departure] = '';
					break;
				}
				// from here on everything is castling

				if ($destination == SQUARE_G8) { // kingside castling
					$this->board[$destination] = $this->board[$departure];
					$this->board[$departure] = '';
					$this->board[SQUARE_F8] = 'r';
					$this->board[SQUARE_H8] = '';
				} elseif ($destination == SQUARE_C8) { // queenside castling
					$this->board[$destination] = $this->board[$departure];
					$this->board[$departure] = '';
					$this->board[SQUARE_D8] = 'r';
					$this->board[SQUARE_A8] = '';
				} else {
					throw new PositionException('function doMove: Wrong king position. This is probably a bug.' . $destination, 1);
				}
				break;
		}

		if (!$preserveEnPassant) {
			$this->enPassant = '';
		}

		if (!$this->turn) {
			$this->moveNumber++;
		}

		$this->turn = (!$this->turn);

		return $this;
	}

	/**
 	 * parses a move in SAN and returns a Move object
 	 *
 	 * The function allows the following syntax:
 	 *  * The actual move. This can be either:
 	 *    * a normal move:
 	 *      * the piece letter if it's not a pawn
 	 *      * the necessary information for disambiguation
 	 *      * 'x' for captures if necessary (can be left out)
 	 *      * the destination square (a letter from a to h followed by a number from 1 to 8)
 	 *      * '=' and a piece letter for promotion (if it's a promoting move)
 	 *    * O-O
 	 *    * O-O-O
 	 *  * a '+' for check or a '#' for mate
 	 *  * a space (optional)
 	 *  * one of the following (optional):
 	 *    * !
 	 *    * ?
 	 *    * !!
 	 *    * ??
 	 *    * !?
 	 *    * ?!
 	 *  * the following (optional, multiple times):
 	 *    * a space (optional)
 	 *    * $
 	 *    * a number from 0 to 255
 	 *
 	 * @param string $move The move to be parsed in SAN
 	 */

	function parseSAN($move)
	{
		if (empty($move)) {
			throw new PositionException('function parseSAN: move is empty', 2);
		}

		// this function is mainly based around the following regular expression.
		// All the seperate fields of a SAN move, i.e. diambiguation, destination, promotion etc. are later accessed via $matches

		$matches = array();
		if (!preg_match('/^(?<move>(?<piece>[KQRBN]?)(?<disambiguationFile>[a-h]?)(?<disambiguationRank>[1-8]?)(?<capture>[x]?)(?<destination>[a-h][1-8])(=(?<promotion>[QRBN]))?|(?<kingsideCastling>O-O)|(?<queensideCastling>O-O-O))(?<check>\+|\#)? ?(?<annotationMove>\?|!|\?!|!\?|\?\?|!!)? ?(?<NAGs>( ?\$[0-9]+ ?)*)$/', $move, $matches)) {
			throw new PositionException('function parseSAN: invalid move syntax', 130);
		}

		if (!empty($matches['kingsideCastling'])) { // the moves is kingside castling
			$departure = ($this->turn ? SQUARE_E1 : SQUARE_E8); // set departure to e1 or e8
			$destination = ($this->turn ? SQUARE_G1 : SQUARE_G8); // set destination to g1 or g8
			if ($this->board[$departure] != ($this->turn ? 'K' : 'k')) {
				throw new PositionException('function parseSAN: castling is not possible', 132);
			}
			if (!$this->isLegalMove(new Move($departure, $destination))) {
				throw new PositionException('function parseSAN: castling is no legal move', 132);
			}
			$promotion = PROMOTION_QUEEN;
			goto castling;
		}

		if (empty($matches['piece'])) { // piece is a pawn
			$piece = 'P';
		} else {
			$piece = $matches['piece'];
		}
		if (!empty($matches['promotion'])) {
			$promotion = substr($matches['promotion'], 1);
		} else {
			$promotion = PROMOTION_QUEEN;
		}
		$destination = string2square($matches['destination']);

		$foundLegalMove = FALSE;
		foreach ($this->board as $index=>$square) {
			if ($square != ($this->turn ? $piece : strtolower($piece))) { // check if the current sqaure has the right piece
				continue;
			}

			if (!empty($matches['disambiguationFile']) && $matches['disambiguationFile'] != substr(square2string($index), 0, 1)) {
				continue;
			}

			if (!empty($matches['disambiguationRank']) && ($matches['disambiguationRank'] - 1) != getRank($index)) {
				continue;
			}

			if ($this->isLegalMove(new Move($index, $destination, $promotion))) {
				if ($foundLegalMove) {
					throw new PositionException('function parseSAN: move is ambiguous', 131);
				}
				$foundLegalMove = TRUE;
				$departure = $index;
			}
		}

		if (!isset($departure)) {
			throw new PositionException('function parseSAN: no legal move found', 132);
		}

		castling:

		// replace !, ?, !!, ??, ?! and !? with NAGs
		$annotationMove = preg_replace(array('/^!$/', '/^\?$/', '/^!!$/', '/^\?\?$/', '/^!\?$/', '/^\?!$/'), array('\$1', '\$2', '\$3', '\$4', '\$5', '\$6'), $matches['annotationMove']);
		// add the NAGs just created to the NAGs in the move
		$NAGs = $matches['NAGs'] . $annotationMove;
		// strip whitespaces
		$NAGs = str_replace(' ', '', $NAGs);
		$NAGs = explode('$', $NAGs);
		unset($NAGs[0]); // delete the first element, because it is empty (the string used with explode started with $)
		foreach ($NAGs as $index=>$NAG) {
			$NAGs[$index] = (int)$NAG;
		}

		return new Move($departure, $destination, $promotion, $NAGs);
	}

	/**
 	 * returns the color to move as 'w' or 'b'
 	 * 
 	 * @return string The current turn as 'w' or 'b'
 	 */

	private function turnColor()
	{
		return ($this->turn ? 'w' : 'b');
	}

	public function isPromotingMove(Move $move)
	{
		if (!$this->isLegalMove($move)) {
			throw new PositionException('move is illegal', 121);
		}

		return ($this->board[$move->getDeparture()] == 'p' && $move->getDestination() < 8) || ($this->board[$move->getDeparture()] == 'P' && $move->getDestination() > 55);
	}
}
