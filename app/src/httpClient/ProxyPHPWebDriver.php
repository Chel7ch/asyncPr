<?php

namespace Client;

use Config\Config;
use Proxy\CookingProxy;
use Facebook\WebDriver\Exception\WebDriverException;


/** HTTP client  for  Cloudflare protected sites */

class ProxyPHPWebDriver extends HttpPHPWebDriver
{
    /**
     * @param string $page
     * @param string $proxy
     * @return string
     */
    public function getPage($page, $proxy = '')
    {
        $source = '';
        static $d = 0;

        if (self::$goodWork == 0) $this->getStarted();
        self::$goodWork++;

        try {

            if ($d < 3) {
                $this->driver->get($page);
                sleep(10);
                $d++;

            } else {
                $this->driver->get($page);
                sleep(rand(3, 7));
            }

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

}