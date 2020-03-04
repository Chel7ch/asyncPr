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

}