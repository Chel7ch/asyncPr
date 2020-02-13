<?php

namespace Client;

use DiDom\Document;
use Facebook\WebDriver\Exception\WebDriverException;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;

class HttpPHPWebDriver implements IHttpClient
{
    use ErrResp, SaveHTMLPage;
    /**
     * @var RemoteWebDriver
     */
    public $driver;
    public $url;
    public $scratch;

    public function __construct($url, $scratch, $browserType)
    {
        $this->url = $url;
        $this->scratch = $scratch;//!!!!!!!!!!!!!!!!!!!!!
        # browser_type
        # :firefox => firefox
        # :chrome  => chrome
        # :ie      => iexplore
        $host = 'http://localhost:4444/wd/hub';
//        $this->driver = RemoteWebDriver::create($host, DesiredCapabilities::$browserType());

        $desiredCapabilities = DesiredCapabilities::$browserType();
        $desiredCapabilities->setCapability('acceptSslCerts', false);
//        $desiredCapabilities->setCapability(FirefoxDriver::PROFILE, PROFILE); //?????
        $this->driver = RemoteWebDriver::create($host, $desiredCapabilities);
    }

    public function getPage($page)
    {
        try {
            $this->driver->get($page);

            $cookie = new \Facebook\WebDriver\Cookie('cookie_name', 'cookie_value');
            $this->driver->manage()->addCookie($cookie);
            $cookies = $this->driver->manage()->getCookies();
            print_r($cookies);
            print_r(get_headers($page));

            $this->errResp(http_response_code(), $page);
            $element = $this->driver->findElement(WebDriverBy::tagName('*'));
            $content = $element->getAttribute('outerHTML');


        } catch (WebDriverException $e) {
        }

        if (isset($content)) {
            $document = new Document($content);
            $links = $document->find('a::attr(href)');

            $this->SaveHTMLPage($document ,$page);

//            $this->benefit($page, $document, $scratch);
        } else $links[] = $this->url;

        return $links;

////////////////////////////////////////////////
//        $driver->wait(5, 1000)->until(
//            WebDriverExpectedCondition::titleContains('Курс программирования:')
//        );

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