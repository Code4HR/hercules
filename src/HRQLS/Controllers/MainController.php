<?php
namespace HRQLS\Controllers;

use Silex\Application as Application;
use Symfony\Component\HttpFoundation\Request as Request;
use Symfony\Component\HttpFoundation\Response as Response;

class Main
{
    public function main(Request $req, Application $app)
    {
        return $app['twig']->render('homepage.twig', array(
            'title' => 'Hercules'
        ));
    } 
}
