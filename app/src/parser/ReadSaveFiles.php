<?php

namespace Parser;

use DiDom\Query;

/**
 * Class ReadSaveFiles crawling files from storage/progects/../htmlPages
 */
class ReadSaveFiles extends ParserPage
{
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
    public function parsFile($page, $url, $scratches = [])
    {
        $data[] = $url;
        foreach ($scratches as $scratch) {
            (USING_XPATH == 1) ? $data[] = $page->find($scratch, Query::TYPE_XPATH) : $data[] = $page->find($scratch);
        }
        $this->data = $this->output->prepOutput($data);

        if (PREP_QUERY_FOR_DB == 1) {
            $this->query = $this->output->prepInsert($this->data);

            if (empty($this->query)) return;
            if (CONNECT_DB == 1) $this->insertDB($this->query);
        }
    }

    /**
     * @param  $fName
     * @return \DiDom\Document
     */
    public function getFile($fName)
    {
        return $this->doc->loadHtmlfile($fName);
    }

}

