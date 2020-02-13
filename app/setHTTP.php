<?php

use DiDom\Query;

//$url = 'https://allnet.me';
//$url = 'http://razrabotkaweb.ru/';
//$url = 'http://books.toscrape.com/';
$url = 'http://bk55.ru/';
//$url = 'https://meduza.io/';
//$url = 'https://ria.ru/';
//$url = 'https://yandex.ru/';
//$url = 'https://xdan.ru/https-zapros-pri-pomoshi-curl-php.html';
//$url = 'https://market.csgo.com/';
//$url = 'https://ruseller.com//photoshop-master.ru';
//$url = 'https://ruseller.com/lesson.php';
//$url = 'https://ruseller.com';
//$url = 'http://diesel.elcat.kg/';
//$url = 'https://diesel.elcat.kg/index.php?app=core&amp;module=global&amp;section=login&amp;do=process';
//$url = 'https://diesel.elcat.kg/index.php?app=core&amp;module=global&amp;section=login&amp;do=process';
//$url = 'https://www.reddit.com/login';
//$url = 'razrabotkaweb.ru/sto/entry_id.php';
//$url = 'http://razrabotkaweb.ru/sto/testreg.php';
//$url = 'http://razrabotkaweb.ru';



$startPage = '';
$html = $url . $startPage;

$header = array(
'Connection: keep-alive',
'Cache-Control: max-age=0',
'Origin: https://diesel.elcat.kg',
'Upgrade-Insecure-Requests: 1',
'Content-Type: application/x-www-form-urlencoded',
'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36',
'Sec-Fetch-User: ?1',
'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
'Sec-Fetch-Site: cross-site',
'Sec-Fetch-Mode: navigate',
'Referer: http://diesel.elcat.kg/',
'Accept-Encoding: gzip, deflate, br',
'Accept-Language: ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7,bg;q=0.6',
//'X-Requested-With: XMLHttpRequest'
);
$agent = 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.117 Safari/537.36';

$scratch = array(
//    'img::attr(src)',
//    'a::attr(href)',
    'img::attr(src)',
//    'img::attr(src)'
);

//$postdata = array(
//    'cookie_domain' => '.reddit.com',
//    'dest' =>'https://www.reddit.com/settings',
//    'csrf_token' =>'c615cc5bc908c61f2ef8d0c1b8f5d8e1b4ac2b1c',
//    'is_oauth' =>'False',
//    'frontpage_signup_variant' =>'',
//    'ui_mode' =>'',
//    'username' =>'win7d',
//    'password' =>'mama1553',
//);

$postData = null;
//$postdata = 'login=qwe&password=qwe&submit=%D0%92%D0%BE%D0%B9%D1%82%D0%B8';
//$postdata = 'auth_key=880ea6a14ea49e853634fbdc5015a024&referer=http%3A%2F%2Fdiesel.elcat.kg%2F&ips_username=wind&ips_password=140572&rememberMe=1';

//$postdata = array(
//    'login' => 'qwe',
//    'password' =>'qwe'
//);
//$postdata = array(
//    'auth_key' =>'880ea6a14ea49e853634fbdc5015a024',
//    'referer' =>'http%3A%2F%2Fdiesel.elcat.kg%2F',
//    'ips_username' =>'wind',
//    'ips_password' =>'140572',
//    'irememberMe' =>'1',
//);

///////////////////////////////////////////////////////////////////////////////
//$document->find('img::attr(src)');
//$document->find('a::attr(href)');
//$document->find('img[src$=png]');  // все изображения с расширением png
//$document->find('a[href*=example.com]');  // все ссылки, содержащие в своем адресе строку "example.com"
//$document->find('a.foo::text');  // текст всех ссылок с классом "foo" (массив строк)
//$document->find('a.bar::attr(href|title)'); // адрес и текст подсказки всех полей с классом "bar"
//$document->find('//a/@href', Query::TYPE_XPATH);


//'form_params' => [
//    'login' => 'VasyaPupkin',
//    'password' => 'SuperPuperParol'
//]

//'on_stats'  =>  function  ( TransferStats  $stats ) {
//    echo $stats->getEffectiveUri() . "\n";
//    echo $stats->getTransferTime() . "\n";
//    var_dump($stats->getHandlerStats());
//    if ($stats->hasResponse()) {
//        echo '<pre>';
//        echo $stats->getResponse()->getStatusCode();
//    } else {
//        echo '<pre>';
//        var_dump($stats->getHandlerErrorData());
//    }
//}

//try  {
//    $acceptDoc = $this->client -> request('GET',$html);
//}  catch(RequestException $e ) {
//    echo '<pre>';
//    echo  Psr7\str( $e->getRequest());
//    if  ( $e->hasResponse()){
//        echo '<pre>';
//        echo  Psr7\str( $e->getResponse ());
//    }
//}

