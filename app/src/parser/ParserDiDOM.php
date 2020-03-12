<?php

namespace Parser;


use DiDom\Document;
use DiDom\Query;

abstract class ParserDiDOM extends PrepareOutput
{

    /**plug-in traits */
    use CleanLinks;

    /**
     * @var array scratch  XML expressions for searching on a page. Needs of benefits
     * @var object client HTTP client
     * @var object connect PDO
     * @var array linked reviewed links
     * @var string doc DiDom page
     */
    public $client;
    public $connect;
    public $linked;
    public $doc;

    public function __construct()
    {
        $this->doc = new Document();
    }

    /**
     * @param \Client\IHttpClient $client
     */
    public function setHttpClient(\Client\IHttpClient $client)
    {
        $this->client = $client;
        $this->url = $this->client->url;
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
        $document = $this->getPage($url);
        if (!empty($document)) {
            $this->page = $this->doc->loadHtml($document);
        }
        return $this;
    }

    /**
     * @param array $urls
     * @return object ParserDiDOM
     */
    public function parsMultiPage($urls)
    {
        $docs = $this->client->getMultiPages($urls);

        foreach ($docs as $key => $doc) {
            $this->page[$key] = new Document($doc);
        }

        return $this;
    }

    public function getMultiLinks()
    {
        $links = array();
        foreach ($this->page as $pg) {
            $links = array_merge($links, $pg->find('a::attr(href)'));
        }
        $links = $this->cleanLinks($links);

        return $links;
    }

    /**
     * pulls links from page
     * @return array
     * @uses CleanLinks trait
     */
    public function getLinks()
    {
        $links = array();
        if (!empty($this->page)) {
            $links = $this->page->find('a::attr(href)');
            $links = $this->cleanLinks($links);
        }
        return $links;
    }

    /**
     * pulls  benefits  data from page
     * @param string $page URN
     * @param array $scratches DiDom find expressions
     * @return array
     */
    public function multibenefit($pages, $scratches = [])
    {
        $dt = array();

        foreach ($this->page as $key => $pg) {

            $data[] = $pages[$key];
            foreach ($scratches as $scratch) {
                (USING_XPATH == 1) ? $data[] = $pg->find($scratch, Query::TYPE_XPATH) : $data[] = $pg->find($scratch);
            }
            $dt[] = $data;
            unset($data);
        }

        return $dt;
    }

    public function multiPrepOutput($data)
    {
        if (PREPARE_BENEFIT == 1) $data = $this->multiTurnOverOutput($data);
        elseif (PREPARE_BENEFIT == 0) $data = $this->straightOutput($data);
        if (PREP_QUERY_FOR_DB == 1) $data = $this->prepInsertDB($data, 'proxy');

        return $data;
    }

    public function benefit($page, $scratches = [])
    {
        $data[] = $page;
        foreach ($scratches as $scratch) {
            (USING_XPATH == 1) ? $data[] = $this->page->find($scratch, Query::TYPE_XPATH) : $data[] = $this->page->find($scratch);
        }
        return $data;
    }

    /**
     * prepare data for output
     * @param array $data
     * @return array
     */
    public function prepOutput($data)
    {
        if (PREPARE_BENEFIT == 1) $data = $this->turnOverOutput($data);
        elseif (PREPARE_BENEFIT == 0) $data = $this->straightOutput($data);
        if (PREP_QUERY_FOR_DB == 1) $data = $this->prepInsertDB($data, 'proxy');

        return $data;
    }

    /** ConnectDB  */
    public function connectDB($db)
    {
        $this->conn = $db;
    }

    /** InsertDB  */
    public function insertDB($benefit)
    {
        $this->conn->insertDB($benefit);
    }

    /** SelectDB  */
    public function selectDB()
    {
        $this->conn->selectDB();
    }

    /** Clean table  */
    public function cleanTable($nameTable)
    {
        $this->conn->cleanTable($nameTable);
    }

}