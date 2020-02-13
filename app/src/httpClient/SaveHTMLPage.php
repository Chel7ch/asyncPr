<?php
namespace Client;

trait SaveHTMLPage
{
    public function saveHTMLPage($html, $name)
    {
        if (SAVE_HTML_PAGE == 1) {
            file_exists(PROJECT_DIR. '/htmlPages')? :mkdir(PROJECT_DIR. '/htmlPages');
//            if (!file_exists(PROJECT_DIR . '/htmlPages')) mkdir(PROJECT_DIR . '/htmlPages');
            $trans = array("https://" => "", "http://" => "");
            $name = strtr("$name", $trans);

            $fd = fopen(PROJECT_DIR . '/htmlPages/' . $name . '.html', 'a');
            fputs($fd, $html);
            fclose($fd);
        }
    }
}