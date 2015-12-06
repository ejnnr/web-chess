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

	public function encodeMove(Move $move, $promotion)
	{
		$ret = dechex($this->encodePlainMove($move, $promotion));

		if (!empty($move->getNAGs())) {
			
			foreach ($move->getNAGs() as $nag) {
				$annotation = 0;
				$annotation |= (1 << 15);

				$annotation |= (1 << 8);
				$annotation |= $nag;
				$ret .= dechex($annotation);
			}
		}

		if (!empty($move->getComment())) {
			$ret .= dechex((1 << 7) + 4);

			foreach (str_split($move->getComment()) as $char) {
				if (ord($char) < 16) {
					$ret .= '0';
				}
				$ret .= dechex(ord($char));
			}

			$ret .= dechex((1 << 7) + 5);
		}

		return $ret;
	}
}
