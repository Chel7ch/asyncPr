<?php

namespace Client;

use Config\Config;

/** Save Html pages in file */

trait SaveHTMLPage
{
    public function saveHTMLPage($html, $name)
    {
        if (Config::get('saveHTMLPage') == 1) {
            file_exists(Config::get('saveHTMLDir')) ?: mkdir(Config::get('saveHTMLDir'));

            $trans = array("https://" => "", "http://" => "", "/" => "~~", "?" => "^");
            $name = strtr("$name", $trans);

            if (!empty($html)) {
                $fd = fopen(Config::get('saveHTMLDir') . '/' . $name . '.html', 'a');
                fputs($fd, $html);
                fclose($fd);
            }
        }
    }
}