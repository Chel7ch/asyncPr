<?php

namespace Parser;

use Config\Config;
use Proxy\CookingProxy;

class SpiderGroup extends ParserGroupPage
{
    use WriteLogs, \Client\LogErrorResponse;

    /**
     * Bypass all pages
     * @param array $urls
     * @param array $scratches
     * @param array $links
     * @param array $linked
     */
    public function spider($urls, $scratches = [], $links = [], $linked = [])
    {
        $linked = $linked ?? array();

        if (empty($links)) {
            $this->proxyOn();
            $links = $this->firstPage($urls, $scratches = []);
            $linked = array_merge($linked, $urls);
        }

        for ($i = 1; $i <= Config::get('levels'); $i++) {

            $this->proxyOn();

            list($links, $linked) = $this->parsOneLevel($links, $linked, $scratches);
            if (Config::get('proxyOn') == 1)
                list($links, $linked) = $this->forceReadErrResponse($links, $linked, $scratches, Config::get('zeroErrRespFile'));
        }

        list($links, $linked) = $this->forceReadErrResponse($links, $linked, $scratches, Config::get('errRespFile'));

        if (Config::get('writeLogs') == 1) {
            $this->writelogs($links, 'links');
            $this->writelogs($linked, 'linked');
        }

        require_once("../app/serviceFile.php");

    }

    /**
     * Get links from first page
     * @param array $urls
     * @param array $scratches
     * @return array
     */
    public function firstPage($urls, $scratches = [])
    {
        $links = $this->getLinks($urls, $scratches);

        while (empty($links)) {
            $this->replaceProxy(join(Config::get('workProxy')));
            $links = $this->getLinks($urls, $scratches);
        }
        return $links;
    }

    /**
     * Bypass all links at one level
     * @param array $links
     * @param array $linked
     * @param array $scratches
     * @return array
     */
    public function parsOneLevel($links, $linked, $scratches)
    {
        $sub = array();
        while ($links) {
            $urls = array_splice($links, 0, Config::get('multiRequest'));
            $subLinks = $this->getLinks($urls, $scratches);

            print_r(Config::get('multiRequest'));
            echo '___________self::$multiRequest++++++++++++++<br>';//!!!!
            echo 'уровень  ' . '  _  ' . count($linked) . '  в linked  ' . count($links) . '  в $links <br>';

            $linked = array_merge($linked, $urls);
            $sub = array_unique(array_merge($sub, $subLinks));
            $sub = array_diff($sub, $links, $linked);
            usleep(Config::get('usleep'));
        }

        return array($sub, $linked);
    }

    /**
     * Bypass all missing links from errFile
     * @param array $links
     * @param array $linked
     * @param array $scratches
     * @param string $fname
     * @return array
     */
    public function forceReadErrResponse($links, $linked, $scratches, $fname)
    {
        for ($i = 1; $i <= Config::get('forceReadErrResponseUrl'); $i++) {
            $errorURL = $this->readErrorURL($fname);
            if (empty($errorURL)) {
                return array($links, $linked);
            } else {
                $linked = array_diff($linked, $errorURL);

                list($ln, $linked) = $this->parsOneLevel($errorURL, $linked, $scratches);
                $links = array_merge($links, $ln);
            }
        }
        return array($links, $linked);
    }

    /**
     * Get list good proxy
     */
    public function proxyOn()
    {
        static $multireq;

        if (Config::get('proxyOn') == 1) {
            CookingProxy::getList();
            $listDB = $this->selectDB('SELECT  field1 FROM  check_proxy');
            CookingProxy::$listPr = array_values(array_unique(array_merge(CookingProxy::$listPr, $listDB)));
            if (CookingProxy::$firstPage > 0) {
                CookingProxy::cooking($multireq);
            } else {
                $multireq = Config::get('multiRequest');
                CookingProxy::cooking(1);
                CookingProxy::$firstPage++;
            }
//            self::$workProxy = array_fill(0, self::$multiRequest, '');
        }
    }

    /**
     * Replace bad proxy
     * @param string $badProxy
     */
    public function replaceProxy($badProxy)
    {
        if (Config::get('proxyOn') == 1) CookingProxy::replace($badProxy);
    }

}