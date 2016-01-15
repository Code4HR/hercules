<?php
/**
 * Provider for Guzzle HttP Client
 *
 * @package HRQLS\Models
 */
use GuzzleHttp\Client;

/**
 * defines GuzzleHttpClient
 */
final class GuzzleHttpClient
{
    /**
     * The Guzzle HTTp Client
     *
     * @var Guzzle Client
     */
    private $client;

     /**
      * Registers a Guzzle client
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
