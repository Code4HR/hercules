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
            $url = SchoolUtils::formatRequestUrl($requestedCity);
            $response = $app['guzzle']->get($url, []);
            
            $schools = SchoolUtils::convertToJson($response->getBody());
            $resultSet[] = SchoolUtils::filterResultsByCity($schools, $requestedCity);
        }
        
        $herculesResponse = new HerculesResponse('/schools', 200, $resultSet);
        
        // The frontend expects a JSONP format, to do this the response must be wrapped in a callback.
        return $_GET['callback'] . '(' . $herculesResponse->to_json() . ')';
    }
}
