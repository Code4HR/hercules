<?php
/**
 * Controller for Hampton Crime Data.
 *
 * @package HRQLS/Controllers
 */
 
namespace HRQLS\Controllers\Crime;

use HRQLS\Bootstrap;
use Silex\Application;
use HRQLS\Models\GuzzleServiceProvider;
use HRQLS\Models\Controllers\Crime\DataPoint;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use HRQLS\Models\ElasticSearchServiceProvider;

/**
 * Defines controller for Crime/Hampton API endpoint.
 */
final class Hampton
{
    /**
     * Main entry point for City of Hampton Crime Endpoint.
     * Lists all of the crime DataPoints available for the City of Hampton.
     *
     * @param Request     $req The Request object to be handled.
     * @param Application $app Silex Application object responsible for handling requests.
     *
     * @return array Like [
     *   'endpoint' => '/crime/Hampton',
     *   'datetime' => 'Y-m-d H:i:s', @see php DateTime::format()
     *   'data' => [
     *     DataPoint,
     *     ...
     *   ],
     *   'error' => [
     *     'code' => (Integer),
     *     'message' => 'You done gone and broke it now!',
     *   ],
     * ];
     */
    public function main(Request $req, Application $app)
    {
        return [];
    }
    
    /**
     * Gets exactly one crime datapoint.
     *
     * @param Request     $req The Request object to be handled.
     * @param Application $app Silex Application object responsible for handling requests.
     *
     * @return \HRQLS\Controllers\Crime\DataPoint
     */
    public function get(Request $req, Application $app)
    {
        //@TODO fix the return statement cause that's hella busted.
        return new DataPoint('', '', '', new DateTime(), '', []);
    }
    
    /**
     * Refreshes the Hampton Crime Data stored in ES if $timestamp >= NextRefreshTimestamp for this endpoint.
     *
     * @param Application $app       Silex Application used to handle refreshing data.
     * @param \DateTime   $timestamp The timestamp of the current request.
     *
     * @return void
     */
    private function refreshStaleData(Application $app, \DateTime $timestamp)
    {
        $query = [
            'query' => [
                'match' => ['endpoint' => '/crime/Hampton']
            ]
        ];
        
        $response = $app['elasticsearch']->search('crime', 'refresh-timestamps', $query);
        
        if ($timestamp >= $app['elasticsearch']->getResults($response)[0]['next_refresh_epoch']) {
            continue;
        }
    }
}
