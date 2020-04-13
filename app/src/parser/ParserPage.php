<?php

namespace Parser;

use Config\Config;
use DiDom\Document;
use DiDom\Query;
use Proxy\CookingProxy;

class ParserPage extends ParserRoutine
{
    use WriteLogs;

    /**
     * @var array scratch  XML expressions for searching on a page. Needs of benefits
     * @var object client HTTP client
     * @var object connect PDO
     * @var array linked reviewed links
     * @var string doc DiDom page
     */
    public $client;
    public $connect;
    public $doc;
    public $paternLinks;
    public $links = array();
    public static $workProxy;
    public static $step = 0;

    public function __construct()
    {
        $this->doc = new Document();
        $this->paternLinks = '//a/@href';
    }

    /**
     * @param string $url
     * @return string HTML doc
     */
    public function getPage($url)
    {
        return $this->client->getPage($url);
    }

    /**
     * @param string $url the URN pages
     * @return object ParserDiDOM
     */
    public function parsPage($url)
    {
        $page = '';
        $document = $this->getPage($url);
        if (!empty($document)) {
            $page = $this->doc->loadHtml($document);
        }
        return $page;
    }

    public function setPaternLinks($links = '//a/@href')
    {
        $this->paternLinks = $links;
    }

    /**
     * pulls links from page
     * @param $url
     * @param array $scratches
     * @return array
     */
    private function parsLinks($url, $scratches = [])
    {
        $page = $this->parsPage($url);

        if (!empty($page)) {
            $this->links = $page->find($this->paternLinks, Query::TYPE_XPATH);

            $this->links = $this->filter->cleanLinks($this->links);

            $data[] = $url;
            foreach ($scratches as $scratch) {
                (Config::get('usingXPATH') == 1) ? $data[] = $page->find($scratch, Query::TYPE_XPATH) : $data[] = $page->find($scratch);
            }
            $this->data = $this->output->prepOutput($data);
            $this->query = $this->output->prepInsert($this->data);

            if (!empty($this->data) and Config::get('connectDB') == 1) $this->insertDB($this->query);
            if (!empty($this->data) and Config::get('writeBenefitInFile') == 1) $this->writeBenefit($this->data);
        }
        return $this->links;
    }

    public function getLinks($url, $scratches)
    {
        $links = $this->parsLinks($url, $scratches);
        if(Config::get('writeLogs') == -1)$this->writelogs($links, 'links');
        return $links;
    }

    public function getBenefit($url, $scratches)
    {
        $this->parsLinks($url, $scratches);
        return $this->data;
    }

    public function getQuery($url, $scratches)
    {
        $this->parsLinks($url, $scratches);
        return $this->query;
    }

    public function proxyOn()
    {
        static $i = 0;
        if (Config::get('proxyOn') == 1) {
            $listProxy = $this->selectDB('SELECT  field1 FROM  check_proxy');
            if ($i == 0) {
                CookingProxy::cook($listProxy, 1);
                self::$workProxy = CookingProxy::$workProxy;
                CookingProxy::$firstPage = self::$step;
                $i++;
            } else {
                CookingProxy::$listP = $listProxy;
                CookingProxy::$firstPage = self::$step;
                CookingProxy::$attemptWork = 0;
            }
        }
    }

    public function replaceProxy($badProxy)
    {
        CookingProxy::replace($badProxy);
        self::$workProxy = CookingProxy::$workProxy;
    }

    public function close()
    {
        $this->client->close();
    }

}
