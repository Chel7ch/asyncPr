<?php

namespace Parser;


class SpiderParserDiDOM extends ParserDiDOM
{
    /**plug-in traits */
    use RepeatErrResp;

    /**
     * Crawling site pages on links
     * @param array $scratch
     * @param array $links required only if you want to pass repeatErrorPage
     * @return void
     */
    public function spider($scratch = [], $links = [])
    {
        if (empty($links)) {
            $this->linked = array();
            $links = $this->parsPage($this->url)->getLinks();
            $b = $this->parsPage($this->url)->prepOutput($this->benefit($this->url, $scratch));
            print_r($b);//!!!!!!!!!!!
            echo "запрос<br>";//!!!!!!!!!!!!!!!!!
            if (!empty($b)) $this->insertDB($b);
        }

        for ($i = 1; $i <= LEVELS; $i++) {
            foreach ($links as $nextLink) {
                $subLinks = $this->parsPage($nextLink)->getLinks();
                $b = $this->parsPage($nextLink)->prepOutput($this->benefit($nextLink, $scratch));
                print_r($b);//!!!!!!!!!!
                echo "запрос<br>";//!!!!!!!!!!!!!!!!!!!!!
                if (!empty($b)) $this->insertDB($b);

                if (!empty($subLinks)) {
                    foreach ($subLinks as $subLink) {
                        $ln[] = $subLink;
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
// todo заставить работать правильно ( сделать if !empty)
        for ($e = 0; $e < REPEAT_ERR_URL; $e++) {
            $this->readErrorURL();
            sleep(REPEAT_ERR_URL_DELAY);
        }


        echo '<pre>';
        echo '<br>links<br>';
        print_r($links);
        echo '<br>linked<br>';
        print_r($this->linked);
        echo '</pre>';
    }

    public function multiSpider($urls, $scratch = [], $links = [])
    {
        if (empty($links)) {
            $linked = array();

            $links = $this->parsMultiPage($urls)->getMultiLinks();
            $b = $this->multiPrepOutput($this->multiBenefit($urls, $scratch));
            if (!empty($b)) $this->insertDB($b);
        }

        for ($i = 1; $i <= LEVELS; $i++) {
            $sub = array();
            while ($links) {
                $urls = array_splice($links, 0, MULTI_REQUEST);
                $subLinks = $this->parsMultiPage($urls)->getMultiLinks();
                $b = $this->multiPrepOutput($this->multiBenefit($urls, $scratch));
                if (!empty($b)) $this->insertDB($b);

                echo 'уровень  ' . $i . '  _  ' . count($linked) . '  в linked  ' . count($links) . '  в $links <br>';

                $linked = array_merge($linked, $urls);
                $sub = array_merge($sub, array_diff($subLinks, $links, $linked));
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