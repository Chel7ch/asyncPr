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
     * @param string $patern
     * @return void
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
     * @return void
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

    public function getBenefit($fname, $scratches)
    {
        $doc = Config::get('saveHTMLDir') . '/' . $fname;
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

