<?php

require_once 'include/JCFGame.php';

class JCFGameTest extends PHPUnit_Framework_TestCase
{
	public function testJsonSerialize()
	{
		$jcf = new JCFGame();
		$jcf->doMove(new Move('e2', 'e4'));
		$jcf->doMove(new Move('e7', 'e5'));
		$jcf->addVariation(new Move('c7', 'c5'));
		$jcf->doMove(new Move('g1', 'f3'));
		$jcf->doMove(new Move('d7', 'd6'));
		$jcf->addVariation(new Move('b8', 'c6'));
		$jcf->endVariation();
		$jcf->doMove(new Move('d2', 'd4'));
		$jcf->endVariation();
		$jcf->addVariation(new Move('e7', 'e6', PROMOTION_QUEEN, [1]));
		$jcf->endVariation();

		$jcf->setHeader('site', 'Berlin');

	 	$this->assertJsonStringEqualsJsonString(
			'{
    			"meta": { "site": "Berlin" },
    			"moves": [
        			{
            			"from": "e2",
            			"to": "e4",
            			"children": [
                			{
                    			"from": "e7",
                    			"to": "e5"
                			},
                			{
                    			"from": "c7",
                    			"to": "c5",
                    			"children": [
                        			{
                            			"from": "g1",
                            			"to": "f3",
                            			"children": [
                                			{
                                    			"from": "d7",
                                    			"to": "d6",
                                    			"children": [
                                        			{
                                            			"from": "d2",
                                            			"to": "d4"
                                        			}
                                    			]
                                			},
                                			{
                                    			"from": "b8",
                                    			"to": "c6"
                                			}
                            			]
                        			}
                    			]
                			},
                			{
                    			"from": "e7",
                    			"to": "e6",
                    			"NAGs": [
                        			1
                    			]
                			}
            			]
        			}
    			]
			}', json_encode($jcf));
	}
}
