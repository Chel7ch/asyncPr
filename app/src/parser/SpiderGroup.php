<?php

namespace Parser;

use Config\Config;
use Proxy\CookingProxy;

class SpiderGroup extends ParserGroupPage
{
    use WriteLogs,\Client\LogErrorResponse;

    public function spider($urls, $scratches = [], $links = [], $linked = [])
    {
        $linked = $linked ?? array();

        if (empty($links)) {
            $links =$this->firstPage($urls, $scratches = []);
            $linked = array_merge($linked, $urls);
        }
        self::$step++;
        $this->proxyOn();

        for ($i = 1; $i <= Config::get('levels'); $i++) {
            list($links, $linked) = $this->parsOneLevel($links, $linked, $scratches);
            if (Config::get('proxyOn') == 1) list($links, $linked) = $this->forceReadErrResponse($links, $linked, $scratches,Config::get('zeroErrRespFile'));
        }

        list($links, $linked) = $this->forceReadErrResponse($links, $linked, $scratches, Config::get('errRespFile'));

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
     * @param array $urls
     * @param array $scratches
     * @return array
     */
    public function firstPage($urls, $scratches = [])
    {
        $this->proxyOn();
        $links = $this->getLinks($urls, $scratches);

        while (empty($links)) {
            $a = join(self::$workProxy);
            $this->replaceProxy($a);
            $links = $this->getLinks($urls, $scratches);
        }
        return $links;
    }

    public function parsOneLevel($links, $linked, $scratches)
    {
        $sub = array();
        while ($links) {

            $this->multiRequest();
            $urls = array_splice($links, 0, self::$multiRequest);
            $subLinks = $this->getLinks($urls, $scratches);

            print_r(self::$multiRequest);echo '___________self::$multiRequest++++++++++++++<br>';//!!!!
            echo 'уровень  ' . '  _  ' . count($linked) . '  в linked  ' . count($links) . '  в $links <br>';

            $linked = array_merge($linked, $urls);
            $sub = array_unique(array_merge($sub, $subLinks));
            $sub = array_diff($sub, $links, $linked);
            usleep(Config::get('usleep'));
        }

        return array($sub, $linked);
    }

    public function forceReadErrResponse($links, $linked, $scratches,$fname)
    {

        for ($i = 1; $i <= Config::get('forceReadErrResponseUrl'); $i++) {
            $errorURL = $this->readErrorURL($fname);
            if (empty($errorURL)){
                return array($links, $linked);
            }
            else {
                $linked = array_diff($linked, $errorURL);

                list($ln, $linked) = $this->parsOneLevel($errorURL, $linked, $scratches);
                $links = array_merge($links, $ln);
            }
        }
        return array($links, $linked);
    }
}