<?php

namespace Parser;

use DiDom\Document;
use DiDom\Query;

class ParserGroupPage
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
    public $data = array();

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
     * @param array $urls
     * @return array  HTML pages
     */
    public function getPages($urls)
    {
        return $this->client->getGroupPages($urls);

    }

    /**
     * @param array $urls
     * @return array
     */
    protected function parsPages($urls)
    {
        $docs = $this->getPages($urls);
        foreach ($docs as $key => $doc) {
            $pages[$key] = new Document($doc);
        }
        return $pages;
    }

    /**
     * pulls links from page
     * @param array $urls
     * @param array $scratches
     * @return array
     */
    protected function parsLinks($urls, $scratches = [])
    {
        $pages = $this->parsPages($urls);

        if (!empty($pages)) {
            foreach ($pages as $key => $page) {
                $link = $page->find('a::attr(href)');

                $link = $this->filter->cleanLinks($link);
                $this->links = array_merge($this->links, $link);
                $this->links = array_values(array_unique($this->links));

                $data[] = $urls[$key];
                foreach ($scratches as $scratch) {
                    (USING_XPATH == 1) ? $data[] = $page->find($scratch, Query::TYPE_XPATH) : $data[] = $page->find($scratch);
                    $data = $this->output->prepOutput($data);
                    $this->data = array_merge($this->data, $data);
                }
                unset($data);
            }
            if (PREP_QUERY_FOR_DB == 1) {
                $this->query = $this->output->prepInsert($this->data);
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
        $this->conn->insertDB($data);
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
