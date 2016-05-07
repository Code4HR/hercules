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
     * Tests that search requests are properly handled by the service provider.
     *
     * @return void
     */
    public function testSearch()
    {
        $mocks = $this->getMockObjects();
        $appMock = $mocks['1'];
        $esClientBuilderMock = $mocks['0'];
        $esClientMock = $mocks['2'];

        $esClientMock->method('search')
            ->willReturn('test');

        $appMock['elasticsearch.url'] = 'Test';
        $esServiceProvider = new ElasticSearchServiceProvider($esClientBuilderMock);
        $esServiceProvider->setClient($esClientMock);

        $esClientMock->expects($this->once())
            ->method('search')
            ->with(['test query']);

        $result = $esServiceProvider->search(['test query'], [], []);

        $this->assertEquals('test', $result);
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
