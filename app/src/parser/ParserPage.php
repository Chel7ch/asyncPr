<?php

namespace Parser;

use DiDom\Document;
use DiDom\Query;
use Proxy\CookingProxy;

class ParserPage extends ParserRoutine
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
    public static $workProxy;
    public static $step = 0;

    public function __construct()
    {
        $this->doc = new Document();
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

    public function proxyOn()
    {
        static $i = 0;
        if (PROXY_ON == 1) {
            $listProxy = $this->selectDB('SELECT  field1 FROM  check_proxy');
            if ($i == 0) {
                CookingProxy::cook($listProxy, 1);
                self::$workProxy = CookingProxy::$workProxy;
                CookingProxy::$firstPage = self::$step;
                $i++;
            }else{
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
