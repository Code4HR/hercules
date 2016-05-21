<?php
/**
 * Test file for GuzzleHttp\Client Service Provider.
 *
 * @package tests/HRQLS/Models
 */
use Silex\Application;
use HRQLS\Models\GuzzleServiceProvider;
use GuzzleHttp\Client;

/**
 * Defines GuzzleHttp Client Service Provider Unit Tests
 */
class GuzzleServiceProviderTest extends PHPUnit_Framework_TestCase
{
    /**
     * Gets necessary Mock Objects for unit tests
     *
     * @return array like [
     *    'guzzle' => GuzzleHttp\Client,
     * ];
     */
    public function getMocks()
    {
        $guzzleMock = $this->getMockBuilder('GuzzleHttp\Client')
            ->disableOriginalConstructor()
            ->setMethods(['request'])
            ->getMock();
            
        $responseMock = $this->getMockBuilder('GuzzleHttp\Psr7\Response')
            ->disableOriginalConstructor()
            ->setMethods(['getStatusCode', 'getBody'])
            ->getMock();
        
        return [
            'guzzle' => $guzzleMock,
            'response' => $responseMock
        ];
    }
     
    /**
     * Verifies the behavior of the GuzzleProvider constructor
     *
     * @return void
     */
    public function testConstructor()
    {
        $mocks = $this->getMocks();
        $guzzleProvider = new GuzzleServiceProvider($mocks['guzzle']);
         
        $this->assertInstanceOf('HRQLS\Models\GuzzleServiceProvider', $guzzleProvider);
    }
    
    /**
     * Verifies behaviour of get requests by Guzzle Service Provider.
     *
     * @return void
     */
    public function testGet()
    {
        $expectedBody = [
            'key1' => 'value1',
            'key2' => 'value2',
        ];
        
        $mocks = $this->getMocks();
        $guzzleMock = $mocks['guzzle'];
        $responseMock = $mocks['response'];
        
        $responseMock->method('getStatusCode')->willReturn(200);
        $responseMock->method('getBody')->willReturn($expectedBody);
        
        $guzzleMock->method('request')->willReturn($responseMock);
        
        $guzzleServiceProvider = new GuzzleServiceProvider($guzzleMock);
        
        $actual = $guzzleServiceProvider->get('http://localhost', []);
        
        $this->assertEquals($expectedBody, $actual->getBody());
        $this->assertEquals(200, $actual->getStatusCode());
    }
}
