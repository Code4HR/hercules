<?php
/**
 * Provider for Guzzle Http Client
 *
 * @package HRQLS\Models
 */
 
namespace HRQLS\Models;

use Silex\Application;
use GuzzleHttp\Client;
use Silex\ServiceProviderInterface;

/**
 * Defines GuzzleServiceProvider Class.
 */
final class GuzzleServiceProvider implements ServiceProviderInterface
{
    /**
     * The Guzzle HTTP Client used by this provider to make requests
     *
     * @var Guzzle Client
     */
    private $guzzle;

    /**
     * Creates a GuzzleServiceProvider usingthe configured GuzzleClient.
     *
     * @param GuzzleHttp\Client $guzzleClient A GuzzleHttp\Client object.
     */
    public function __construct(Client $guzzleClient)
    {
        $this->guzzle = $guzzleClient;
    }
    
    /**
     * Registers guzzle with the Silex appliation.
     *
     * @param Application $app The Silex application this service provider is being registered to.
     *
     * @return void
     */
    public function register(Application $app)
    {
        $app['guzzle'] = $this;
    }
    
    /**
     * This function is here just so the ServiceProviderInterface is fully implemented.
     * According to the documentation it really shouldn't have to be here but there are errors when it isn't.
     *
     * @param Application $app A pointless Silex Application because we won't be using it.
     *
     * @return void
     */
    public function boot(Application $app)
    {
    }

    /**
     * Exposes Http(s) Get Requests through service provider
     *
     * @param string $url     The URL to send the Get request to.
     * @param array  $options An array of guzzle configuration options.
     *
     * @return Response A Http Response object containing whatever it is you just done got.
     */
    public function get($url, array $options)
    {
        return $this->guzzle->request('GET', $url, $options);
    }
}
