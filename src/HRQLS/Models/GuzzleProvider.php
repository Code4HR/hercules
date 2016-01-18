<?php
/**
 * Provider for Guzzle HttP Client
 *
 * @package HRQLS\Models
 */
use GuzzleHttp\Client;

/**
 * Defines GuzzleProvider Class.
 */
final class GuzzleProvider
{
    /**
     * The Guzzle HTTP Client used by this provider to make requests
     *
     * @var Guzzle Client
     */
    private $client;

     /**
      * Registers a Guzzle client with Silex App
      *
      * @param Client $guzzleClient A GuzzleHttp\Client object.
      */
    public function __construct(Client $guzzleClient)
    {
        $this->app = $client;
    }

    /**
     * gets the GuzzleHttp\Client object
     *
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }
}
