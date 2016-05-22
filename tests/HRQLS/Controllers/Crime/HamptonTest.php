<?php
/**
 * Test file for City of Hampton Crime endpoint.
 *
 * @package tests/HRQLS/Controllers
 */
use HRQLS\Bootstrap;
use Silex\Application;
use HRQLS\Controllers\Crime\Hampton;
use HRQLS\Models\GuzzleServiceProvider;
use HRQLS\Models\ElasticSearchServiceProvider;

/**
 * Defines Hampton Crime Controller unit tests.
 */
class HamptonTest extends PHPUnit_Framework_TestCase
{
    /**
     * Uses a reflection class to test the private refreshStaleData function to do a one time seed of the crime database.
     *
     * @test
     *
     * @return void
     */
    public function testSeedCrimeData()
    {
        //Sets up the Hercules Framework and connects elasticsearch and Guzzle service providers
        $app = new Silex\Application();
        $bootstrap = new Bootstrap($app);
        $bootstrap->loadConfig();
        $bootstrap->connectDatabases();//For Elasticsearch.
        $bootstrap->loadHttpClients();//For Guzzle.
        
        $query = [
            'query' => [
                'match' => ['endpoint' => '/crime/Hampton']
            ]
        ];
        
        $refreshTime = $app['elasticsearch']->search(['crime'], ['refresh-timestamps'], $query);
        var_dump($refreshTime);
    }
}
