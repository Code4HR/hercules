<?php
/**
 * Test file for Google Geocoding Service Provider.
 *
 * @package tests/HRQLS/Models
 */
use Silex\Application;
use HRQLS\Models\GuzzleServiceProvider;
use HRQLS\Models\GeocodingServiceProvider;
use HRQLS\Exceptions\FailedRequestException;

/**
 * Defines Geocoding Service Provider unit tests.
 */
final class GeocodingServiceProviderTests extends PHPUnit_Framework_TestCase
{
    /**
     * Constructs the base mock objects reuired to test the Geocoding Service provider.
     *
     * @return array like [
     *     'guzzle' => Mock Guzzle Service Provider Object.
     *     'response' => Mock Guzzle Response Object.
     *     'bodyContent' => Mock Body Content for Response
     * ];
     */
    private function getMocks()
    {
        $guzzleMock = $this->getMockBuilder('GuzzleHttp\Client')
            ->disableOriginalConstructor()
            ->setMethods(['get'])
            ->getMock();
            
        $responseMock = $this->getMockBuilder('GuzzleHttp\Psr7\Response')
            ->disableOriginalConstructor()
            ->setMethods(['getStatusCode', 'getBody'])
            ->getMock();
            
        $bodyContentMock = $this->getMockBuilder('GuzzleHttp\Psr7\Stream')
            ->disableOriginalConstructor()
            ->setMethods(['getContents'])
            ->getMock();
            
        return [
            'guzzle' => $guzzleMock,
            'response' => $responseMock,
            'bodyContent' => $bodyContentMock
        ];
    }
    
    /**
     * Sets up environment variables required for Geocoding Service Provider unit tests.
     *
     * @return void
     */
    protected function setUp()
    {
        putenv('GOOGLE_GEOCODE_KEY=3471337');
    }
    
    /**
     * removes environment variable created for these unit tests.
     *
     * @return void
     */
    protected function tearDown()
    {
        putenv('GOOGLE_GEOCODE_KEY');
    }
    
    /**
     * Verifies constructor for geocoding service provider.
     *
     * @return void
     */
    public function testConstructor()
    {
        $geocoder = new GeocodingServiceProvider(self::getMocks()['guzzle']);
        
        $this->assertInstanceOf('HRQLS\Models\GeocodingServiceProvider', $geocoder);
    }
    
    /**
     * Verifies behaviour when geocoding an address.
     *
     * @return void
     */
    public function testGeocode()
    {
        $mocks = self::getMocks();
        $guzzleMock = $mocks['guzzle'];
        $responseMock = $mocks['response'];
        $bodyContentMock = $mocks['bodyContent'];
        
        $responseBody = [
            'results' => [
                [
                    'geometry' => [
                        'location' => [
                            'lat' => 0,
                            'lng' => 0,
                        ],
                    ],
                ],
            ],
        ];
        
        $expected = [
            'lat' => 0,
            'lon' => 0,
        ];
        
        $bodyContentMock->method('getContents')->willReturn(json_encode($responseBody));
        $responseMock->method('getStatusCode')->willReturn(200);
        $responseMock->method('getBody')->willReturn($bodyContentMock);
        
        $guzzleMock->method('get')->willReturn($responseMock);
        
        $geocoder = new GeocodingServiceProvider($guzzleMock);
        
        $actual = $geocoder->geocode('221 Baker Street, London UK');
        
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * Verifies behvaiour when geocoding an address that cannot be geocoded.
     *
     * @return void
     *
     * @expectedException HRQLS\Exceptions\FailedRequestException
     */
    public function testGeocode_Address()
    {
        $mocks = self::getMocks();
        $guzzleMock = $mocks['guzzle'];
        $responseMock = $mocks['response'];
        
        $responseMock->method('getStatusCode')->willReturn(400);
        $guzzleMock->method('get')->willReturn($responseMock);
        
        $geocoder = new GeocodingServiceProvider($guzzleMock);
        
        $geocoder->geocode('221 Baker St.');
    }
}
