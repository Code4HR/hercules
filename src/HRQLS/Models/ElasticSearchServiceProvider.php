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
use HRQLS\Exceptions\ProtectedIndexException;

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
        $app['elasticsearch'] = $this;
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
     * It uses scan search type and scroll API to retrieve large numbers of documents.
     *
     * @param array $indices The list of indices to include in the query. Defaults to searching all indices.
     * @param array $types   The list of types to include in the query. Defaults to searching all types.
     * @param array $query   The query to run against the specified indexes and types.
     *
     * @return array Like [
     *    'total' => (integer), Number of documents returned by the query,
     *    'hits' => [
     *      result set
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
            'search_type' => 'scan',
            'scroll' => '1m',
            'index' => implode(',', $indices),
            'type' => implode(',', $types),
            'body' => $query,
        ];
        
        $response = $this->client->search($req);
        $scrollId = $response['_scroll_id'];
        $totalResults['total'] = $response['hits']['total'];
        $totalResults['hits'] = [];
        do {
            $totalResults['hits'] = array_merge($totalResults['hits'], $response['hits']['hits']);
            $response = $this->client->scroll(['scroll_id' => $scrollId, 'scroll' => '1m']);
            $results = $response['hits']['hits'];
            $scrollId = $response['_scroll_id'];
        } while (count($results) > 0);

        return $totalResults;
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
     * @return array like ['acknowledged' => (Boolean)];
     *
     * @throws ProtectedIndexException When $index starts with 'hercules-'.
     * @throws UsageException When $index, $name, or $mapping are empty/null.
     */
    public function addMapping($index, $name, array $mapping)
    {
        $errorMessages = [];
        
        if (empty($index)) {
            throw new UsageException('$index cannot be null.');
        }
        
        //Block mappings from being added to any hercules-* index new or existing.
        if (preg_match('/^(hercules-)/', $index, $matches)) {
            throw new ProtectedIndexException("Mappings cannot be added to protected {$matches}* Indices.");
        }
       
        //Attempt to create the index. If the index already exists an indexAlreadyExists Exception is thrown
        try {
            $response = $this->client->create($index);
            if ($response['acknowledged'] != true) {
                $errorMessages[] = "The request to create {$index} was not acknowledged.";
            }
        } catch (\Exception $e) {
            //Report error message only if it was not an error stating the index already existed.
            //IndexAlreadyExistsException is one of ES's custom exceptions.
            if (get_class($e) === 'IndexAlreadyExistsException') {
                $errorMessages[] = "Failed to create {$index} with error {$e->getMessage()}.";
            }
        }
        
        if (empty($name)) {
            $errorMessages[] = '$name cannot = null.';
        }
        
        if (empty($mapping)) {
            $errorMessages[] = "An empty mapping cannot be created. That doesn't make any sense.";
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
     * @param array $indices The indices to get mappings from. Defaults to all indices.
     * @param array $types   The types to get mappings for. Defaults to all types.
     *
     * @return array like [
     *    'indexName' => [
     *      'mappings' => [
     *        'typeName' => [
     *          'properties' => [
     *            'fieldName' => [
     *              'type' => (string)..
     *            ],
     *          ],
     *        ],
     *      ],
     *    ]...
     * ];
     */
    public function getMappings(array $indices = [], array $types = [])
    {
        //If no indices or types are specified return mappings for all indices and types.
        if (empty($indices) && empty($types)) {
            return $this->client()->indices()->getMapping();
        }
        
        return $this->client->indices()->getMapping(['index' => $indices, 'type' => $types]);
    }
    
    /**
     * Gets the result set from an ElasticSearch Query Response.
     *
     * @param array $queryResponse A standard Elastic Search Query Response.
     *
     * @return array
     */
    public function getResults(array $queryResponse)
    {
        return $queryResponse['hits']['hits'];
    }
}
