<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use HRQLS\Controllers\Main;

class MainTest extends PHPUnit_Framework_TestCase
{
    /**
     * The mock object for the silex application instance.
     *
     * @var Application
     */
    private $appMock;

    /**
     * Mock object for the request object.
     *
     * @var Request
     */
    private $reqMock;

    /**
     * Test that the main controller correctly attempts to render the base page.
     *
     * @return void
     */
    public function testMainController()
    {
        // Create silex mock
        $this->appMock = $this->getMockBuilder('Silex\Application')
            ->setMethods(['register'])
            ->getMock();

        $twigServiceMock = $this->getMockBuilder('Silex\Provider\TwigServiceProvider')
            ->setMethods(['render'])
            ->getMock();

        $this->appMock['twig'] = $twigServiceMock;

        $this->reqMock = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $mainController = new HRQLS\Controllers\Main();

        $twigServiceMock->expects($this->once())
            ->method('render')
            ->with(
                $this->stringContains('mainPage.twig'),
                $this->anything()
            );

        $mainController->main($this->reqMock, $this->appMock);
    }
}
