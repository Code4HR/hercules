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
use HRQLS\Controllers\Crime\DataPoint;
use HRQLS\Models\HerculesResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use HRQLS\Models\ElasticSearchServiceProvider;

/**
 * Defines controller for Crime/Hampton API endpoint.
 */
final class Hampton
{
    /**
     * List of indices to use to pull data for this endpoint.
     *
     * @var array
     */
    private $indices = ['hercules-crime_v1'];

    /**
     * List of types to use to pull data for this endpoint.
     *
     * @var array
     */
    private $types = ['crimes'];
    
    /**
     * Default Location array for the City of Hampton.
     *
     * @var array
     */
    private $defaultCoordinates = ['lat' => 37.0450412, 'lon' => -76.4309788];

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
        $response = new HerculesResponse('/crime/Hampton');

        //$this->refreshStaleData($app, new \DateTime());

        $esResult = $app['elasticsearch']->search($this->indices, [], []);

        $response = $this->parseResults($esResult, $response);

        // The frontend expects a JSONP format, to do this the response must be wrapped in a callback.
        return $_GET['callback'] . '('.$response->to_json().')';
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
        $crimes = json_decode(file_get_contents('https://data.hampton.gov/resource/umc3-tsey.json'));
        
        foreach ($crimes as $crime) {
            $coordinates = $app['geocode']->geocode($crime->address);
            
            if ($coordinates['lat'] === null || $coordinates['lon'] === null) {
                $coordinates = $this->defaultCoordinates;
            }
            
            $crimeDoc = new DataPoint(
                $crime->description,
                new \DateTime($crime->date_time),
                'Hampton',
                $coordinates
            );
            
            try {
                $app['elasticsearch']->insert($this->indices, $this->types, $crimeDoc->toArray());
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }
    }

    /**
     * Parse the results from Elasticsearch for the Hampton Crime data set.
     *
     * @param array            $results  The json data from the request.
     * @param HerculesResponse $response The response object to append data to.
     *
     * @return HerculesReponse The response object all pretty.
     */
    private function parseResults(array $results, HerculesResponse $response)
    {
        // Parse the results.
        $resultArray = $results['hits']['hits'];
        foreach ($resultArray as $key => $value) {
            $offense = $value['_source']['offense'];
            $category = $value['_source']['category'];
            $class = $value['_source']['class'];
            $occured = new \DateTime($value['_source']['occured']);
            $city = $value['_source']['city'];
            $location = $value['_source']['location'];

            if (isset($occured) && gettype($location) === 'array') {
                $datapoint = new DataPoint($offense, $occured, $city, $location, $category, $class);
                $response->addDataEntry($datapoint->toArray());
            }
        }

        return $response;
    }
}
