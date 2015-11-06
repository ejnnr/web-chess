<?php

require_once 'JCFGameNode.php';

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
}
