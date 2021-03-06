<?php
/**
 * Test file for the Crime/DataPoint Object
 *
 * @package tests/HRQLS/Controllers
 */
use Silex\Application;
use HRQLS\Controllers\Crime\DataPoint;

/**
 * Defines Crime/DataPoint object unit tests.
 */
class DataPointTest extends PHPUnit_Framework_TestCase
{
    /**
     * Verifies the DataPoint object is constructed properly.
     *
     * @return DataPoint The datapoint that was constructed in this test.
     */
    public function testConstructor()
    {
        $data = new DataPoint(
            'uniqueId',
            'an offense',
            new \DateTime(),
            'Gotham',
            [
                'lat' => 0,
                'lon' => 0
            ],
            'Felony',
            '1'
        );
        
        $this->assertInstanceOf('HRQLS\Controllers\Crime\DataPoint', $data);
        
        return $data;
    }
    
    /**
     * Verifies behaviour when an invalid Crime category is entered.
     *
     * @return void
     *
     * @expectedException InvalidArgumentException
     */
    public function testConstructor_invalidCategory()
    {
        new DataPoint('uniqueid', 'an offense', new \DateTime(), 'Gotham', ['lat' => 0, 'lon' => 0], 'invalid', '1');
    }
    
    /**
     * Verfies behaviour when class is not valid for the specified category.
     *
     * @return void
     *
     * @expectedException InvalidArgumentException
     */
    public function testConstructor_invalidClass()
    {
        new DataPoint('uniqueid', 'an offense', new \DateTime(), 'Gotham', ['lat' => 0, 'lon' => 0], 'FELONY', '1337');
    }
    
    /**
     * Verifies behaviour when class and category are not provided.
     *
     * @return void
     */
    public function testConstructor_noCategory()
    {
        $actual = new DataPoint('uniqueid', 'ASSAULT', new \DateTime(), 'Gotham', ['lat' => 0, 'lon' => 0], '', '');
        
        $this->assertEquals('MISDEMEANOR', $actual->getCategory());
        $this->assertEquals('1', $actual->getClass());
    }
    
    /**
     * Verifies behaviour of DataPoint getters.
     *
     * @return void
     */
    public function testGets()
    {
        $date = new \DateTime();
        
        $actual = new DataPoint('uniqueId', 'an offense', $date, 'Gotham', ['lat' => 0, 'lon' => 0], 'FELONY', '1');
        
        $this->assertEquals('uniqueId', $actual->getId());
        $this->assertEquals('an offense', $actual->getOffense());
        $this->assertEquals('FELONY', $actual->getCategory());
        $this->assertEquals('1', $actual->getClass());
        $this->assertEquals($date->format('Y-m-d H:i:s'), $actual->getOccured());
        $this->assertEquals('Gotham', $actual->getCity());
        $this->assertEquals(['lat' => 0, 'lon' => 0], $actual->getLocation());
    }
    
    /**
     * Verifies behavious of toArray function.
     *
     * @return void
     */
    public function testToArray()
    {
        $date = new \DateTime();
        
        $actual = new DataPoint('uniqueId', 'an offense', $date, 'Gotham', ['lat' => 0, 'lon' => 0], 'FELONY', '1');
        
        $expected = [
            'id' => 'uniqueId',
            'offense' => 'an offense',
            'category' => 'FELONY',
            'class' => '1',
            'occurred' => $date->format('Y-m-d H:i:s'),
            'city' => 'Gotham',
            'location' => [
                'lat' => 0,
                'lon' => 0,
            ],
        ];
        
        $this->assertEquals($expected, $actual->toArray());
    }
}
