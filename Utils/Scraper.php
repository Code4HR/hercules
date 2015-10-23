<?php

/*
 * Scrapes data from Pilot Online's Crime RSS Feed
 */

 namespace Utils;

 public class Scraper
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
