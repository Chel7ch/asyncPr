<?php

namespace Parser;

use Config\Config;
use Proxy\CookingProxy;

class Spider extends ParserPage
{
    use WriteLogs, \Client\LogErrorResponse;

    /**
     * Bypass all pages
     * @param string $url
     * @param array $scratches
     * @param array $links
     * @param array $linked
     */
    public function spider($url, $scratches, $links = [], $linked = [])
    {
        $linked = $linked ?? array();

        if (empty($links)) {
            $this->proxyOn();
            $links = $this->firstPage($url, $scratches);
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
     * @param string $url
     * @param array $scratches
     * @return array
     */
    public function firstPage($url, $scratches = [])
    {
        $links = $this->getLinks($url, $scratches);
        while (empty($links)) {
            $this->replaceProxy(join(Config::get('workProxy')));
            $links = $this->getLinks($url, $scratches);
            usleep(Config::get('usleep'));
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
        foreach ($links as $nextLink) {
            $subLinks = $this->getLinks($nextLink, $scratches);

            echo 'уровень  ' . '  _  ' . count($linked) . '  в linked  ' . count($links) . '  в $links <br>';

            $subLinks = array_diff($subLinks, $links, $linked);
            $links = array_merge($links, $subLinks);

            $linked[] = $nextLink;
            array_shift($links);
            usleep(Config::get('usleep'));
        }

        return array($links, $linked);
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

        if (Config::get('proxyOn') == 1) {
            CookingProxy::getList();
            $listDB = $this->selectDB('SELECT  field1 FROM  check_proxy');
            CookingProxy::$listPr = array_values(array_unique(array_merge(CookingProxy::$listPr, $listDB)));

            CookingProxy::cooking(1);
            CookingProxy::$firstPage++;
        }
    }

    /**
     * Replace bad proxy
     * @param string $badProxy
     */
    public function replaceProxy($badProxy)
    {
        CookingProxy::replace($badProxy);
    }

}
