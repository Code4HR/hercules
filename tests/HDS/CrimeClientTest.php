<?php

use Silex\Application;
use HDS\CrimeClient;
use Guzzle\Http\ClientInterface;

/**
 * Unit Tests for CrimConsumer
 */
class CrimeClientTest extends PHPUnit_Framework_TestCase{

     public function testConsume()
     {
         $bootstrap = new HRQLS\Bootstrap(new Silex\Application());
         $cityUrl = 'http://hamptonroads.com/newsdata/crime/{city}/search/rss?from[month]={fromMonth}&from[day]={fromDay}&from[year]={fromYear}&to[month]={toMonth}&to[day]={toDay}&to[year]={toYear}&op=Submit&form_id=crime_searchform';

         $toDate = new \DateTime();//Gets todays date
         list($toYear, $toMonth, $toDay) = explode('-', $toDate->format('Y-m-d'));

         $fromDate = new \DateTime('-7 days');//Gets date of 7 days prior from today.
         list($fromYear, $fromMonth, $fromDay) = explode('-', $fromDate->format('Y-m-d'));

         //substitutes ending date range into crimeUrl
         $cityUrl = str_replace('{toYear}', $toYear, $cityUrl);
         $cityUrl = str_replace('{toMonth}', $toMonth, $cityUrl);
         $cityUrl = str_replace('{toDay}', $toDay, $cityUrl);

         //substitutes beginning of date range into crimeUrl
         $cityUrl = str_replace('{fromYear}', $fromYear, $cityUrl);
         $cityUrl = str_replace('{fromMonth}', $fromMonth, $cityUrl);
         $cityUrl = str_replace('{fromDay}', $fromDay, $cityUrl);


         $guzzleMock = $this->_getGuzzleClientMock($cityUrl);

         $crimeClient = new CrimeClient($guzzleMock);

         $crimeClient->consume('testCity', $cityUrl);
     }

     private function _getGuzzleClientMock($url)
     {
         $response = $this->getMock('\Guzzle\Http\Message\Response', ['body'], [200]);
         $response->expects($this->any())->method('getStatusCode')->with($this->equalTo(true))->will($this->returnValue(200));
         $response->expects($this->any())->method('getBody')->with($this->equalTo(true))->will(
             $this->returnValue('
<channel>
    <title>crime test</title>
    <description></description>
    <item>
      <title>Missing person (December 12, 2015)</title>
      <link>http://hamptonroads.com/newsdata/crime/virginia-beach/detail/2026481</link>
      <description>1300 block of Brant Road</description>
      <pubdate>Sun, 13 Dec 2015 09:00:55 -0500</pubdate>
      <guid>http://hamptonroads.com/newsdata/crime/virginia-beach/detail/2026481</guid>
      <loc>
         <lon>-75.9924078</lon>
         <lat>36.834414</lat>
      </loc>
    </item>
</channel>'));

        $request = $this->getMock('\Guzzle\Http\Message\Request', ['send'], ['GET', 'n/a']);//['get'], [$url]);
        $request->expects($this->any())->method('send')->will($this->returnValue($response));

        $client = $this->getMock('\Guzzle\Http\Client', ['get']);
        $client->expects($this->any())->method('get')->will($this->returnValue($request));

        return $client;
    }
 }
