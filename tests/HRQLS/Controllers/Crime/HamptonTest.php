<?php
/**
 * Test file for City of Hampton Crime endpoint.
 *
 * @package tests/HRQLS/Controllers
 */
use HRQLS\Bootstrap;
use Silex\Application;
use HRQLS\Controllers\Crime\Hampton;
use HRQLS\Controllers\Crime\DataPoint;
use HRQLS\Models\GuzzleServiceProvider;
use HRQLS\Models\ElasticSearchServiceProvider;

/**
 * Defines Hampton Crime Controller unit tests.
 */
class HamptonTest extends PHPUnit_Framework_TestCase
{
    /**
     * Just a comment.
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
        //Get DB Service Providers
        $bootstrap->connectDatabases();
        //Get HTTP Service Providers.
        $bootstrap->loadHttpClients();
        
        $crimes = json_decode(file_get_contents('https://data.hampton.gov/resource/umc3-tsey.json'));
        
        foreach ($crimes as $crime) {
            continue;
        }
    }
}
