<?php
/**
 * Sanitation Controller Unit Tests
 * @package tests/HRQLS/Controllers
 */
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use HRQLS\Controllers\Food\Sanitaton;

/**
 * Defines Sanitation Controllers Unit Tests
 */
final class SanitationTest extends PHPUnit_Framework_TestCase
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
    private $requestMock;

    /**
     * Test that the main controller correctly attempts to render the base page.
     *
     * @return void
     */
    public function testMainController()
    {
        $this->appMock = $this->getMockBuilder('Silex\Application')
            ->setMethods(['register'])
            ->getMock();
    }
}
