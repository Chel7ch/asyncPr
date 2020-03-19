<?php

namespace Parser;

class SpiderGroup extends ParserGroupPage
{
    public function spider($urls, $scratches = [], $links = [])
    {
        if (empty($links)) {
            $linked = array();

            $links = $this->getLinks($urls, $scratches);
            $linked = array_merge($linked, $urls);
        }

        for ($i = 1; $i <= LEVELS; $i++) {
            $sub = array();
            while ($links) {
                $urls = array_splice($links, 0, MULTI_REQUEST);
                $subLinks = $this->getLinks($urls, $scratches);

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


            echo '<pre>';
            echo '<br>links<br>';
            print_r($links);
            echo '<br>linked<br>';
            print_r($linked);
            echo '</pre>';
        }
    }