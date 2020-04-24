<?php

namespace Client;

use Config\Config;
use Proxy\CookingProxy;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Exception\WebDriverException;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\WebDriverCapabilityType;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;


class HttpPHPWebDriver implements IHttpClient
{
    use LogErrorResponse, SaveHTMLPage;

    public $driver;
    public $url;
    public $scratch;

//    public function __construct()
//    {
//        $this->getStarted();
//    }
    /**
     *  browser_type
     *  :firefox => firefox
     *  :chrome  => chrome
     *  :ie      => microsoftEdge     *
     */
    private function getStarted()
    {
        $browserType = Config::get('browserType');

        if (Config::get('proxyOn') == 0) $proxy = '';
        elseif (Config::get('proxyOn') == 1 && !empty(Config::get('workProxy'))) {
            $proxy = join(Config::get('workProxy'));
            print_r($proxy);
            echo 'getStarted';
        } else Exit('WebDriver: proxy not found');

        $host = 'http://localhost:4444/wd/hub';
        $capabilities = DesiredCapabilities::$browserType();
        $capabilities->setCapability('acceptSslCerts', false);

        if ($browserType == 'chrome') {
            $options = new ChromeOptions();
            $options->addArguments(['--window-size=571,500',]);
//        $options->addArguments(["--headless"]);
            if (Config::get('proxyOn') == 1) {

                $options->addArguments(['--proxy-server=http://' . $proxy,]);
            }
            $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);

        } elseif ($browserType == 'firefox') {
//            $capabilities->setCapability('moz:firefoxOptions',['args' => ['-headless']]);
        }
        $this->driver = RemoteWebDriver::create($host, $capabilities);
    }

    public function getPage($page, $proxy = '')
    {
        static $goodWork = 0;
        $source = '';
        if ($goodWork == 0) $this->getStarted();
        $goodWork++;

        try {
            $this->driver->get($page);

            $source = $this->driver->getPageSource();
            $this->source =$source;

        } catch (WebDriverException $e) {
            $goodWork = 0;
//            $page = strstr($page, 'javascript:', true);

            CookingProxy::replace(Config::get('workProxy'));
//            $this->close();
            print_r('.............catch<br>');
//            if (Config::get('proxyOn') == 1)
//                $this->errorRespZero($this->source, $page, $proxy);
//            else $this->errorResponse('999', $page, $proxy);

            echo '<br>' . $proxy . '  --------------$proxy____________<br>';
        }
//        $this->saveHTMLPage($source, $page);

        return $source;
    }

    public function errorRespZero($content, $page, $proxy)
    {
        if (strpos($content, 'ERR_PROXY_CONNECTION_FAILED') or
            strpos($content, 'ERR_PROXY_CONNECTION_FAILED') or
            strpos($content, 'ERR_EMPTY_RESPONSE')) {
            sleep(8);
//            $this->close();
//            $this->getStarted();
            CookingProxy::replace(Config::get('workProxy'));
            echo '4444444444444444444444444444444444444444444444';
//            $this->getPage($page);
        }
        CookingProxy::replace(Config::get('workProxy'));
    }

    public function getGroupPages($urls, $proxy = '')
    {
        $content = array();

        foreach ($urls as $url) {
            $content[] = $this->getPage($url);
        }

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