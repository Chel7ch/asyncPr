<?php

namespace Parser;

use Proxy\CookingProxy;

class Spider extends ParserPage
{
    use WriteLogs, \Client\LogErrorResponse;

    public function spider($url, $scratches, $links = [], $linked = [])
    {
        $linked = $linked ?? array();

        if (empty($links)) {
            $links = $this->firstPage($url, $scratches);
        }
        self::$step++;
        $this->proxyOn();

        for ($i = 1; $i <= LEVELS; $i++) {
            list($links, $linked) = $this->parsOneLevel($links, $linked, $scratches);
            if (PROXY_ON == 1) list($links, $linked) = $this->forceReadErrResponse($links, $linked, $scratches,ZERO_ERR_RESP_FILE);
       echo $i.'========================================LEVELS';//!!!!!!!!!!!!!!!!!!!
        }
        list($links, $linked) = $this->forceReadErrResponse($links, $linked, $scratches, ERR_RESP_FILE);

        $this->writelogs($links, 'links');
        $this->writelogs($linked, 'linked');

        echo '<pre>';
        echo '<br>links<br>';
        print_r($links);
        echo '<br>linked<br>';
        print_r($linked);
        echo '</pre>';
    }

    /**
     * @param string $url
     * @param array $scratches
     * @return array
     */
    public function firstPage($url, $scratches = [])
    {
        $this->proxyOn();
        $links = $this->getLinks($url, $scratches);

        while (empty($links)) {
            $bad = join(self::$workProxy);
            $this->replaceProxy($bad);
            $links = $this->getLinks($url, $scratches);

            echo '________________________________firstPage___________________<br>';
            print_r($links);
            echo '__________$links<br>';//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
            print_r($bad); echo '__________$bad<br>';//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

        }

        return $links;
    }

    public function parsOneLevel($links, $linked, $scratches)
    {
        foreach ($links as $nextLink) {
            $subLinks = $this->getLinks($nextLink, $scratches);

            echo 'уровень  ' . '  _  ' . count($linked) . '  в linked  ' . count($links) . '  в $links <br>';

            $subLinks = array_diff($subLinks, $links, $linked);
            $links = array_merge($links, $subLinks);

            $linked[] = $nextLink;
            array_shift($links);
            usleep(USLEEP);


            echo '________________________________parsOneLevel___________________<br>';
//            print_r(self::$workProxy[$key]);
//            echo '__________self::$workProxy[$key]<br>';//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
//            print_r($new);
//            echo '__________$new<br>';//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
//            print_r(self::$listProxy);
//            echo '__________self::$listProxy';//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
////        print_r(self::$listP);
////        echo '__________$a';
//            print_r($badProxy);
//            echo '__________self::$badProxy';
//            echo '<br>' . self::$attemptWork . '__________self::$multiRequest';
        }
        return array($links, $linked);
    }

    public function forceReadErrResponse($links, $linked, $scratches,$fname)
    {
        echo $fname. '________________________________forceReadErrResponse enter___________________<br>';//!!!

        for ($i = 1; $i <= FORCE_READ_ERR_RESPONSE_URL; $i++) {
            $errorURL = $this->readErrorURL($fname);
            if (empty($errorURL)){
                echo '--------------!!!!!!!!!!!!!!!!!!!!!!!=-------------$errorURL<br>';//!!!!!!!
                return array($links, $linked);
            } 
            else {
                echo $i. '________________________________forceReadErrResponse___________________<br>';
                print_r($linked);  echo '__________$linked<br>';//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
                print_r($errorURL);  echo '__________$errorURL<br>';//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

                $linked = array_diff($linked, $errorURL);
                
                print_r($linked);  echo '__________$linked___array_diff<br>';//!!!!!!!
                
                list($ln, $linked) = $this->parsOneLevel($errorURL, $linked, $scratches);
                $links = array_merge($links, $ln);

                print_r($linked);  echo '__________$linked<br>';//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
                print_r($errorURL);  echo '__________$errorURL<br>';//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
            print_r($ln);  echo '__________print_r($ln)<br>';//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
            print_r($links); echo '__________$links';//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
            }
        }
        return array($links, $linked);
    }
}
