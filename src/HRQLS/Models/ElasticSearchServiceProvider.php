<?php
/**
 * The ElasticSearch Service Provider for Silex.
 *
 * @package HRQLS\Models
 */

namespace HRQLS\Models;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;

/**
 * This is the class that handles registering and exposing functionality to Silex for ElasticSearch.
 */
class ElasticSearchServiceProvider implements ServiceProviderInterface
{
    /**
     * The Elastic Search client builder.
     *
     * @var ClientBuilder
     */
    private $elasticsearch;

    /**
     * The Elastic Search client.
     *
     * @var Client
     */
    private $client;

    /**
     * Service Constructor.
     *
     * @param ClientBuilder $elasticsearch The ElasticSearch client object to use.
     *
     * @return void
     */
    public function __construct(ClientBuilder $elasticsearch)
    {
        $this->elasticsearch = $elasticsearch;
    }

    /**
     * This is run when the service provider is registered with Silex.
     *
     * @param Application $app The Silex application which has the configuration data.
     *
     * @return void
     */
    public function register(Application $app)
    {
        $this->elasticsearch->setHosts([$app['elasticsearch.url']]);
        $this->client = $this->elasticsearch->build();
    }

    /**
     * Due to the nature of generating a ES client object, this is provided to support unit testing.
     *
     * @param Client $client The client object to use instead of the normally generated one.
     *
     * @return void
     */
    public function setClient(Client $client)
    {
        $this->client = $client;
    }

    /**
     * This is run before any request is made.
     *
     * @param Application $app The Silex application.
     *
     * @return void
     */
    public function boot(Application $app)
    {
    }

    /**
     * Search functionality exposed for use through the service provider.
     *
     * @param array $req The request to be sent to the ElasticSearch client.
     *
     * @return array
     */
    public function search(array $req)
    {
        return $this->client->search($req);
    }
}
