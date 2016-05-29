<?php
/**
 * Test file for Hercules Response object.
 *
 * @package tests/HRQLS/Models
 */
use HRQLS\Models\HerculesResponse;

/**
 * Defines Unit tests for the HerculesResponse class.
 */
class HerculesResponseTests
{
    /**
     * Verifies behaviour of HerculesResponse Constructor
     *
     * @return void
     */
    public function testConstructor()
    {
        $actual = new HerculesResponse('/endpoint');
        
        $this->assertInstanceOf('HRQLS\Models\Response', $actual);
        $this->assertEquals('/endpoint', $actual->getEndpoint());
        $this->assertEquals(200, $actual->getStatusCode());
        $this->assertEquals([], $actual->getData());
        $this->assertEquals([], $actual->getErrors);
    }
    
    /**
     * Verifies behaviour when adding a datapoint to the response.
     *
     * @return void
     */
    public function testAddDataEntry()
    {
        $actual = new HerculesResponse('/endpoint');
        $actual->addDataEntry(['a data point']);
        $actual->addDataEntry(['a key' => 'a keyed data point']);
        $actual->addDataEntry(['another data point']);
        
        $expected = [
            'a data point',
            'a key' => 'a keyed data point',
            'another data point',
        ];
        
        $this->assertEquals($expected, $actual->getData());
    }
    
    /**
     * Verifies behaviour when adding errors to the response.
     *
     * @return void
     */
    public function testAddErrors()
    {
        $actual = new HerculesResponse('/endpoint', 500, [], ['an existing error']);
        $actual->addError(['You done gone and fucked it up now.']);
        
        $expected = [
            'an existing error',
            'You done gone and fucked it up now.',
        ];
        
        $this->assertEquals($expected, $actual->getErrors());
    }
    
    /**
     * Verifies behviour when validating response fields converting response to json.
     *
     * @return void
     */
    public function testToJson()
    {
        $actual = new HerculesResponse('/enpoint');
        
        $expected = json_encode([
            '/endpoint',
            time()->format('Y-m-d H:i:s'),
            'data' => [],
            'errors' => [],
        ]);
        
        $this->assertEquals($expected, $actual->to_json());
    }
    
    /**
     * Verifies behaviour when validating a response fails while converting to json.
     *
     * @return void
     *
     * expectedException \InvalidResponseException
     */
    public function testToJson_missingData()
    {
        $actual = new HerculesResponse(null, null, null, null);
        $actual->to_json();
    }
}
