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

        $esClientMock = $this->getMockBuilder('ElasticSearch\Client')
            ->disableOriginalConstructor()
            ->setMethods(['search'])
            ->getMock();

        $esClientBuilderMock->method('build')
            ->willReturn($esClientMock);

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

        //Create a the expected results array for search.
        $expected = static::getSearchReturnArray(self::MOCK_SEARCH_HITS);
        
        // Set the ESClientMock's return value for the search method to $expected.
        // I'm still fuzzy on the differences between will and willReturn so I used willReturn for readability.
        $esClientMock->method('search')
            ->willReturn($expected);

        //Sets the elasticSearch URL to use for testing. Since this is a UNti Test we set it to a nonsensical value.
        $appMock['elasticsearch.url'] = 'Test';
        //Creates a new ElasticSearchServiceProvider from the ES Builder Mock.
        $esServiceProvider = new ElasticSearchServiceProvider($esClientBuilderMock);
        //Ensures the ES Client being used is our Mock Client.
        $esServiceProvider->setClient($esClientMock);

        //Query all documents under testIndex/testType.
        $result = $esServiceProvider->search(['testIndex'], ['testType'], []);

        $this->assertEquals($expected, $result);
    }
    
    /**
     * Verifies that functionality to insert a document is exposed by the service provider.
     *
     * @return void
     */
    public function testInsert()
    {
        //Get the mock objects
        list($esClientBuilderMock, $appMock, $esClientMock) = $this->getMockObjects();
        
        
    }
    
    /**
     * Creates a mock ES search response array from the passed in data array.
     *
     * @param array $data Array of data injected into search return array.
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
    public function getSearchReturnArray(array $data)
    {
        return [
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
}
