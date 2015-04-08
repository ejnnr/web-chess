<?php
require_once 'include/square.php';

class squareTest extends PHPUnit_Framework_TestCase
{
	public function testConversionOfValidArrayToInteger()
	{
		$this->assertSame(0, array2square(array(0, 0)));
		$this->assertSame(20, array2square(array(4, 2)));
	}

	public function testConversionOfArrayToIntegerBadArrayLength()
	{
		$this->assertFalse(array2square(array()));
		$this->assertFalse(array2square(array(3, 4, 5)));
	}

	public function testConversionOfArrayToIntegerBadArrayContent()
	{
		$this->assertFalse(array2square(array('e', 't')));
		$this->assertFalse(array2square(array('3', '4')));
		$this->assertFalse(array2square(array(array(), array())));
	}

	public function testConversionOfValidStringToInteger()
	{
		$this->assertSame(0, string2square('a1'));
		$this->assertSame(63, string2square('h8'));
	}

	public function testConversionOfEmptyStringToInteger()
	{
		$this->assertFalse(string2square(''));
	}

	public function testConversionOfInvalidStringToInteger()
	{
		$this->assertFalse(string2square('f9'));
		$this->assertFalse(string2square('i5'));
		$this->assertFalse(string2square('hello world'));
	}

	public function testConversionOfNonStringToInteger()
	{
		$this->assertFalse(string2square(34));
		$this->assertFalse(string2square(TRUE));
	}

	public function testConversionOfValidIntegerToArray()
	{
		$this->assertSame(array(0, 0), square2array(0));
		$this->assertSame(array(4, 2), square2array(20));
	}

	public function testConversionOfOutOfRangeIntegerToArray()
	{
		$this->assertFalse(square2array(1000));
		$this->assertFalse(square2array(-1));
	}

	public function testConversionOfNonIntegerToArray()
	{
		$this->assertFalse(square2array(0.5));
		$this->assertFalse(square2array('5'));
		$this->assertFalse(square2array("hello world"));
	}

	public function testConversionOfValidIntegerToString()
	{
		$this->assertSame('a1', square2string(0));
		$this->assertSame('h8', square2string(63));
	}

	public function testConversionOfOutOfRangeIntegerToString()
	{
		$this->assertFalse(square2string(1000));
		$this->assertFalse(square2string(-1));
	}

	public function testConversionOfNonIntegerToString()
	{
		$this->assertFalse(square2string(0.5));
		$this->assertFalse(square2string('5'));
		$this->assertFalse(square2string("hello world"));
	}
}
?>
