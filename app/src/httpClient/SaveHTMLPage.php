<?php

namespace Client;

use Config\Config;

trait SaveHTMLPage
{
    public function saveHTMLPage($html, $name)
    {
        if (Config::get('saveHTMLPage') == 1) {
            file_exists(Config::get('saveHTMLPages')) ?: mkdir(Config::get('saveHTMLPages'));

            $trans = array("https://" => "", "http://" => "", "/" => "~~");
            $name = strtr("$name", $trans);

            if (!empty($html)) {
                $fd = fopen(Config::get('saveHTMLPages') . $name . '.html', 'a');
                fputs($fd, $html);
                fclose($fd);
            }
        }
    }
}