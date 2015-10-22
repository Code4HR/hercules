<?php
header('Content-Type: application/json');
$feed = new DOMDocument();
$feed->load('');
$json = array();

$json['title'] = $feed->getElementsByTagName('channel')->item(0)->getElementsByTagName('title')->item(0)->firstChild->nodeValue;
$json['description'] = $feed->getElementsByTagName('channel')->item(0)->getElementsByTagName('description')->item(0)->firstChild->nodeValue;
$json['link'] = $feed->getElementsByTagName('channel')->item(0)->getElementsByTagName('link')->item(0)->firstChild->nodeValue;

$items = $feed->getElementsByTagName('channel')->item(0)->getElementsByTagName('item');

$json['item'] = array();
$i = 0;


foreach($items as $item) {

   $title = $item->getElementsByTagName('title')->item(0)->firstChild->nodeValue;
   $description = $item->getElementsByTagName('description')->item(0)->firstChild->nodeValue;
   $purchaseurl = $item->getElementsByTagName('purchaseurl')->item(0)->firstChild->nodeValue;
   $standardimage = $item->getElementsByTagName('standardimage')->item(0)->firstChild->nodeValue;
   $shipping =      $item->getElementsByTagName('shipping')->item(0)->firstChild->nodeValue;
   $price =         $item->getElementsByTagName('price')->item(0)->firstChild->nodeValue;
   $condition  =    $item->getElementsByTagName('condition')->item(0)->firstChild->nodeValue;
   $guid = $item->getElementsByTagName('guid')->item(0)->firstChild->nodeValue;


   $json['item'][$i++]['title'] = $title;
   $json['item'][$i++]['description'] = $description;
   $json['item'][$i++]['purchaseurl'] = $purchaseurl;
   $json['item'][$i++]['image'] = $standardimage;
   $json['item'][$i++]['shipping'] = $shipping;
   $json['item'][$i++]['price'] = $price;
   $json['item'][$i++]['type'] = $condition;
   $json['item'][$i++]['guid'] = $guid;  

}


echo json_encode($json);
?>
