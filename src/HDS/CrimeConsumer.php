<?php
/**
*[
*    [
*        {
*            "title": "Attempted robbery (October 21, 2015)",
*            "description": "2700 block of North Mall Drive",
*            "link": "http://hamptonroads.com/newsdata/crime/virginia-beach/detail/1984111",
*            "pubdate": "Thu, 22 Oct 2015 09:00:17 -0400",
*            "longitude": "-76.0670637",
*            "latitude": "36.8206927"
*        }
*    ]
*]
*/

class CrimeConsumer{

    public $scraper;
    public $city;

    public function __construct($city, $url);
    {
        $this->scraper = new Scraper($url);
        $this->city = $city;
    }

    public function consume()
    {
        header('Content-Type: application/json');
        $json = array();

        $currPage = 0;
        $prevPage = 0;
        //do {
           $json = $this->scraper->scrapeCrime($currPage);
           $this->insertToElasticSearch($json);
           $prevPage = $currPage;
           $currPage++;
        //}
        //while (count($json[$prevPage])%35 === 0);


        //$jsonToInsert = $this->scraper->scrape();
    }

    private function insertIntoElasticSearch($json)
    {

    }
}
