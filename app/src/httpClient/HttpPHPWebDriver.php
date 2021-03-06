<?php

namespace Client;

use Config\Config;
use Proxy\CookingProxy;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Exception\WebDriverException;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;

/** HTTP client */
class HttpPHPWebDriver implements IHttpClient
{
    use LogErrorResponse, SaveHTMLPage;

    public $driver;
    public $url;
    public $scratch;
    public static $goodWork;

    /**
     * Initiate the browser
     *  browser_type
     *  :firefox => firefox
     *  :chrome  => chrome
     *  :ie      => microsoftEdge     *
     */
    public function getStarted()
    {
        $browserType = Config::get('browserType');

        if (Config::get('proxyOn') == 0) $proxy = '';
        elseif (Config::get('proxyOn') == 1 && !empty(Config::get('workProxy'))) {
            $proxy = join(Config::get('workProxy'));
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
                $options->addArguments(['--user-data-dir=' . CHROME_PROFILE,]);
            }
            $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);

        } elseif ($browserType == 'firefox') {
//            $capabilities->setCapability('moz:firefoxOptions', ['args' => ['-headless']]);
        }
        $this->driver = RemoteWebDriver::create($host, $capabilities);
    }

    /**
     * @param string $page
     * @param string $proxy
     * @return string
     */
    public function getPage($page, $proxy = '')
    {
        $source = '';

        if (self::$goodWork == 0) $this->getStarted();
        self::$goodWork++;

        try {
            $this->driver->get($page);
            $source = $this->driver->getPageSource();

            if (Config::get('proxyOn') == 1) {
                $resp = $this->errorRespZero($source);
                if ($resp) $this->errorResponse('0', $page);
            }

        } catch (WebDriverException $e) {
            self::$goodWork = 0;
            CookingProxy::replace(Config::get('workProxy'));
            $this->close();
            echo '..............catch<br>';//!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        }
        $this->saveHTMLPage($source, $page);

        return $source;
    }

    /**
     * changing bsd proxy
     * @param string $content
     * @return string
     */
    public function errorRespZero($content)
    {
        $response = '';
        if (!$content or strpos($content, 'ERR_PROXY_CONNECTION_FAILED') or
            strpos($content, 'ERR_PROXY_CONNECTION_FAILED') or
            strpos($content, 'ERR_TIMED_OUT') or
            strpos($content, 'ERR_CONNECTION_TIMED_OUT') or
            strpos($content, 'ERR_CONNECTION_CLOSED') or
            strpos($content, 'ERR_EMPTY_RESPONSE')) {

            CookingProxy::replace(Config::get('workProxy'));
            self::$goodWork = 0;
            $this->close();
            echo '..............errorRespZero<br>';//!!!!!!!!!!!!
            print_r(Config::get('workProxy')) ;echo '         __________workProxy';
            $response = 0;
        }
        return $response;
    }

    /**
     * @param array $urls
     * @return array
     */
    public function getGroupPages($urls)
    {
        $content = array();

        foreach ($urls as $url) {
            $content[] = $this->getPage($url);
        }

        return $content;
    }

    /**
     * @param string $page
     * @param array $postData
     * @return string
     */
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

    /** close browser */
    public function close()
    {
        $this->driver->close();
        $this->driver->quit();
    }
}