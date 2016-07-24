<?php
/**
 * Controller for School Endpoint.
 *
 * @package HRQLS/Controllers
 */
 
namespace HRQLS\Controllers\Schools;

use Silex\Application;
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
     * @return array An array of school datapoints for Hampton, VABeach, Norfolk, Portsmouth, Chesapeake, and Suffolk.
     */
    public function main(Request $req, Application $app)
    {
        return json_encode([
            'get' => [
                '/hampton',
                '/norfolk',
                '/vabeach',
                '/suffolk',
                '/portsmouth',
                '/chesapeake',
            ],
        ]);
    }
}
