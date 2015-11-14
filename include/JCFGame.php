<?php

require_once 'JCFGameNode.php';


class JCFGameException extends Exception {}

/**
 * A class representing a game in JCF
 *
 * @see Game
 */
class JCFGame extends Game implements JsonSerializable
{
	/**
	 * set current to a new GameNode
	 *
	 * This overwrites the parent method to use JCFGameNode instead of GameNode
	 *
	 * @param Move $move
	 * @return void
	 */
	protected function createCurrentNode(Move $move)
	{
		$this->current = new JCFGameNode($move);
	}

	/**
	 * returns a representation of the class that can be parsed by json_encode()
	 *
	 * This means you can just write `json_encode(new JCFGame())` and will get the game in JCF
	 *
	 * @return mixed[]
	 */
	public function jsonSerialize()
	{
		$ret = [];

		$ret['meta'] = $this->getHeaders();

		$ret['moves'] = [];

		foreach ($this->children as $child)
		{
			$move = $child->getMove();
			$moveArray = ['from' => $move->getDeparture(SQUARE_FORMAT_STRING),
			              'to'   => $move->getDestination(SQUARE_FORMAT_STRING)];

			if ($this->startingPosition->isPromotingMove($move)) {
				$moveArray['promotion'] = $move->getPromotion();
			}

			if (sizeof($move->getNAGs()) > 0)
			{
				$moveArray['NAGs'] = $move->getNAGs();
			}

			if ($child->hasChildren())
			{
				$moveArray['children'] = $this->generateMoveArray($child);
			}

			$ret['moves'][] = $moveArray;
 		}

		return $ret;
	}

	/**
	 * get the tree structure of root and its descendants as an array
	 *
	 * @param JCFGameNode $root
	 * @return array
	 */
	protected function generateMoveArray(JCFGameNode $root)
	{
		foreach ($root->getChildren() as $child)
		{
			$move = $child->getMove();
			$moveArray = ['from' => $move->getDeparture(SQUARE_FORMAT_STRING),
			              'to'   => $move->getDestination(SQUARE_FORMAT_STRING)];

			if ($root->positionAfter($this->startingPosition)->isPromotingMove($move)) {
				$moveArray['promotion'] = $move->getPromotion();
			}

			if (sizeof($move->getNAGs()) > 0)
			{
				$moveArray['NAGs'] = $move->getNAGs();
			}

			if ($child->hasChildren())
			{
				$moveArray['children'] = $this->generateMoveArray($child);
			}

			$ret[] = $moveArray;
 		}

		return $ret;
	}

	/**
	 * load a jcf string
	 *
	 * @param string $jcf String in JCF
	 * @return void
	 */
	public function loadJCF($jcf)
	{
		$arr = json_decode($jcf, true);

		if (is_null($arr))
		{
			throw new JCFGameException('invalid  JSON: json_last_error():' . json_last_error(), 151);
		}

		if (!isset($arr['moves'])) {
			throw new JCFGameException('invalid JCF: no move list found', 152);
		}

		if (isset($arr['meta'])) {
			$this->setHeaders($arr['meta']);
		}

		foreach ($arr['moves'] as $move) {
			if (!$this->validateMoveArray($move)) {
				throw new JCFGameException('invalid JCF: move has invalid syntax', 153);
			}

			$this->addMoveAndDescendants($move);
		}

		$this->goToEndOfMainline();
	}

	/**
	 * validate a JCF move array
	 *
	 * @param array $move A move with childen in JCF
	 * @return bool true if valid, fals if not
	 */
	protected function validateMoveArray($move)
	{
		if (!isset($move['from']) || !isset($move['to'])) {
			return false;
		}
		
		if (string2square($move['from']) === false || string2square($move['to']) === false) { // string2square returns false on invalid input
			return false;
		}

		if (isset($move['children'])) {
			foreach ($move['children'] as $child) {
				if (!$this->validateMoveArray($child)) {
					return false;
				}
			}
		}
		return true;
	}

	/**
	 * add a JCF move array with children to the game
	 *
	 * No validation is done inside this method. So if you use it, validate the move array with validateMoveArray() first!
	 * Otherwise the results are undefined.
	 *
	 * @param array $move
	 * @return void
	 */
	protected function addMoveAndDescendants($moveArray)
	{
		$this->doMove(new Move($moveArray['from'], $moveArray['to'], (isset($moveArray['promotion']) ? $moveArray['promotion'] : PROMOTION_QUEEN),	(isset($moveArray['NAGs']) ? $moveArray['NAGs'] : [])));

		if (isset($moveArray['children'])) {
			foreach ($moveArray['children'] as $child) {
				$this->addMoveAndDescendants($child);
			}
		}

		$this->back();
	}
}
