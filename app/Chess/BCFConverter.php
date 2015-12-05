<?php namespace App\Chess;

class BCFConverterException extends \Exception {}

class BCFConverter
{
	public function encodeJCF($jcf)
	{

	}

	public function decodeToJCF($bcf)
	{

	}

	public function decodeToGame($bcf)
	{

	}

	public function encodeGame(Game $game)
	{

	}

	protected function encodePlainMove(Move $move, $promotion)
	{
		$ret = 0;

		$ret |= ((int)$promotion << 14); // shift the promotion flag to position 15 and add it to the return value

		if ($promotion) {
			$ret |= (($move->getPromotion() - 1) << 6);
		}

		$ret |= ($move->getDeparture(SQUARE_FORMAT_INT) << 8);
		$ret |= $move->getDestination(SQUARE_FORMAT_INT);

		return $ret;
	}

	protected function encodeMove(Move $move, $promotion)
	{
		$ret = $this->encodePlainMove($move, $promotion);

		if (!empty($move->getNAGs())) {
			$ret = $ret << 16;
			$ret |= (1 << 15);

			//TODO: actually add type of annotation (NAG) and value
		}

		return $ret;
	}
}
