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
use HRQLS\Exceptions\UsageException;

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
     * @param array $indices The list of indices to include in the query. Defaults to searching all indices.
     * @param array $types   The list of types to include in the query. Defaults to searching all types.
     * @param array $query   The query to run against the specified indexes and types.
     *
     * @return array Like [
     *    'took' => The amount of time the query took to execute,
     *    'timed_out' => [
     *      '_shards' => [
     *        'total' => (Integer), Total number of shards queried,
     *        'successful' => (integer), Number of successfully queried shards.
     *        'failed' => (integer), Number of shards that could not be queried.
     *    ],
     *    'hits' => [
     *      'total' => (integer), Number of documents returned by the query.
     *      'max_score' => (double), The highest score assigned during execution.
     *      'hits' => [
     *      ],
     *    ],
     *  ];
     */
    public function search(array $indices, array $types, array $query = [])
    {
        if (empty($indices)) {
            $indices = ['_all'];
        }
        
        if ($types === null) {
            $types = [];
        }
        
        $req = [
            'index' => implode(',', $indices),
            'types' => implode(',', $types),
            'body' => $query,
        ];
        
        return $this->client->search($req);
    }
    
    /**
     * Inserts (a.k.a indexes) the document into the specified index.
     *
     * @param string  $index The name of the index to insert the document.
     * @param string  $type  The type (a.k.a collection) to insert the document.
     * @param array   $doc   The document to insert.
     * @param integer $id    The Id to assign the document being inserted. If null Id is auto-assigned (recommended).
     *
     * @return array Like [
     *    '_shards' => [
     *        'total' => (Integer),
     *        'failed' => (Integer),
     *        'successful' = > (Integer),
     *    ],
     *    '_index' => (String),
     *    '_type' => (String),
     *    '_id' => (String),
     *    '_version' => (Integer),
     *    'created' => (Boolean),
     *  ];
     *
     * @throws UsageException When $index, $type, or $doc is null.
     */
    public function insert($index, $type, array $doc, $id = null)
    {
        $errorMessages = [];
        
        if (empty($index)) {
            $errorMessages[] = '$index cannot be null.';
        }
        
        if (empty($type)) {
            $errorMessages[] = '$type cannot be null.';
        }

        if (empty($doc)) {
            $errorMessages[] = "An empty document cannot be inserted. That doesn't make any sense.";
        }

        if (!empty($errorMessages)) {
            throw new UsageException(implode("\n", $errorMessages));
        }

        $req = [
            'index' => $index,
            'type' => $type,
            'body' => $doc,
        ];
        
        if (!empty($id)) {
            $req['id'] = $id;
        }
        
        return $this->client->index($req);
    }
    
    /**
     * Adds a new type to an index with the mappings. Index and type will be created if they do not exist.
     *
     * @param string $index   The name of the index the mapping will be added to.
     * @param string $name    The name of the mapping/type you are creating.
     * @param array  $mapping An array of mapping properties that define the mapping/type.
     *
     * @return boolean True on success otherwise false.
     *
     * @throws UsageException When $index, $name, or $mapping are empty/null.
     */
    public function addMapping($index, $name, array $mapping)
    {
        $errorMessages = [];
        
        if (empty($index)) {
            $errorMessages[] = 'The index that mappings will be added to must be specified. $index cannot = null';
        }
        
        //Block mappings from being added to any hercules-* index new or existing.
        
        $response = $this->client->create($index);
        //Check for error. and throw exception accordingly.
        
        if (empty($name)) {
            $errorMessages[] = 'A mapping must be assigned a name. $name cannot = null.';
        }
        
        if (empty($mapping)) {
            $errorMessages[] = "$mapping cannot be empty. That doesn't make any sense.";
        }
        
        if (!empty($errorMessages)) {
            throw new UsageException(implode("\n", $errorMessages));
        }

        $req = [
            'index' => $index,
            'type' => $name,
            'body' => $mapping,
        ];
        
        return $this->client->putMapping($req);
    }
    
    /**
     * Returns all of the mappings for the specified indices, and types.
     *
     * @param array $indices The indices to get mappings from.
     * @param array $types   The types to get mappings for.
     *
     * @return array The mappings for the specified indice and type.
     */
    public function getMappings(array $indices, array $types)
    {
        //The mapping and all inherited mappings.
        return '';
    }
}
