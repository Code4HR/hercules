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
         /*header('Content-Type: application/json');
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

         file_put_contents($this->destination, json_encode($json));*/
     }

     public function scrapeCrime($page = 0, $city)
     {
         $url = $this->source . '&page=' . $page;

         $response = json_encode(simplexml_load_string(file_get_contents($url)));
         $data = json_decode($response, true);

         $jsonArray = [];
         $json = [
             'title' => null,
             'location' => [
                 'lat' => null,
                 'lon' => null,
             ],
             'link' => null,
             'date_occured' => New \DateTime(),
             'severity' => 0,
             'city' => null,
         ];

         $items = $data['channel'];

         foreach($items['item'] as $item)
         {
            $title;
            $dateOccured;
             if(isset($item['title'])){
                 $start = $end = 0;
                 $start = strpos($item['title'], '(');
                 $end = strpos($item['title'], ')');
                 $title = trim(substr(' ' . $item['title'], 0, $start));
                 $dateOccured = \DateTime::CreateFromFormat('M d, Y', trim(substr($item['title'], $start+1, $end-1)));
                 $json['title'] = $title;
                 $json['date_occured'] = $dateOccured;
                 $json['severity'] = $this->calcSeverity($title);
             }

             if (isset($item['loc'])) {
                $json['location']['lon'] = $item['loc']['lon'];
                $json['location']['lat'] = $item['loc']['lat'];
             }

             if(isset($item['link'])) {
                 $json['link'] = $item['link'];
             }

             $json['city'] = $city;
             //print_r($json['city']);
             array_push($jsonArray, $json);
         }
         print_r($jsonArray);
         return $jsonArray;
     }

     private function calcSeverity($crime)
     {
         switch($crime)
        {
            case 'Rape':
            case 'Sexual battery':
            case 'Bomb threat':
            case 'Statutory rape/ carnal knowledge':
               return 10;
            case 'Death investigation':
            case 'Attempted robbery':
            case 'Robbery':
            case 'Aggravated assault':
               return 9;
           case 'Assault, simple, domestic':
           case 'Simple assault':
           case 'Arson':
           case 'Attempted arson':
               return 8;
           case 'Weapons offense':
           case 'Pornography/ obscene material':
           case 'Vehicle theft':
           case 'Child abuse':
               return 7;
           case 'Destruction of property':
           case 'Attempted destruction of property':
           case 'Molesting':
           case 'Tampering with auto':
           case 'Forcible indecent liberties':
           case 'Indecent exposure':
               return 6;
           case 'Hit and run':
           case 'Threaten bodily harm':
           case 'Violation of protection order':
           case 'Extortion':
           case 'Attempted family offense, nonviolent, child abuse':
           case 'Child neglect':
           case 'Abduction/kidnapping':
           case 'Impersonating a police officer':
           case 'Attempted vehicle theft':
               return 5;
               //Misdemeanors
           case 'Dui':
           case 'Drug offense':
           case 'Fraud':
           case 'Attempted fraud':
           case 'Larceny':
           case 'Attempted suicide':
           case 'Attempted burglary':
           case 'Burglary':
           case 'Attempted larceny':
           case 'Attempted extortion':
           case 'Forgery':
           case 'Suicide attempt':
           case 'Overdose':
           case 'Suicide':
           case 'Child endangerment':
           case 'Stalking':
           case 'Attempted shoplifting':
           case 'Concealment/ price changing':
           case 'Attempted concealment/price changing':
           case 'Obstructing justice':
           case 'Liquor law violations':
           case 'Attempted counterfeiting/ forgery, all others':
               return 4;
           case 'Throwing object at moving vehicle':
           case 'Counterfeiting/forgery':
           case 'Unauthorized use of vehicle':
           case 'Cruelty to animals':
           case 'Embezzlement':
           case 'Contributing to the delinquency of a minor':
           case 'Peeping':
               return 3;
           case 'Missing person':
           case 'Immoral conduct':
           case 'Providing false information to police':
           case 'Trespassing':
           case 'Disturbing the peace':
           case 'Attempted trespass':
           case 'Disorderly conduct':
               return 2;
           case 'Attempted all other reportable offenses':
           case 'All other reportable offenses':
           case 'Annoying phone calls':
           case 'Runaway':
           case 'Cursing/ obscene language':
           case 'Obscene phone calls':
               return 1;
           default:
               return 0;
        }/**/
     }
 }
