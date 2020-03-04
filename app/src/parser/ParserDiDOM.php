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
     * @return string $doc  get page by DiDOM
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