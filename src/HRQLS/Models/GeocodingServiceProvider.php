<?php
/**
 * Google Geocoding Service Provider for Hercules Framework
 *
 * @package HRQLS\Models
 */
namespace HRQLS\Models;

use Silex\Application;
use GuzzleHttp\Client;
use Silex\ServiceProviderInterface;
use HRQLS\Exceptions\FailedRequestException;

/**
 * Defines the Geocoding Service Provider class.
 */
class GeocodingServiceProvider implements ServiceProviderInterface
{
    
    /**
     * @var local variable to hold API key for Google Geocode API.
     */
    private $apiKey;
    
    /**
     * @var local variable for holding guzzle client.
     */
    private $guzzle;
    
    /**
     * @var URL for Google Geocoding API
     */
    const URL = 'https://maps.googleapis.com/maps/api/geocode/json';
    
    /**
     * Constructs a new GeocodingServiceProvider.
     *
     * @param Client $guzzle The guzzle client to be used.
     *
     * @return void
     */
    public function __construct(Client $guzzle)
    {
        $this->guzzle = $guzzle;
        $this->apiKey = getenv('GOOGLE_GEOCODE_KEY');
    }
    
    /**
     * Registers the GeocodingServiceProvider with the specified Silex Application.
     *
     * @param Application $app Silex application this service provider is being registered with.
     *
     * @return void
     */
    public function register(Application $app)
    {
        $app['geocode'] = $this;
    }
    
    /**
     * This is a useless function other than to fully implement the ServiceProviderInterface.
     *
     * @param Application $app A Pointless declaration because we don't use this.
     *
     * @return void
     */
    public function boot(Application $app)
    {
    }
    
    /**
     * Converts a street address to coordinates of latitude and longitude using Google Geocoe API.
     *
     * @param string $address The address to be converted to Lat Lon.
     *
     * @return array Like [
     *   'lat' => (float),
     *   'lon' => (float),
     * ];
     *
     * @throws FailedRequestException When Google API does not respond a Status Code of 200.
     */
    public function geocode($address)
    {
        //Construct the request URL
        $requestUrl = self::URL . '?address' . urlencode($address) . '&' . $this->apiKey;

        $response = $this->guzzle->get(self::URL);
        
        if ($response->getStatusCode() != 200) {
            throw new FailedRequestException("{$address} failed geocoding. Sorz!", $response->getStatusCode());
        }
        
        $coordinates = $response->getBody()['results']['geometry']['location'];
        
        return [
            'lat' => $coordinates['lat'],
            'lon' => $coordinates['lng'],
        ];
    }
}
