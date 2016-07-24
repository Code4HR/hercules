<?php
/**
 * File for abstract school controller. All other School Controllers will inherit from this one.
 *
 * @package HRQLS/Controllers
 */
namespace HRQLS\Controllers\Schools;

use Silex\Application;

/**
 * Defines abstract base class for all School Controllers
 */
final class SchoolUtils
{ 
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
    public static function formatRequestUrl($search)
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
    public static function convertToJson($data)
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
    public static function filterResultsByCity(array $data, $city)
    {
        $filteredData = [];
        foreach ($data as $entry) {
            if ($data['city'] === $city) {
                $filteredData[] = $entry;
            }
        }
        
        return $filteredData;
    }
}
