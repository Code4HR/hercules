<?php
/**
 * Controller for Crime Endpoints.
 *
 * @package HRQLS/Controllers
 */
 
namespace HRQLS\Controllers\Crime;

/**
 * Defines controller for crime endpoints.
 */
final class Crime
{
    /**
     * This is the main entry point for Crime endpoints.
     *
     * @param Request     $req The request to be handled.
     * @param Application $app The silex application that will handle the request.
     *
     * @return array A list of all the Crime endpoints under /crime.
     */
    public function main(Request $req, Application $app)
    {
        return ['This endpoint is really set up yet. Check back later...but not to soon.'];
    }
}
