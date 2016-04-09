<?php
/**
 * Provider for Guzzle Http Client
 *
 * @package HRQLS\Models
 */
 
namespace HRQLS\Models;
 
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
    private $guzzle;

     /**
      * Registers a Guzzle client with Silex App
      *
      * @param GuzzleHttp\Client $guzzleClient A GuzzleHttp\Client object.
      */
    public function __construct(Client $guzzleClient)
    {
        $this->guzzle = $guzzleClient;
    }
}
