<?php

namespace Parser;

/**
 * Class ReadSaveFiles crawling files from storage/progects/../htmlPages
 * @package Parser
 */
class ReadSaveFiles extends ParserDiDOM
{
    /**
     * Crawling files from storage/progects/../htmlPages
     * @param $scratch
     * @return void
     */
    public function getFile($scratch)
    {
        $i = 1;
        $dir = PROJECT_DIR . '/htmlPages';
        if (file_exists($dir)) {
            $files = scandir($dir);

            foreach ($files as $fname) {
                $doc = $dir . '/' . $fname;
                if (strpos($doc, '.html', -5)) {

                    $trans = array(".html" => "", "~~" => "/");
                    $fn = strtr("$fname", $trans);

                    $b = $this->parsFile($doc)->prepOutput($this->benefit($fn, $scratch));
                    if (!empty($b)) $this->insertDB($b);
                }
            }
        }
    }

    /**
     * @param  $fName
     * @return ReadSaveFiles
     */
    public function parsFile($fName)
    {
        $this->page = $this->doc->loadHtmlfile($fName);

        return $this;
    }

}

