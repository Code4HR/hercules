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
        $esResult = $app['elasticsearch']->search($this->indices, [], []);

        $response = $this->parseResults($esResult, $response);

        // The frontend expects a JSONP format, to do this the response must be wrapped in a callback.
        return $_GET['callback'] . '('.$response->to_json().')';
    }
    
    /**
     * Retrieves a single crime record for the city of Hampton with the specified id.
     *
     * @param Request     $req The request to be handled.
     * @param Application $app The Silex Application used to handle the request.
     * @param Integer     $id  The id of the crime record to retrieve.
     *
     * @return array An array containing the details of a specific crime. 
     */
    public function get(Request $req, Application $app, $id)
    {
        return json_encode([ 'endpoint' => '/crime/Hampton/{id}', 'id' => $id ]);
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
        $resultArray = $results['hits'];
        foreach ($resultArray as $key => $value) {
            $id = $value['_id'];
            $offense = $value['_source']['offense'];
            $category = $value['_source']['category'];
            $class = $value['_source']['class'];
            $occured = new \DateTime($value['_source']['occured']);
            $city = $value['_source']['city'];
            $location = $value['_source']['location'];

            if (isset($occured) && gettype($location) === 'array') {
                $datapoint = new DataPoint($id, $offense, $occured, $city, $location, $category, $class);
                $response->addDataEntry($datapoint->toArray());
            }
        }

        return $response;
    }
}
