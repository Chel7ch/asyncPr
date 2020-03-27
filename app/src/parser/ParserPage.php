<?php

namespace Parser;

use DiDom\Document;
use DiDom\Query;

class ParserPage
{

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
    public $links = array();

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

    public function setCleanLinks(\FilterLinks\ICleanLinks $cleanLinks)
    {
        $this->filter = $cleanLinks;
    }

    public function setOutput(\Prepeare\IPrepeareOutput $output)
    {
        $this->output = $output;
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
        $page ='';
        $document = $this->getPage($url);
        if (!empty($document)) {
            $page = $this->doc->loadHtml($document);
        }
        return $page;
    }

    /**
     * pulls links from page
     * @return array
     * @uses CleanLinks trait
     */
    public function parsLinks($url, $scratches = [])
    {
        $page = $this->parsPage($url);

        if (!empty($page)) {
            $this->links = $page->find('a::attr(href)');

            $this->links = $this->filter->cleanLinks($this->links);

            $data[] = $url;
            foreach ($scratches as $scratch) {
                (USING_XPATH == 1) ? $data[] = $page->find($scratch, Query::TYPE_XPATH) : $data[] = $page->find($scratch);
            }

            $this->data = $this->output->prepOutput($data);

            if (PREP_QUERY_FOR_DB == 1) {

                $this->query = $this->output->prepInsert($this->data);

                if (empty($this->query)) return $this->links;
                if (CONNECT_DB == 1) $this->insertDB($this->query);
            }
        }
        return $this->links;
    }

    public function getLinks($url, $scratches)
    {
        return $this->parsLinks($url, $scratches);
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

    /** ConnectDB  */
    public function connectDB($db)
    {
        $this->conn = $db;
    }

    /** InsertDB  */
    public function insertDB($data)
    {
        $this->conn->execInsert($data);
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

    public function close()
    {
        $this->client->close();
    }

}
