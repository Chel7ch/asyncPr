<?php

namespace Client;

use Facebook\WebDriver\Exception\WebDriverException;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;

class ProxyPHPWebDriver implements IHttpClient
{
    use ErrResp, SaveHTMLPage;
    /**
     * @var RemoteWebDriver
     */
    public $driver;
    public $url;
    public $scratch;

    public function __construct($url, $browserType)
    {
        $this->url = $url;
        # browser_type
        # :firefox => firefox
        # :chrome  => chrome
        # :ie      => iexplore
        $host = 'http://localhost:4444/wd/hub';
        $desiredCapabilities = DesiredCapabilities::$browserType();
        $desiredCapabilities->setCapability('acceptSslCerts', false);
        $this->driver = RemoteWebDriver::create($host, $desiredCapabilities);
    }

    public function getPage($page)
    {
        $content = '';
        static $d = 0;
        try {

            if ($d < 3 ) {
                $this->driver->get($page);
                sleep(10); //!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
                $d++;

            } else{
                $this->driver->get($page);
                sleep(rand(3, 7)); //!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
            }

            $this->errResp(http_response_code(), $page);
            $element = $this->driver->findElement(WebDriverBy::tagName('*'));
            $content = $element->getAttribute('outerHTML');


        } catch (WebDriverException $e) {
        }

        $this->saveHTMLPage($content, $page);

        return $content;
    }

    public function postPage($page, $postData)
    {
        $this->driver->get($page);
        sleep(5);
        $this->driver->findElement(WebDriverBy::id('sign_in'))->click();
        sleep(1);
        $this->driver->findElement(WebDriverBy::xpath('//*[@id="ips_username"]'))->sendKeys("wind");
        sleep(1);
        $this->driver->findElement(WebDriverBy::id("ips_password"))->sendKeys("140572");
        sleep(2);
        $this->driver->findElement(WebDriverBy::xpath('//*[@id="login"]/div/div/input'))->click();
        sleep(5);

        return $this->driver->getCurrentURL();

    }

    public function close()
    {
        $this->driver->close();
    }
}