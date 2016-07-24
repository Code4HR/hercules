<?php
/**
 * Controller for Virginia Beach School Endpoint.
 *
 * @package HRQLS/Controllers
 */
 
namespace HRQLS\Controllers\Schools;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use HRQLS\Models\HerculesResponse;
use HRQLS\Controllers\Schools\SchoolUtils;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class defining the Virginia Beach School endpoint controller.
 */
final class VABeach
{
    /**
     * The Main entry point for Virginia Beach School endpoint.
     *
     * @param Request     $req The request being handled by this endpoint.
     * @param Application $app The Silex application handling this request.
     *
     * @return array An array of schools that are in Chesapeake, VA.
     */
    public function main(Request $req, Application $app)
    {
        $requestUrl = SchoolUtils::formatRequestUrl('Virginia Beach');
        $response = $app['guzzle']->get($requestUrl, []);
        
        $schools = SchoolUtils::convertToJson($response->getBody());
        $schools = SchoolUtils::filterResultsByCity($schools, 'Virginia Beach');
        
        $herculesResponse = new HerculesResponse('/schools/vabeach', 200, $schools);
        
        // The frontend expects a JSONP format, to do this the response must be wrapped in a callback.
        return $_GET['callback'] . '(' . $herculesResponse->to_json() . ')';
    }
}
