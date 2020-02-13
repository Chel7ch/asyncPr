<?php

namespace Src;

use Client\IHttpClient;
use Parser\ParsDiDOM;

/**
 * Crawling site pages on links
 */
class Spider
{
    /**plug-in traits */
    use \Client\RepeatErrResp;

    /** @var string URL
     * @var array links getting for crawling
     * @var array linked used
     * @var object client of HTTP Client
     */
    public $url;
    public $links;
    public $linked;
    public $client;
    /**
     * @var ParsDiDOM
     */
    public $parser;


    /** Create folder project  */
    public function __construct()
    {
        if (!file_exists(PROJECT_DIR)) mkdir(PROJECT_DIR);


    }

//todo сделать статический $linked[] из за repeatErrorPage() !!!!!!!!

    /**
     * @param array $links required only if you want to pass repeatErrorPage
     * @return void
     */
    public function spider($links = [])
    {
        if (empty($links)) {
            $this->linked = array();
            $links = $this->getPage($this->url);
//            $links = $this->getLinks($this->getPage($this->url));
        }

        for ($i = 1; $i <= LEVELS; $i++) {
            foreach ($links as $nextLink) {
                $subLinks = $this->getPage($nextLink);
//                $subLinks = $this->getLinks($this->getPage($nextLink));
                if (!empty($subLinks)) {
                    foreach ($subLinks as $subLink) {
                        $ln[] = $subLink;
                            echo $subLink . '<br>';// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
                    }
                }

                echo 'уровень  ' . $i . '  _  ' . count($this->linked) . '  в linked  ' . count($links) . '  в $links <br>';


                $ln = array_diff($ln, $links, $this->linked);
                $links = array_merge($links, $ln);

                $this->linked[] = $nextLink;
                array_shift($links);
                usleep(USLEEP);
            }
        }

        for ($e = 0; $e < REPEAT_ERR_URL; $e++) {
            $this->repeatErrorPage();
            sleep(REPEAT_ERR_URL_DELAY);
        }


        echo '<pre>';
        echo '<br>links<br>';
        print_r($links);
        echo '<br>linked<br>';
        print_r($this->linked);
        echo '</pre>';
    }

//    public function Didom(ParsDiDOM $parser){
//        $this->parser = $parser;
////        echo 'aaa';
////        print_r($parser);
//    }
//
//    public function setHttpClient(\Client\HttpGuzzle $a,$url, $header){
//        $this->client = $a;
////        $client = $this->parser->setHttpClient(new Client\HttpGuzzle($url, $header));
////        echo 'aaa';
////        echo $url;
////        print_r($this->client);
//    }
//
//    /**
//     * re-passing pages with a response error >= 400
//     * @uses RepeatErrResp trait
//     */
//    public function repeatErrorPage()
//    {
//        $links = $this->readErrorURL();
//        if (!empty($links)) {
//            $this->spider($links);
//        }
//    }
//
//    /**
//     *  Implements dependency injection
//     * @param IHttpClient $client
//     */
//    public function setClient00(IHttpClient $client)
//    {
//        $this->client = $client;
//        $this->url = $this->client->url;
//    }
//
//    /**
//     * Implements dependency injection
//     * @param string $url
//     * @return mixed
//     */
//    public function getPage($url, $scratch, $header)
//    {
//        $parser =new ParsDiDOM($scratch); //11
//        $parser->setHttpClient(new \Client\HttpGuzzle($url, $header) );//11
//        $a =$parser->getLinks($parser->getPage($url)); // 11
//        print_r($a);
////        return $this->parser->getPage($url);
//    }
//
//    public function getLinks($url)
//    {
////        return $this->parser->getLinks($url);
//    }
//
//    public function benefit($page, $document, $scratches = [])
//    {
//        return $this->client->benefit($page, $document, $scratches = []);
//    }
//
//    /**
//     * Implements dependency injection
//     * @param string $page URN of page
//     * @param array $postData data pass with request
//     * @return mixed
//     */
//    public function postPage($page, $postData)
//    {
//        return $this->client->postPage($page, $postData);
//    }
//
//    public function close()
//    {
//        sleep(10);
//        $this->client->close();
//    }

}