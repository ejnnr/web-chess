<?php namespace App\Chess;

class squareTest extends \PHPUnit_Framework_TestCase
{
    public function testConversionOfValidArrayToInteger()
    {
        $this->assertSame(0, array2square([0, 0]));
        $this->assertSame(20, array2square([4, 2]));
    }

    public function testConversionOfArrayToIntegerBadArgumentType()
    {
        $this->assertFalse(array2square(223));
    }

    public function testConversionOfArrayToIntegerBadArrayLength()
    {
        $this->assertFalse(array2square([]));
        $this->assertFalse(array2square([3, 4, 5]));
    }

    public function testConversionOfArrayToIntegerBadArrayContent()
    {
        $this->assertFalse(array2square(['e', 't']));
        $this->assertFalse(array2square(['3', '4']));
        $this->assertFalse(array2square([[], []]));
        $this->assertFalse(array2square([2, 9]));
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
        $this->assertFalse(string2square(true));
    }

    public function testConversionOfValidIntegerToArray()
    {
        $this->assertSame([0, 0], square2array(0));
        $this->assertSame([4, 2], square2array(20));
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
        $this->assertFalse(square2array('hello world'));
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
        $this->assertFalse(square2string('hello world'));
    }

    public function testValidationOfValidSquare()
    {
        $this->assertTrue(validateSquare(0));
        $this->assertTrue(validateSquare(63));
    }

    public function testValidationOfInvalidSquare()
    {
        $this->assertFalse(validateSquare(-1));
        $this->assertFalse(validateSquare(64));
        $this->assertFalse(validateSquare('no number'));
    }

    public function testGetRankOfValidSquare()
    {
        $this->assertSame(3, getRank(27));
        $this->assertSame(7, getRank(63));
        $this->assertSame(1, getRank(8));
    }

    public function testGetFileOfValidSquare()
    {
        $this->assertSame(0, getFile(0));
        $this->assertSame(7, getFile(47));
    }

    public function testGetFileAndRankOfInValidSquare()
    {
        $this->assertFalse(getRank(65));
        $this->assertFalse(getFile('text'));
    }
}
