<?php

namespace Parser;

use Config\Config;
use DiDom\Document;
use DiDom\Query;

/**
 * Class ReadSavedFiles crawling files from storage/progects/../htmlPages
 */
class ReadSavedFiles extends ParserRoutine
{
    public function __construct()
    {
        $this->doc = new Document();
    }

    /**
     * @param  string $fName
     * @return \DiDom\Document
     */
    public function getFile($fName)
    {
        return $this->doc->loadHtmlfile($fName);
    }

    /**
     * Crawling files from  storage/progects/../htmlPages
     * @param array $scratch
     * @param string $patern
     */
    public function rollFiles($scratch, $patern = '\w+')
    {
        $files = $this->getLinks($patern);

        foreach ($files as $fname) {
            $this->getBenefit($fname, $scratch);
        }
    }

    /**
     * @param $page DiDom object
     * @param string $url
     * @param array $scratches
     */
    private function parsFile($page, $url, $scratches = [])
    {
        $data[] = $url;
        foreach ($scratches as $scratch) {
            (Config::get('usingXPATH') == 1)
                ? $dt = $page->find($scratch, Query::TYPE_XPATH) : $dt = $page->find($scratch);
            $data[] = $dt;
        }

        $this->data = $this->output->prepOutput($data);
        if (empty($this->data)) return;

        $this->query = $this->output->prepInsert($this->data);
        if (Config::get('connectDB') == 1) {
            $this->insertDB($this->query);
        } else (new \DB\FileExecute)->execInsert($this->data);
    }

    /**
     * @param string $patern
     * @return array
     */
    public function getLinks($patern = '\w+')
    {
        $match = array();

        if (file_exists(Config::get('saveHTMLDir'))) $files = scandir(Config::get('saveHTMLDir'));

        foreach ($files as $file) {
            if (preg_match("#$patern#", $file) and strpos($file, '.html', -5)) {
                $match[] = $file;
            }
        }

        return $match;
    }

    /**
     * @param string $fname
     * @param array $scratches
     * @return array
     */
    public function getBenefit($fname, $scratches)
    {
        $doc = Config::get('saveHTMLDir') . '/' . $fname;
        $trans = array(".html" => "", "~~" => "/");
        $url = strtr($fname, $trans);

        $page = $this->getFile($doc);
        $this->parsFile($page, $url, $scratches);

        return $this->data;
    }

    /**
     * @param string $fname
     * @param array $scratches
     * @return string
     */
    public function getQuery($fname, $scratches)
    {
        $this->getBenefit($fname, $scratches);

        return $this->query;
    }

}

