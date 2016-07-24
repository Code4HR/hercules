<?php
/**
 * Controller for Norfolk School Endpoint.
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
 * Class defining the Norfolk School endpoint controller.
 */
final class Norfolk
{
    /**
     * The Main entry point for Norfolk School endpoint.
     *
     * @param Request     $req The request being handled by this endpoint.
     * @param Application $app The Silex application handling this request.
     *
     * @return array An array of schools that are in Norfolk, VA.
     */
    public function main(Request $req, Application $app)
    {
        $requestUrl = SchoolUtils::formatRequestUrl('Norfolk');
        $response = $app['guzzle']->get($requestUrl, []);
        
        $schools = SchoolUtils::convertToJson($response->getBody());
        $schools = SchoolUtils::filterResultsByCity($schools, 'Norfolk');
        
        $herculesResponse = new HerculesResponse('/schools/norfolk', 200, $schools);
        
        // The frontend expects a JSONP format, to do this the response must be wrapped in a callback.
        return $_GET['callback'] . '(' . $herculesResponse->to_json() . ')';
    }
}
