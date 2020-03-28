<?php

namespace Parser;

use Proxy\CookingProxy;

class SpiderGroup extends ParserGroupPage
{
    use WriteLogs;

    public function spider($urls, $scratches = [], $links = [], $linked = [])
    {
        $linked = $linked ?? array();

        if (empty($links)) {
            $links =$this->firstPage($urls, $scratches = []);
            $linked = array_merge($linked, $urls);
        }
        self::$step++;
        $this->proxyOn();
        for ($i = 1; $i <= LEVELS; $i++) {
            $sub = array();
            while ($links) {
                
                $this->multiRequest();
                $urls = array_splice($links, 0, self::$multiRequest);
                $subLinks = $this->getLinks($urls, $scratches);

//                print_r(self::$workProxy);//!!!!!!!!!!!!!!!!!!!!!!!!!
//                echo '__________workProxy+++++++++++++++++++++++<br>';//!!!!!!!!!!!!!!!!!!!!!!!!!
                print_r(self::$multiRequest);//!!!!!!!!!!!!!!!!!!!!!!!!!
                echo '___________self::$multiRequest++++++++++++++<br>';//!!!!!!!!!!!!!!!!!!!!!!!!!
//                print_r(CookingProxy::$workProxy);//!!!!!!!!!!!!!!!!!!!!!!!!!
//                echo '__________CookingProxy::workProxy+++++++++++++++++++++++<br>';//!!!!!!!!!!!!!!!!!!!!!!!!!
                echo 'уровень  ' . $i . '  _  ' . count($linked) . '  в linked  ' . count($links) . '  в $links <br>';

                $linked = array_merge($linked, $urls);
                $sub = array_unique(array_merge($sub, $subLinks));
                $sub = array_diff($sub, $links, $linked);
                usleep(USLEEP);
            }
            $links = $sub;
            unset($sub);
        }

// todo заставить работать правильно ( сделать if !empty)
        for ($e = 0; $e < REPEAT_ERR_URL; $e++) {
            $this->readErrorURL();
            sleep(REPEAT_ERR_URL_DELAY);
        }

        $this->writelogs($links, 'links');
        $this->writelogs($linked, 'linked');

        echo '<pre>';
        echo '<br>links<br>';
        print_r($links);
        echo '<br>linked<br>';
        print_r($linked);
        echo '</pre>';
    }

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
}