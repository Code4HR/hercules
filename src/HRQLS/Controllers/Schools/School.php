<?php
/**
 * Controller for School Endpoint.
 *
 * @package HRQLS/Controllers
 */
 
namespace HRQLS\Controllers\Schools;

use Silex\Application;
use HRQLS\Models\HerculesResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class defining the Schools endpoint controller.
 */
final class School
{
    /**
     * The base url for School API we are sourcing data from.
     *
     * @var string
     */
    public $baseUrl = 'http://api.greatschools.org/search/schools';
    
    /**
     * Main point of entry for the Schools endpoint.
     *
     * @param Request     $req The request to be handled.
     * @param Application $app The Silex Application used to handle the request.
     *
     * @return array An array of datapoints describing schools in Hampton, VABeach, Norfolk, Portsmouth, Chesapeake, and Suffolk.
     */
    public function main(Request $req, Application $app)
    {
        $requestedCities = ['Hampton', 'Norfolk', 'Virginia Beach', 'Portsmouth', 'Chesapeake', 'Suffolk'];
        
        if ($req->query->has('cities')) {
            $requestedCities = explode(',', $req->query->get('cities'));
        }
        
        $resultSet = [];
        foreach ($requestedCities as $requestedCity) {
            $url = self::formatRequestUrl($requestedCity);
            $response = $app['guzzle']->get($url, []);
            
            $schools = self::convertToJson($response->getBody());
            $resultSet[] = self::filterResultsByCity($schools, $requestedCity);
        }
        
        $herculesResponse = new HerculesResponse('/schools', 200, $resultSet);
        
        // The frontend expects a JSONP format, to do this the response must be wrapped in a callback.
        return $_GET['callback'] . '(' . $herculesResponse->to_json() . ')';
    }
    
    /**
     * Gets the ApiKey for the Great Schools API.
     *
     * @return string
     */
    public function getApiKey()
    {
        return getenv('GREATSCHOOLS_API_KEY');
    }
    
    /**
     * formats the Request URL with the specified search term.
     *
     * @param string $search The search term to use in the request.
     *
     * @return string
     */
    public function formatRequestUrl($search)
    {
        return 'http://api.greatschools.org/search/schools?key=' . self::getApiKey() . '&state=VA&q=' . $search . '&sort=alpha';
    }
    
    /**
     * Converts a well formatted XML string to an associative array.
     *
     * @param string $data An XML String that can be converted into convertToJson.
     *
     * @return array An associative array.
     */
    public function convertToJson($data)
    {
        $obj = simplexml_load_string($data);
        $jsonData = json_encode($obj);
        
        return json_decode($jsonData, true);
    }
    
    /**
     * The Great Schools API that we are using as a source only allows us to query keywords.
     * As a result we may get a Norfolk school from the Chesapeake endpoint if the School is on Chesapeake Blvd.
     * To prevent the crossover we iterate over each data entry and remove it if the city is not what was expected.
     *
     * @param array  $data An array of data points to be filtered by city.
     * @param string $city The city to filter results by.
     *
     * @return array An array of data entries that match the city specified as the filter.
     */
    public function filterResultsByCity(array $data, $city)
    {
        $filteredData = [];
        foreach ($data['school'] as $entry) {
            if (strtoupper($entry['city']) === strtoupper($city)) {
                $filteredData[] = $entry;
            }
        }
        
        return $filteredData;
    }
}
