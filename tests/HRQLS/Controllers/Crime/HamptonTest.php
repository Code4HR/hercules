<?php
/**
 * Test file for City of Hampton Crime endpoint.
 *
 * @package tests/HRQLS/Controllers
 */
use HRQLS\Bootstrap;
use Silex\Application;
use HRQLS\Controllers\Crime\Hampton;
use HRQLS\Controllers\Crime\DataPoint;
use HRQLS\Models\GuzzleServiceProvider;
use HRQLS\Models\ElasticSearchServiceProvider;

/**
 * Defines Hampton Crime Controller unit tests.
 */
class HamptonTest extends PHPUnit_Framework_TestCase
{
    /**
     * Straight up honest this is just a placeholder so phpcs stops complaining.
     *
     * @return void
     */
    public function test_basicUse()
    {
        return;
    }
}
