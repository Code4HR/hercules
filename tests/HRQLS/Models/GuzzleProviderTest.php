<?php
/**
 * Test file for GuzzleHttp\Client Service Provider.
 *
 * @package tests/HRQLS/Models
 */
use Silex\Application;
use HRQLS\Models\GuzzleProvider;
use GuzzleHttp\Client;

/**
 * Defines GuzzleHttp Client Service Provider Unit Tests
 */
class GuzzleProviderTest extends PHPUnit_Framework_TestCase
{
    /**
     * Gets necessary Mock Objects for unit tests
     *
     * @return array like [
     *    'guzzle' => GuzzleHttp\Client,
     * ];
     */
    public function getMocks()
    {
        return [
            'guzzle' => $this->getMockBuilder('GuzzleHttp\Client')->disableOriginalConstructor()->getMock(),
        ];
    }
     
    /**
     * Verifies the behavior of the GuzzleProvider constructor
     *
     * @return void
     */
    public function testConstructor()
    {
        $mocks = $this->getMocks();
        $guzzleProvider = new GuzzleProvider($mocks['guzzle']);
         
        $this->assertInstanceOf('HRQLS\Models\GuzzleProvider', $guzzleProvider);
    }
}
