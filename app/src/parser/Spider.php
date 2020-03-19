<?php

namespace Parser;

class Spider extends ParserPage
{
    public function spider($url, $scratches)
    {
        if (empty($links)) {
            $linked = array();

            $links = $this->getLinks($url, $scratches);
        }

        for ($i = 1; $i <= LEVELS; $i++) {
            foreach ($links as $nextLink) {
                $subLinks = $this->getLinks($nextLink, $scratches);

                echo 'уровень  ' . $i . '  _  ' . count($linked) . '  в linked  ' . count($links) . '  в $links <br>';

                $subLinks = array_diff($subLinks, $links, $linked);
                $links = array_merge($links, $subLinks);

                $linked[] = $nextLink;
                array_shift($links);
                usleep(USLEEP);
            }
        }

// todo заставить работать правильно ( сделать if !empty)
        for ($e = 0; $e < REPEAT_ERR_URL; $e++) {
            $this->readErrorURL();
            sleep(REPEAT_ERR_URL_DELAY);
        }


        echo '<pre>';
        echo '<br>links<br>';
        print_r($links);
        echo '<br>linked<br>';
        print_r($linked);
        echo '</pre>';
    }


}
