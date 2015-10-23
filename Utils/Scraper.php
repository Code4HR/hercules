<?php

/*
 * Scrapes data from Pilot Online's Crime RSS Feed
 */

 namespace Utils;

 class Scraper
 {
     const PER_PAGE = 35;

     private $source;

     /**
      * Constructs a Scraper
      *
      * @param $url the URL you are scaping data from
      */
     public function __construct($url)
     {
         $this->source = $url;
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

     public function scrapeCrime($page = 0, $city)
     {
         $url = $this->source . '&page=' . $page;
         $feed = new \DOMDocument();
         $feed->load($url);
         $jsonArray = array();

         $items = $feed->getElementsByTagName('channel')->item(0)->getElementsByTagName('item');
         foreach($items as $item) {
             $json = array();
             $json['title'] = $item->getElementsByTagName('title')->item(0)->firstChild->nodeValue;
             $location = $item->getElementsByTagName('loc')->item(0);
             $json['location']['lat'] = $location->getElementsByTagName('lat')->item(0)->firstChild->nodeValue;
             $json['location']['lon'] = $location->getElementsByTagName('lon')->item(0)->firstChild->nodeValue;
             $json['link'] = $item->getElementsByTagName('link')->item(0)->firstChild->nodeValue;
             $json['date_occured'] = (new \DateTime($item->getElementsByTagName('pubdate')->item(0)->firstChild->nodeValue))->format('Y-m-d');
             $json['severity'] = '5';
             $json['city'] = $city;
             array_push($jsonArray, $json);
         }

         return $jsonArray;
     }

 }
