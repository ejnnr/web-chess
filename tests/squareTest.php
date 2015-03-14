<?php
require '../include/square.php';

class squareTest extends PHPUnit_Framework_TestCase
{
	public function testConversionOfStringToInteger()
	{
		$this->assertEquals(0, string2square('a1'));
		$this->assertEquals(63, string2square('h8'));
		$this->assertEquals(FALSE, string2square('f9'));
		$this->assertEquals(FALSE, string2square('i5'));
		$this->assertEquals(FALSE, string2square('hello world'));
	}

	public function testConversionOfIntegerToString()
	{
		$this->assertEquals('a1', square2string(0));
		$this->assertEquals('h8', square2string(63));
		$this->assertEquals(FALSE, square2string(1000));
		$this->assertEquals(FALSE, square2string(-1));
		$this->assertEquals(FALSE, square2string(0.5));
		$this->assertEquals(FALSE, square2string('5'));
	}
}
?>
