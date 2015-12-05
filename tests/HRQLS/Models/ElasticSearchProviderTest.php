<?php

use Silex\Application;
use HRQLS\Models\ElasticSearchServiceProvider;
use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;

class ElasticSearchProviderTest extends PHPUnit_Framework_TestCase
{
    /**
     * Provides mock objects for tests.
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

        $result = $esServiceProvider->search(['test query']);

        $this->assertEquals('test', $result);
    }
}
