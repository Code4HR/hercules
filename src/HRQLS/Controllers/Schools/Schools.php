<?php
/**
 * Controller for Schools Endpoint.
 *
 * @package HRQLS\Schools
 */
 
namespace HRQLS\Controllers;

use Silex\Application;

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
        return [
            'get' => [
                '/hampton',
                '/norfolk',
                '/vabeach',
                '/suffolk',
                '/portsmouth',
                '/chesapeake',
            ],
        ];
    }
}
