<?php

include __DIR__ . '/../../vendor/autoload.php';

try{
$consumer = new CrimeConsumer('VA Beach', 'http://hamptonroads.com/newsdata/crime/virginia-beach/search/rss?type=&near=&radius=&from%5Bmonth%5D=10&from%5Bday%5D=1&from%5Byear%5D=2015&to%5Bmonth%5D=10&to%5Bday%5D=23&to%5Byear%5D=2015&op=Submit&form_id=crime_searchform');
$consumer->consume();
}
catch (Exception $e)
{
    echo 'Failed VA Beach';
}
try{
$consumer = new CrimeConsumer('Norfolk', 'http://hamptonroads.com/newsdata/crime/norfolk/search/rss?me=%2Fnorfolk%2Fsearch&type=&near=&radius=&op=Submit&form_token=9dc84572393ad9c68f54cad6549692f3&form_id=crime_searchform');
$consumer->consume();
}
catch(Exception $e)
{
        echo 'Failed Norfolk';
}
try{
$consumer = new CrimeConsumer('Portsmouth', 'http://hamptonroads.com/newsdata/crime/portsmouth/search/rss?type=&near=&radius=&from%5Bmonth%5D=10&from%5Bday%5D=1&from%5Byear%5D=2015&to%5Bmonth%5D=10&to%5Bday%5D=23&to%5Byear%5D=2015&op=Submit&form_id=crime_searchform');
$consumer->consume();
}
catch (Exception $e)
{
    echo 'Failed Portsmouth';
}
try{
$consumer = new CrimeConsumer('Suffolk', 'http://hamptonroads.com/newsdata/crime/suffolk/search/rss?type=&near=&radius=&from%5Bmonth%5D=10&from%5Bday%5D=1&from%5Byear%5D=2015&to%5Bmonth%5D=10&to%5Bday%5D=23&to%5Byear%5D=2015&op=Submit&form_id=crime_searchform');
$consumer->consume();
}
catch (Exception $E)
{
    echo 'Failed Suffolk';
}

