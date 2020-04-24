<?php

namespace Parser;

use Config\Config;
use DiDom\Document;
use DiDom\Query;

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
                (Config::get('usingXPATH') == 1)
                    ? $data[] = $page->find($scratch, Query::TYPE_XPATH) : $data[] = $page->find($scratch);
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
        if (Config::get('writeLogs') == -1) $this->writelogs($links, 'links');
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

    public function close()
    {
        $this->client->close();
    }

}
