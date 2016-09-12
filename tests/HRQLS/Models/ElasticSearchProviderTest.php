<?php
/**
 * Test file for ElasticSearch Service Provider
 *
 * @package tests/HRQLS/Models
 */
use Silex\Application;
use HRQLS\Models\ElasticSearchServiceProvider;
use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use HRQLS\Exceptions\UsageException;

/**
 * Defines ElasticSearch Service Provider Unit Tests
 */
class ElasticSearchProviderTest extends PHPUnit_Framework_TestCase
{
    
    const MOCK_SEARCH_HITS = [
        'total' => 0,
        'max_score' => 0,
        'hits' => [],
    ];
    
    /**
     * Provides mock objects for tests.
     *
     * @return array Like [
     *     ES Client Builder Mock,
     *     SilexApp Mock,
     *     ES Client Mock,
     * ]
     */
    public function getMockObjects()
    {
        $esClientBuilderMock = $this->getMockBuilder('ElasticSearch\ClientBuilder')
            ->disableOriginalConstructor()
            ->setMethods(['setHosts', 'build'])
            ->getMock();

        $appMock = $this->getMockBuilder('Silex\Application')
            ->setMethods(['register'])
            ->getMock();
            
        $esIndicesMock = $this->getMockBuilder('Elasticsearch\Namespaces\IndicesNamespace')
            ->disableOriginalConstructor()
            ->setMethods(['getMapping'])
            ->getMock();

        $esClientMock = $this->getMockBuilder('ElasticSearch\Client')
            ->disableOriginalConstructor()
            ->setMethods(['search', 'scroll', 'index', 'create', 'putMapping', 'indices'])
            ->getMock();

        $esClientBuilderMock->method('build')
            ->willReturn($esClientMock);
            
        $esClientMock->method('indices')->willReturn($esIndicesMock);

        return [
                $esClientBuilderMock,
                $appMock,
                $esClientMock
        ];
    }

    /**
     * Tests that the constructor properly calls the Elasticsearch Client Builder.
     *
     * @return void
     */
    public function testConstructor()
    {
        $mocks = $this->getMockObjects();
        $appMock = $mocks['1'];
        $esClientBuilderMock = $mocks['0'];

        $appMock['elasticsearch.url'] = 'Test';

        $esServiceProvider = new ElasticSearchServiceProvider($esClientBuilderMock);

        $esClientBuilderMock->expects($this->once())
            ->method('setHosts')
            ->with(['Test']);

        $esClientBuilderMock->expects($this->once())
            ->method('build');

        $esServiceProvider->register($appMock);
    }

