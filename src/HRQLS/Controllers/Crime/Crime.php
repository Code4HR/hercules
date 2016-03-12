<?php
/**
 * Base Controller for Crime API Endpoints
 *
 * @package HRQLS/Controllers
 */
namespace HRQLS\Controllers\Crime;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Base Crime endpoint for API
 */
final class Crime
{
    /**
     * The Crime Endpoint Configuration settings.
     *
     * @var array
     */
    private $config;
    
    /**
     * Indexes the available Crime endpoints available
     *
     * @param Request     $req      The Request to be handled.
     * @param Application $hercules The Silex Application handling the request.
     *
     * @return array like [
     *   city => [
     *      'authentication' => (string),
     *      'dataSource': (string),
     *      'description' => (string),
     *      'url' => (string)
     *    ],...
     * ];
     */
    public function main(Request $req, Application $hercules)
    {
        $config = $this->getConfig();
        $routes;
        foreach ($config["routes"]["get"] as $endPoint) {
            $routes[] = [
                "{$endPoint['city']}" => [
                    'authenticaton' => $endPoint['authenticaton'],
                    'dataSource' => $endPoint['dataSource'],
                    'description' => $endPoint['description'],
                    'url' => $endPoint['url'],
                ]
            ];
        }
        return json_encode($routes);
    }
    
    /**
     * Indexes all the crime for all of the Cities available under the crime endpoint
     *
     * @param Request     $req      Current request to be handeld.
     * @param Application $hercules Silex application to handle request.
     *
     * @return array like [
     * @TODO determine standard for returning data
     * ];
     */
    public function getAll(Request $req, Application $hercules)
    {
        //connect to ES and dump all data.
         return [];
    }
    
    /**
     * Returns the Crime API configuration settings.
     *
     * @return array like [
     *    'get' => [
     *       {
     *          'url' => (string),
     *          'controller'=> (string),
     *          'city' =>  (string),
     *          'description' => (string),
     *          'dataSource' => (string),
     *          'authentication' => (string)
     *       }
     *     ],...
     *   ];
     */
    private function getConfig()
    {
        return json_decode(file_get_contents(__DIR__ . 'config.json'));
    }
}
