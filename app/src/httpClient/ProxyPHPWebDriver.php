<?php

namespace Client;

use Config\Config;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Exception\WebDriverException;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;

class ProxyPHPWebDriver extends HttpPHPWebDriver implements IHttpClient
{

    private function getStarted()
    {
        $browserType = Config::get('browserType');

        if (Config::get('proxyOn') == 0) $proxy = '';
        elseif (Config::get('proxyOn') == 1 && !empty(Config::get('workProxy'))) {
            $proxy = join(Config::get('workProxy'));
        } else Exit('WebDriver: proxy not found');

        $host = 'http://localhost:4444/wd/hub';
        $capabilities = DesiredCapabilities::$browserType();
        $capabilities->setCapability('acceptSslCerts', false);

        $this->driver = RemoteWebDriver::create($host, $capabilities);
    }

    public function getPage($page, $proxy = '')
    {
        $content = '';
        static $d = 0;

        if(!$this->driver)$this->getStarted();

        try {
            if ($d < 3) {
                $this->driver->get($page);
                sleep(10);
                $d++;

            } else {
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