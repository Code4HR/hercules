<?php
/**
 * Test file for Google Geocoding Service Provider.
 *
 * @package tests/HRQLS/Models
 */
use Silex\Application;
use HRQLS\Models\GuzzleServiceProvider;

/**
 * Defines Geocoding Service Provider unit tests.
 */
class GeocodingServiceProviderTests extends PHPUnit_Framework_TestCase
{
    /**
     * Constructs the base mock objects reuired to test the Geocoding Service provider.
     *
     * @return array like [
     *     'silex' => Silex Mock Object.
     *     'guzzle' => Mock Guzzle Service Provider Object.
     *     'response' => Mock Guzzle Response Object.
     *     'geocoder' => Mock Geocoding Service Provider. 
     * ];
     */
    private function getMocks()
    {
        $appMock = $this->getMockBuilder('Silex\Application')
            ->disableOriginalConstructor()
            ->setMethods(['register'])
            ->getMock();
            
        $guzzleMock = $this->getMockBuilder('GuzzleHttp\Client')
            ->disableOriginalConstructor()
            ->setMethods(['request'])
            ->getMock();
            
        $responseMock = $this->getMockBuilder('GuzzleHttp\Psr7\Response')
            ->disableOriginalConstructor()
            ->setMethods(['getStatusCode', 'getBody'])
            ->getMock();
            
        $geocoderMock = $this->getMockBuilder()
            ->disableOriginalConstructor()
            ->setMethods(['geocode'])
            ->getMocks();
            
        return [
            'silex' => $appMock,
            'guzzle' => $guzzleMock,
            'response' => $responseMock,
            'geocoder' => $geocoderMock,
        ];
    }
    
    public function testGecode()
    {
        $mocks = getMocks();
        $appMock = $mocks['silex'];
        $guzzleMock = $mocks['guzzle'];
        $responseMock = $mocks['response'];
        $geocodingServiceProviderMock = $mocks['geocoder'];
    }
}
