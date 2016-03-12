<?php
/**
 * City of Hampton crime API endpoint.
 *
 * @package HRQLS\Controllers
 */
namespace HRQLS\Controllers\Crime;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Facade Endpoint to hit the Hampton City Crime API found at the url below.
 * https://dev.socrata.com/foundry/data.hampton.gov/umc3-tsey
 */
final class Hampton
{
    /**
     * indexes all crime data for the city of Hampton.
     *
     * @param Request     $req      Current request to be handled.
     * @param Application $hercules Silex application to handle request.
     *
     * @return array [
     * @TODO
     * ];
     */
    public function getAll(Request $req, Application $hercules)
    {
        return [];
    }
    
    /**
     * gets a specific crime in city of Hampton.
     *
     * @param Request     $req      Current request to be handled.
     * @param Application $hercules Silex application to handle request.
     *
     * @return array [
     * @TODO
     * ];
     */
    public function get(Request $req, Application $hercules)
    {
        return [];
    }
    
    /**
     * Updates data set if the datetime of the current request is >= the next refresh date
     *
     * @param DateTime $reqTimestamp The date and time of a specific request.
     *
     * @return void
     */
    private function updateStaleData(DateTime $reqTimestamp)
    {
        
    }
}