    /**
     * Verifies that search functionality is exposed by the service provider.
     *
     * @return void
     */
    public function testSearch()
    {
        //Get the mock objects.
        list($esClientBuilderMock, $appMock, $esClientMock) = $this->getMockObjects();

        //Set the mocked return array from Elastic Search
        $mockedEsResults = static::getSearchReturnArray('347', self::MOCK_SEARCH_HITS);
        
        // Set the ESClientMock's return value for the search method to $expected.
        // I'm still fuzzy on the differences between will and willReturn so I used willReturn for readability.
        $esClientMock->method('search')
            ->willReturn($mockedEsResults);

        $esClientMock->method('scroll')
            ->willReturn($mockedEsResults);
        
        //Creates a new ElasticSearchServiceProvider from the ES Builder Mock.
        $esServiceProvider = new ElasticSearchServiceProvider($esClientBuilderMock);
        //Ensures the ES Client being used is our Mock Client.
        $esServiceProvider->setClient($esClientMock);

        //Query all documents under testIndex/testType.
        $actual = $esServiceProvider->search(['testIndex'], ['testType'], []);

        $expected = [
            'total' => 0,
            'hits' => []
        ];
        
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * Verifies that search functionality has implemented scan and scroll.
     *
     * @return void
     */
    public function testPaginatedSearch()
    {
        //Get the mock objects.
        list($esClientBuilderMock, $appMock, $esClientMock) = $this->getMockObjects();

        $mockedEsResultsPage1 = [
            '_scroll_id' => '347',
            'total' => 11,
            'max_score' => 1.0,
            'hits' => [
                'total' => 10,
                'hits' => [
                    ['_id' => 1, '_source' => [ 'field1' => 'val1'] ],
                    ['_id' => 2, '_source' => [ 'field1' => 'val2'] ],
                    ['_id' => 3, '_source' => [ 'field1' => 'val3'] ],
                    ['_id' => 4, '_source' => [ 'field1' => 'val4'] ],
                    ['_id' => 5, '_source' => [ 'field1' => 'val5'] ],
                    ['_id' => 6, '_source' => [ 'field1' => 'val6'] ],
                    ['_id' => 7, '_source' => [ 'field1' => 'val7'] ],
                    ['_id' => 8, '_source' => [ 'field1' => 'val8'] ],
                    ['_id' => 9, '_source' => [ 'field1' => 'val9'] ],
                    ['_id' => 10, '_source' => [ 'field1' => 'val10'] ]
                ]
            ]
        ];
        
        $mockedEsResultsPage2 = [
            'hits' => [
                'total' => 1,
                'hits' => [
                    ['_id' => 11, '_source' => [ 'field1' => 'val11'] ]
                ]
            ]
        ] + $mockedEsResultsPage1;

        $esClientMock->method('search')
            ->willReturn([
                '_scroll_id' => '347',
                'hits' => [
                    'total' => 11,
                    'hits' => [],
                ]
            ]);

        $esClientMock->method('scroll')
            ->will($this->onConsecutiveCalls($mockedEsResultsPage1, $mockedEsResultsPage2));
        
        //Creates a new ElasticSearchServiceProvider from the ES Builder Mock.
        $esServiceProvider = new ElasticSearchServiceProvider($esClientBuilderMock);
        //Ensures the ES Client being used is our Mock Client.
        $esServiceProvider->setClient($esClientMock);

        //Query all documents under testIndex/testType.
        $actual = $esServiceProvider->search(['testIndex'], ['testType'], []);

        $expected = [
            'total' => 11,
            'hits' => [
                ['_id' => 1, '_source' => [ 'field1' => 'val1'] ],
                ['_id' => 2, '_source' => [ 'field1' => 'val2'] ],
                ['_id' => 3, '_source' => [ 'field1' => 'val3'] ],
                ['_id' => 4, '_source' => [ 'field1' => 'val4'] ],
                ['_id' => 5, '_source' => [ 'field1' => 'val5'] ],
                ['_id' => 6, '_source' => [ 'field1' => 'val6'] ],
                ['_id' => 7, '_source' => [ 'field1' => 'val7'] ],
                ['_id' => 8, '_source' => [ 'field1' => 'val8'] ],
                ['_id' => 9, '_source' => [ 'field1' => 'val9'] ],
                ['_id' => 10, '_source' => [ 'field1' => 'val10'] ],
                ['_id' => 11, '_source' => [ 'field1' => 'val11'] ]
            ]
        ];

        $this->assertEquals($expected, $actual);
    }
    
    /**
     * Verifies that functionality to insert a document is exposed by the service provider.
     *
     * @return void
     */
    public function testInsert()
    {
        $docId = 1337;

        //Get the mock objects
        list($esClientBuilderMock, $appMock, $esClientMock) = $this->getMockObjects();

        //Creates the expected response Array for the index request.
        $expected = static::getIndexReturnArray('testIndex', 'testType', $docId, 1, true);
        
        $esClientMock->method('index')
            ->willReturn($expected);
        
        $esServiceProvider = new ElasticSearchServiceProvider($esClientBuilderMock);
        $esServiceProvider->setClient($esClientMock);
        
        $actual = $esServiceProvider->insert('testIndex', 'testType', ['key' => 'value'], $docId);

        $this->assertEquals($expected, $actual);
    }
    
    /**
     * Verifies behaviour when inserting a document without specifying an index.
     *
     * @return void
     *
     * @expectedException \HRQLS\Exceptions\UsageException
     * @expectedExceptionMessage $index cannot be null.
     */
    public function testInsert_NoIndex()
    {
        //Get the mock objects
        list($esClientBuilderMock, $appMock, $esClientMock) = $this->getMockObjects();
            
        $esServiceProvider = new ElasticSearchServiceProvider($esClientBuilderMock);
        $esServiceProvider->setClient($esClientMock);
        
        $esServiceProvider->insert('', 'testType', ['key' => 'value']);
    }
    
    /**
     * Verifies behaviour when inserting a document without specifying a type.
     *
     * @return void
     *
     * @expectedException \HRQLS\Exceptions\UsageException
     * @expectedExceptionMessage $type cannot be null.
     */
    public function testInsert_noType()
    {
        //Get the mock objects
        list($esClientBuilderMock, $appMock, $esClientMock) = $this->getMockObjects();
            
        $esServiceProvider = new ElasticSearchServiceProvider($esClientBuilderMock);
        $esServiceProvider->setClient($esClientMock);
        
        $esServiceProvider->insert('testIndex', '', ['key' => 'value']);
    }
    
    /**
     * Verifies behaviour when attempting to insert an empty document.
     *
     * @return void
     *
     * @expectedException \HRQLS\Exceptions\UsageException
     * @expectedExceptionMessage An empty document cannot be inserted. That doesn't make any sense.
     */
    public function testInsert_emptyDocument()
    {
        //Get the mock objects
        list($esClientBuilderMock, $appMock, $esClientMock) = $this->getMockObjects();
        
        $esServiceProvider = new ElasticSearchServiceProvider($esClientBuilderMock);
        $esServiceProvider->setClient($esClientMock);
        
        $esServiceProvider->insert('testIndex', 'testType', []);
    }
    
    /**
     * Verifies addMapping functionality is exposed by the Service Provider.
     * NOTE this also tests the behaviour when adding a mapping to a new index.
     *
     * @return void
     */
    public function testAddMapping()
    {
        $expected = ['acknowledged' => true];
        //Get the mock objects.
        list($esClientBuilderMock, $appMock, $esClientMock) = $this->getMockObjects();
        
        //Set the return values for esClient methods that are called in service provider.
        $esClientMock->method('create')->willReturn($expected);
        $esClientMock->method('putMapping')->willReturn($expected);
        
        //attach the mocked esClient to the Service Provider.
        $esServiceProvider = new ElasticSearchServiceProvider($esClientBuilderMock);
        $esServiceProvider->setClient($esClientMock);
        
        $actual = $esServiceProvider->addMapping('testIndex', 'testType', ['key' => 'value']);
        
        $this->AssertEquals($expected, $actual);
    }
    
    /**
     * Verifies behaviour when no index is specified.
     *
     * @return void
     *
     * @expectedException \HRQLS\Exceptions\UsageException
     * @expectedExceptionMessage $index cannot be null.
     */
    public function testAddMapping_noIndex()
    {
        $expected = ['acknowledged' => true];
        //Get the mock objects.
        list($esClientBuilderMock, $appMock, $esClientMock) = $this->getMockObjects();
        
        //attach the mocked esClient to the Service Provider.
        $esServiceProvider = new ElasticSearchServiceProvider($esClientBuilderMock);
        $esServiceProvider->setClient($esClientMock);
        
        $actual = $esServiceProvider->addMapping('', 'testType', ['key' => 'value']);
    }
    
    /**
     * Verifies behaviour when adding a mapping to an exsting index.
     *
     * @return void
     */
    public function testAddMapping_indexAlreadyExists()
    {
        $indexName = 'testIndex';
        $expected = ['acknowledged' => true];
        //Get the mock objects.
        list($esClientBuilderMock, $appMock, $esClientMock) = $this->getMockObjects();
        
        //Set the return values for esClient methods that are called in service provider.
        $esClientMock->method('create')
            ->will($this->throwException(new Exception("[{$indexName}] already exists")));
        $esClientMock->method('putMapping')->willReturn($expected);
        
        //attach the mocked esClient to the Service Provider.
        $esServiceProvider = new ElasticSearchServiceProvider($esClientBuilderMock);
        $esServiceProvider->setClient($esClientMock);
        
        $actual = $esServiceProvider->addMapping($indexName, 'testType', ['key' => 'value']);
        
        $this->AssertEquals($expected, $actual);
    }
    
    /**
     * Verifies behaviour when attempting to create a new index and the request was not acknowledged.
     *
     * @return void
     *
     * @expectedException \HRQLS\Exceptions\UsageException
     * @expectedExceptionMessage
     * Failed to create testIndex with error The request to create testIndex was not acknowledged.
     */
    public function testAddMapping_indexWasNotCreated()
    {
        list($esClientBuilderMock, $appMock, $esClientMock) = $this->getMockObjects();
        
        //Set the return values for esClient methods that are called in service provider.
        $esClientMock->method('create')->willReturn(['acknowledged' => false]);
        
        $esServiceProvider = new ElasticSearchServiceProvider($esClientBuilderMock);
        $esServiceProvider->setClient($esClientMock);
        
        $actual = $esServiceProvider->addMapping('testIndex', 'testType', ['key' => 'value']);
    }
    
    /**
     * Verifies behaviour when adding a mapping without specifying a type.
     *
     * @return void
     *
     * @expectedException \HRQLS\Exceptions\UsageException
     * @expectedExceptionMessage $name cannot = null.
     */
    public function testAddMapping_noType()
    {
        $expected = ['acknowledged' => true];
        //Get the mock objects.
        list($esClientBuilderMock, $appMock, $esClientMock) = $this->getMockObjects();
        
        //Set the return values for esClient methods that are called in service provider.
        $esClientMock->method('create')->willReturn($expected);
        $esClientMock->method('putMapping')->willReturn($expected);
        
        //attach the mocked esClient to the Service Provider.
        $esServiceProvider = new ElasticSearchServiceProvider($esClientBuilderMock);
        $esServiceProvider->setClient($esClientMock);
        
        $actual = $esServiceProvider->addMapping('testIndex', '', ['key' => 'value']);
    }
    
    /**
     * Verfies behaviour when no mapping is specified.
     *
     * @return void
     *
     * @expectedException \HRQLS\Exceptions\UsageException
     * @expectedExceptionMessage An empty mapping cannot be created. That doesn't make any sense.
     */
    public function testAddMapping_noMapping()
    {
        $expected = ['acknowledged' => true];
        //Get the mock objects.
        list($esClientBuilderMock, $appMock, $esClientMock) = $this->getMockObjects();
        
        //Set the return values for esClient methods that are called in service provider.
        $esClientMock->method('create')->willReturn($expected);
        $esClientMock->method('putMapping')->willReturn($expected);
        
        //attach the mocked esClient to the Service Provider.
        $esServiceProvider = new ElasticSearchServiceProvider($esClientBuilderMock);
        $esServiceProvider->setClient($esClientMock);
        
        $actual = $esServiceProvider->addMapping('testIndex', 'testType', []);
    }
    
    /**
     * Verifies getMapping functionality is exposed by Service Provider.
     *
     * @return void
     */
    public function testGetMapping()
    {
        list($esClientBuilderMock, $appMock, $esClientMock) = $this->getMockObjects();
        
        $expected = [
            'testIndex' => [
                'mappings' => static::buildMappingArray('testType', ['testField' => ['type' => 'string']])
            ]
        ];

        //set the return value for esClientMock->indices()->getMapping()
        $esClientMock->indices()->method('getMapping')->willReturn($expected);
        
        //attach the mocked esClient to the Service Provider.
        $esServiceProvider = new ElasticSearchServiceProvider($esClientBuilderMock);
        $esServiceProvider->setClient($esClientMock);
        
        $actual = $esServiceProvider->getMappings(['testIndex'], ['testType']);
        $this->AssertEquals($expected, $actual);
    }
    
    /**
     * Verfies behaviour when no indices or types are specified.
     *
     * @return void
     */
    public function testGetMapping_AllMappings()
    {
        list($esClientBuilderMock, $appMock, $esClientMock) = $this->getMockObjects();
        
        $expected = [
            'testIndex' => [
                'mappings' => static::buildMappingArray('testType', ['testField' => ['type' => 'string']])
            ],
            'testIndex2' => [
                'mappings' => static::buildMappingArray('testType2', ['testKeyField' => ['type' => 'int']])
            ],
        ];
        
        //set the return value for esClientMock->indices()->getMapping()
        $esClientMock->indices()->method('getMapping')->willReturn($expected);
        
        //attach the mocked esClient to the Service Provider.
        $esServiceProvider = new ElasticSearchServiceProvider($esClientBuilderMock);
        $esServiceProvider->setClient($esClientMock);
        
        $actual = $esServiceProvider->getMappings(['testIndex'], ['testType']);
        $this->AssertEquals($expected, $actual);
    }
    
    /**
     * Creates a mock ES search response array from the passed in data array.
     *
     * @param string $scrollId The scroll id that should be returned.
     * @param array  $data     Array of data injected into search return array.
     *
     * @return array Like [
     *    'took' => (Integer),
     *    'timed_out' => [
     *      '_shards' => [
     *        'total' => (Integer),
     *        'successful' => (Integer),
     *        'failed' => (Integer),
     *      ],
     *    ],
     *    'hits' => [
     *      'total' => (integer),
     *      'max_score' => (double)
     *      'hits' => $data
     *    ],
     *  ];
     */
    public function getSearchReturnArray($scrollId, array $data)
    {
        return [
         '_scroll_id' => $scrollId,
         'took' => 1337,
         'timed_out' => [
           '_shards' => [
             'total' => 2,
             'successful' => 1,
             'failed' => 1,
           ],
         ],
         'hits' => $data,
        ];
    }
    
    /**
     * Creates a mock ES Index response array from provided arguments.
     *
     * @param string  $index   The index in which the document was created under.
     * @param string  $type    The type in which the document was created under.
     * @param integer $id      The id assigned to the document.
     * @param integer $ver     The version of this document.
     * @param boolean $created A flag indicating if the document was indexed successfully or not.
     *
     * @return array like [
     *    '_shards' => [
     *        'total' => (Integer),
     *        'failed' => (Integer),
     *        'successful' => (Integer)
     *    ],
     *    '_index' => (string),
     *    '_type' => (string),
     *    '_id' => (Integer),
     *    '_version' => (Intger),
     *    'created' => (Boolean)
     * ];
     */
    public function getIndexReturnArray($index, $type, $id, $ver, $created)
    {
        return [
            '_shards' => [
              'total' => 1,
              'failed' => 0,
              'successful' => 1
            ],
            '_index' => $index,
            '_type' => $type,
            '_id' => $id,
            '_version' => $ver,
            'created' => $created
        ];
    }
    
    /**
     * Builds a Mapping Array from the specified type and properties array.
     *
     * @param string $type       The Name of the type this mapping applies to.
     * @param array  $properties Array containing field names, types etc. for ES Mapping.
     *
     * @return array Like [
     *    'typeName' => [
     *      'properties' => [
     *        'fieldName' => ['type' => string, ...]
     *        ...
     *      ],
     *    ]
     *  ];
     */
    public function buildMappingArray($type, array $properties)
    {
        return [$type => ['properties' => $properties]];
    }
    
    /**
     * Verifies behaviour of getResults function.
     *
     * @return void
     */
    public function testGetResults()
    {
        $mocks = $this->getMockObjects();
        $esClientBuilderMock = $mocks[0];
        
        $esServiceProvider = new ElasticSearchServiceProvider($esClientBuilderMock);
        
        $resultSet = [
            'took' => 10,
            'timed_out' => [
                '_shards' => [
                    'total' => 2,
                    'successful' => 1,
                    'failed' => 1
                ],
            ],
            'hits' => [
               'total' => 1,
               'max_score' => 1.0,
               'hits' => [
                   [
                       '_index' => 'testIndex',
                       '_type' => 'testType',
                       '_id' => '1337',
                       '_score' => 1.0,
                       '_source' => [
                           'key1' => 'a crap value',
                           'key2' => 'not so crappy a value'
                       ]
                   ],
                   [
                       '_index' => 'testIndex',
                       '_type' => 'testType',
                       '_id' => '1337',
                       '_score' => 1.0,
                       '_source' => [
                           'key1' => 'a uniquely crappy value',
                           'key2' => 'a decent value'
                       ]
                   ]
               ],
            ],
        ];
        
        $expected = $resultSet['hits']['hits'];
        $actual = $esServiceProvider->getResults($resultSet);
        
        $this->assertEquals($expected, $actual);
    }
}
