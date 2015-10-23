<?php

/*
 * Scrapes data from Pilot Online's Crime RSS Feed
 */

 namespace Utils;

 $ChesapeakeScraper = new Scraper('http://hamptonroads.com/newsdata/crime/chesapeake/search/rss?me=%2Fchesapeake%2Fsearch&type=&near=&radius=&op=Submit&form_token=9dc84572393ad9c68f54cad6549692f3&form_id=crime_searchform', './data/Chesapeake_Crime.json');
 $NpnScraper = new Scraper('http://hamptonroads.com/newsdata/crime/newport-news/search/rss?me=%2Fnewport-news%2Fsearch&type=&near=&radius=&op=Submit&form_token=9dc84572393ad9c68f54cad6549692f3&form_id=crime_searchform', './data/NewportNews_Crime.json');
 $NorfolkScraper = new Scraper('http://hamptonroads.com/newsdata/crime/norfolk/search/rss?me=%2Fnorfolk%2Fsearch&type=&near=&radius=&op=Submit&form_token=9dc84572393ad9c68f54cad6549692f3&form_id=crime_searchform', './data/Norfolk_Crime.json');
 $PortsmouthScraper = new Scraper('http://hamptonroads.com/newsdata/crime/portsmouth/search/rss?me=%2Fportsmouth%2Fsearch&type=&near=&radius=&op=Submit&form_token=9dc84572393ad9c68f54cad6549692f3&form_id=crime_searchform', './data/Portsmouth_Crime.json');
 $SuffolkScraper = new Scraper('http://hamptonroads.com/newsdata/crime/suffolk/search?me=%2Fsuffolk%2Fsearch&type=&near=&radius=&op=Submit&form_token=9dc84572393ad9c68f54cad6549692f3&form_id=crime_searchform', './data/Suffolk_Crime.json');
 $VaBeachScraper = new Scraper('http://hamptonroads.com/newsdata/crime/virginia-beach/search/rss?me=/virginia-beach/search&type=&near=&radius=&op=Submit&form_token=9dc84572393ad9c68f54cad6549692f3&form_id=crime_searchform', './data/VABeach_Crime.json');

 //$ChesapeakeScraper->scrape();
 //$NpnScraper->scrape();
 //$NorfolkScraper->scrape();
 //$PortsmouthScraper->scrape();
 //$SuffolkScraper->scrape();
 $VaBeachScraper->scrape();

 class Scraper
 {
     const PER_PAGE = 35;

     private $source;
     private $destination;

     /**
      * Constructs a Scraper
      *
      * @param $url the URL you are scaping data from
      * @param $destination
      */
     public function __construct($url, $destination)
     {
         $this->source = $url;
         $this->destination = $destination;
     }

     public function scrape()
     {
         header('Content-Type: application/json');
         $json = array();

         $currPage = 0;
         $prevPage = 0;
         do {
            $retVal = $this->scrapeCrime($currPage);
            array_push($json, $retVal);
            $prevPage = $currPage;
            $currPage++;
         }
         while ($currPage !== 2);//count($json[$prevPage])%35 === 0);

         file_put_contents($this->destination, json_encode($json));
     }

     public function scrapeCrime($page = 0)
     {
         $url = $this->source . '&page=' . $page;
         $feed = new \DOMDocument();
         $feed->load($url);
         $jsonArray = array();

         $items = $feed->getElementsByTagName('channel')->item(0)->getElementsByTagName('item');
         foreach($items as $item) {
             $json = array();
             $json['title'] = $item->getElementsByTagName('title')->item(0)->firstChild->nodeValue;
             $json['description'] = $item->getElementsByTagName('description')->item(0)->firstChild->nodeValue;
             $json['link'] = $item->getElementsByTagName('link')->item(0)->firstChild->nodeValue;
             $json['pubdate'] = $item->getElementsByTagName('pubdate')->item(0)->firstChild->nodeValue;
             $location = $item->getElementsByTagName('loc')->item(0);
             $json['longitude'] = $location->getElementsByTagName('lon')->item(0)->firstChild->nodeValue;
             $json['latitude'] = $location->getElementsByTagName('lat')->item(0)->firstChild->nodeValue;
             array_push($jsonArray, $json);
         }

         return $jsonArray;
     }

 }
