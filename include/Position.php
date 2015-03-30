<?php

/**
 * This file contains the class Position and the corresponding exception class.
 */

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
 */

class PositionException extends Exception {}

class Position
{
	private $fen;

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

		$regExResult = preg_match('#^([1-8KQRBNPkqrbnp]{1,8}/){7}[1-8KQRBNPkqrbnp]{1,8} [wb] (?P<castling>K?Q?k?q?)|(?P<noCastling>\-) [a-h]|\- [0-9]+ [0-9]+$#', $fen, $matches);

		if ($regExResult === FALSE) { // an error occurred, this should never happen
			throw new PositionException('function loadFEN: error parsing fen with preg_match', 1);
		}

		if ($regExResult === 0) { // RegEx doesn't match
			throw new PositionException('function loadFEN: fen syntax is invalid', 110);
		}

		// check if castling flags are missing (the RegEx doesn't do this well)
		if (empty($matches['castling']) && empty($matches['noCastling'])) {
			throw new PositionException('function loadFEN: castling flags missing', 110);
		}

		// split fen
		$sections = explode(" ", $fen);

		// validate fen
		// this is actually unnecessary because it was already checked with the RegEx but as long as this is still in testing it might be useful in case the RegEx doesn't work
		// TODO: remove this!
		if (!(count($sections) == 6)) {
			throw new PositionException('function loadFEN: Invalid FEN syntax', 110);
		}

		

		// check if all kings are on the bord
		if (strpos($sections[0], "K") === FALSE || strpos($sections[0], "k") === FALSE)
		{
			throw new ChessGameException('Function loadFen: there must be a black and a white king on the board.', 102);
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
					$pieceCountOf[substr($rank, 0, 1)]++;
					$this->board[] = substr($rank, 0, 1); // get the first character of the current rank (i.e. the current piece) and add it to board
					$rank = substr($rank, 1); // strip the piece just added from $rank
				}

				$i_file++;
			}

			if ($i_file != 8) {
				throw new PositionException('function loadFEN: each rank must have 8 files', 110);
			}

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
			if (((string2square($sections[3]) & 56) / 8) != ($this->turn == 'w' ? 2 : 5)) {
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


	}

	/**
	 * Sets the position to the starting position
	 *
	 * @return true on success, false on failure
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
}

?>
