<?php

namespace Parser;

use DiDom\Document;
use DiDom\Query;

/**
 * Class ReadSaveFiles crawling files from storage/progects/../htmlPages
 */
class ReadSaveFiles extends ParserRoutine
{
    public function __construct()
    {
        $this->doc = new Document();
    }

    /**
     * @param  $fName
     * @return \DiDom\Document
     */
    public function getFile($fName)
    {
        return $this->doc->loadHtmlfile($fName);
    }

    /**
     * Crawling files from  storage/progects/../htmlPages
     * @param $scratch
     * @return void
     */
    public function rollFiles($scratch)
    {
        $dir = PROJECT_DIR . '/htmlPages';
        if (file_exists($dir)) {
            $files = scandir($dir);

            $files = array_values(array_unique(array_diff($files, array('.', '..', null))));

            foreach ($files as $fname) {

                $doc = $dir . '/' . $fname;
                if (strpos($doc, '.html', -5)) {
                    $trans = array(".html" => "", "~~" => "/");
                    $url = strtr($fname, $trans);

                    $page = $this->getFile($doc);
                    $this->parsFile($page, $url, $scratch);
                }
            }
        }
    }

    /**
     * @param $page DiDom object
     * @param string $url
     * @param array $scratches
     * @return void
     */
    private function parsFile($page, $url, $scratches = [])
    {
        $data[] = $url;
        foreach ($scratches as $scratch) {
            (USING_XPATH == 1) ? $dt = $page->find($scratch, Query::TYPE_XPATH) : $dt = $page->find($scratch);
            $data[] = $this->filter->cleanLinks($dt);
         }

        $this->data = $this->output->prepOutput($data);

        if (CONNECT_DB == 1) {
            if (empty($this->data)) return;
            $this->query = $this->output->prepInsert($this->data);
            $this->insertDB($this->query);
        }else (new \DB\FileExecute)->execInsert($this->data);
    }

    public function getBenefit($fname, $scratches)
    {
        $doc = PROJECT_DIR . '/htmlPages/' . $fname;
        $trans = array(".html" => "", "~~" => "/");
        $url = strtr($fname, $trans);

        $page = $this->getFile($doc);
        $this->parsFile($page, $url, $scratches);
        return $this->data;
    }

    public function getQuery($fname, $scratches)
    {
        $this->getBenefit($fname, $scratches);
        return $this->query;
    }

}

