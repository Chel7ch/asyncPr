<?php

namespace Client;

use Facebook\WebDriver\Exception\WebDriverException;
use Facebook\WebDriver\WebDriverBy;

class ProxyPHPWebDriver extends HttpPHPWebDriver implements IHttpClient
{

    public function getPage($page)
    {
        $content = '';
        static $d = 0;
        try {

            if ($d < 3 ) {
                $this->driver->get($page);
                sleep(10);
                $d++;

            } else{
                $this->driver->get($page);
                sleep(rand(3, 7));
            }

            $element = $this->driver->findElement(WebDriverBy::tagName('*'));
            $content = $element->getAttribute('outerHTML');


        } catch (WebDriverException $e) {
        }

        $this->saveHTMLPage($content, $page);

        return $content;
    }

}