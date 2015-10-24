<?php
namespace HRQLS\Controllers;

use Silex\Application as Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Utils\ZillowClient;

class CoL
{
    public function main(Request $req, Application $app)
    {
        $zClient = new ZillowClient();
        $results = $zClient->getDataForAllZips();

        return new Response($results, 200);
    } 
}
