<?php
/**
 * Controller for Chesapeake School Endpoint.
 *
 * @package HRQLS\Schools
 */
 
namespace HRQLS\Controllers;

use Silex\Application;
use HRQLS\Controllers\Schools\AbstractSchoolController;
use HRQLS\Controllers\Models\HerculesResponse;

/**
 * Class defining the Chesapeake School endpoint controller.
 */
final class Chesapeake extends SchoolController
{
    /**
     * The Main entry point for Chesapeake School endpoint.
     *
     * @param Request     $req The request being handled by this endpoint.
     * @param Application $app The Silex application handling this request.
     *
     * @return array An array of schools that are in Chesapeake, VA.
     */
    public function main(Request $req, Application $app)
    {
        $requestUrl = parent::formatRequestUrl('Chesapeake');
        $response = $app['guzzle']->get($requestUrl);
        
        $schools = parent::convertToJson($response->getBody());
        $schools = filterResultsByCity($schools, 'chesapeake');
        
        foreach ($schools as $school) {
            //TODO append each school to a HerculesResponse Object.
        }
        
        return [];
    }
}
