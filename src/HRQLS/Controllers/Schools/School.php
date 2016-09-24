<?php
/**
 * Controller for School Endpoint.
 *
 * @package HRQLS/Controllers
 */
 
namespace HRQLS\Controllers\Schools;

use Silex\Application;
use HRQLS\Models\HerculesResponse;
use HRQLS\Exceptions\InvalidFieldException;
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
     * @return array An array of schools in Hampton, VABeach, Norfolk, Portsmouth, Chesapeake, and Suffolk.
     */
    public function main(Request $req, Application $app)
    {
        $requestedCities = ['Hampton', 'Norfolk', 'Virginia Beach', 'Portsmouth', 'Chesapeake', 'Suffolk'];
        
        if ($req->query->has('cities')) {
            $requestedCities = explode(',', $req->query->get('cities'));
        }

        $groupByField = $req->query->get('groupBy');
        $averageField = $req->query->get('averageField');

        $resultSet = [];
        foreach ($requestedCities as $requestedCity) {
            $url = self::formatRequestUrl($requestedCity);
            $response = $app['guzzle']->get($url, []);
            
            $schools = self::convertToJson($response->getBody());
                
            $resultSet[] = self::filterResultsByCity($schools, $requestedCity);
        }
        
        if (!empty($averageField) && !empty($groupByField)) {
            $resultSet = self::calculateAverages($resultSet, $averageField, $groupByField);
        }
        
        $herculesResponse = new HerculesResponse('/schools', 200, $resultSet);
        
        // The frontend expects a JSONP format, to do this the response must be wrapped in a callback.
        return $_GET['callback'] . '(' . $herculesResponse->to_json() . ')';
    }
    
    /**
     * Retrieves a single school record with the specified id.
     *
     * @param Request     $req The request to be handled.
     * @param Application $app The Silex application used to handle the request.
     * @param integer     $id  The Id of the school record to return.
     *
     * @return array An array containing the details about the school with the specified id.
     */
    public function get(Request $req, Application $app, $id)
    {
        //@TODO Krishna will finish implementing this.
        return json_encode([ 'endpoint' => '/schools/{id}', 'id' => $id ]);
    }
    
    /**
     * Gets the ApiKey for the Great Schools API.
     *
     * @return string
     */
    private function getApiKey()
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
    private function formatRequestUrl($search)
    {
        return 'http://api.greatschools.org/search/schools?key=' . self::getApiKey() . '&state=VA&q=' . $search;
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

    /**
     * Calculates the averages for the resultSet as specified by the field and groupBy parameters.
     *
     * @param array  $results The result set to calculate averages for.
     * @param string $field   The field to calculate an average of.
     * @param string $groupBy The field to group averages together on.
     *
     * @return array
     */
    private function calculateAverages(array $results, $field = 'rating', $groupBy = 'city')
    {
        $totalResults = [];
        foreach ($results as $citySchools) {
            $totalResults[] = self::getCityAverage($citySchools, $field, $groupBy);
        }
        
        return $totalResults;
    }

    /**
     * Calculates the average for a single city as specified by the field and groupBy parameters.
     *
     * @param array  $schools The data to use in average calculation.
     * @param string $field   The field to calculte an average for.
     * @param string $groupBy The field to group average by.
     *
     * @return array
     *
     * @throws InvalidFieldException When $field or $groupBy does not exist as a key foreach record in $schools.
     */
    private function getCityAverage(array $schools, $field, $groupBy)
    {
        if (!in_array($field, [ 'parentRating', 'gsRating'])) {
            throw new InvalidFieldException("{$field} is not a valid field name.");
        }
            
        if (!in_array($groupBy, [ 'city', 'enrollment' ])) {
            throw new InvalidFieldException("{$groupBy} is not a valid field name.");
        }
        
        $totalResults = [];
        foreach ($schools as $school) {
            if (!array_key_exists($field, $school) || !array_key_exists($groupBy, $school)) {
                continue;
            }
            
            $key = $school[$groupBy];
            self::addToTotals($totalResults, $key, $school[$field]);
        }
    
        $finalResults = [];
        foreach ($totalResults as $key => $result) {
            $finalResults[$key] = $result['fieldCount'] / $result['numRecords'];
        }
    
        return $finalResults;
    }
    
    /**
     * adds the current records count to the total count.
     *
     * @param array   $totals The current array of totals calculated.
     * @param string  $key    The groupBy field being used as the key for an associative array.
     * @param integer $value  The value add to the current addToTotals.
     *
     * @return void
     */
    private function addToTotals(array &$totals, $key, $value)
    {
        if (!array_key_exists($key, $totals)) {
            $totals[$key] = [
                'numRecords' => 1,
                'fieldCount' => $value,
            ];
        
            return;
        }

        $totals[$key]['fieldCount'] += $value;
        $totals[$key]['numRecords'] += 1;
    }
}
