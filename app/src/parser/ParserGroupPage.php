<?php

namespace Parser;

use Config\Config;
use DiDom\Document;
use DiDom\Query;

class ParserGroupPage extends ParserRoutine
{
    use WriteLogs;

    public $links = array();
    public $paternLinks;
    public $data = array();
    public $query;

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
        $pages = array();
        $docs = $this->getPages($urls);
        foreach ($docs as $key => $doc) {
            if (empty($doc)) break;
            $pages[$key] = new Document($doc);
        }
        return $pages;
    }

    /**
     * @param string $links XPATH|DIDom query
     */
    public function setPaternLinks($links = '//a/@href')
    {
        $this->paternLinks = $links;
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
                    (Config::get('usingXPATH') == 1) ? $data[] = $page->find($scratch, Query::TYPE_XPATH) : $data[] = $page->find($scratch);
                }
                $data = $this->output->prepOutput($data);
                $this->data = array_merge($this->data, $data);
                unset($data);
            }
            $this->query = $this->output->prepInsert($this->data);

            if (!empty($this->data) and Config::get('connectDB') == 1) $this->insertDB($this->query);
            if (!empty($this->data) and Config::get('writeBenefitInFile') == 1) $this->writeBenefit($this->data);
        }

        return $this->links;
    }

    /**
     * @param array $urls
     * @param array $scratches
     * @return array
     */
    public function getLinks($urls, $scratches)
    {
        return $this->parsLinks($urls, $scratches);
    }

    /**
     * @param array $urls
     * @param array $scratches
     * @return array
     */
    public function getBenefit($urls, $scratches)
    {
        $this->parsLinks($urls, $scratches);
        return $this->data;
    }

    /**
     * @param array $urls
     * @param array $scratches
     * @return array
     */
    public function getQuery($urls, $scratches)
    {
        $this->parsLinks($urls, $scratches);
        return $this->query;
    }

}
