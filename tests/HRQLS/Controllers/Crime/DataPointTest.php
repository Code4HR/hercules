<?php
/**
 * Test file for the Crime/DataPoint Object
 *
 * @package tests/HRQLS/Controllers
 */
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
        $data = new DataPoint('an offense', 'Felony', '1', new \DateTime(), 'Gotham', ['lat' => 0, 'lon' => 0]);
        
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
        new DataPoint('an offense', 'invalid', '1', new \DateTime(), 'Gotham', ['lat' => 0, 'lon' => 0]);
    }
    
    /**
     * Verifies behaviour of DataPoint getters.
     *
     * @depends testConstructor
     *
     *
     * @param DataPoint $data The DataPoint object to use in test.
     *
     * @return void
     */
    public function testGets(DataPoint $data)
    {
        $expectedDate = new \DateTime();
        $this->assertEquals('an offense', $data->getOffense());
        $this->assertEquals('FELONY', $data->getCategory());
        $this->assertEquals('1', $data->getClass());
        $this->assertEquals($expectedDate->format('Y-m-d H:i:s'), $data->getOccured());
        $this->assertEquals('Gotham', $data->getCity());
        $this->assertEquals(['lat' => 0, 'lon' => 0], $data->getLocation());
    }
    
    /**
     * Verifies behavious of toJson function.
     *
     * @depends testConstructor
     *
     * @param DataPoint $data The DataPoint object to use in test.
     *
     * @return void
     */
    public function testToJson(DataPoint $data)
    {
        $expectedDateTime = new \DateTime();
        
        $expected = json_encode([
            'offense' => 'an offense',
            'category' => 'FELONY',
            'class' => '1',
            'occured' => $expectedDateTime->format('Y-m-d H:i:s'),
            'city' => 'Gotham',
            'location' => [
                'lat' => 0,
                'lon' => 0,
            ],
        ]);
    }
}
