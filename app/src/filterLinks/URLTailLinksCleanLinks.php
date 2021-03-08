<?php

namespace FilterLinks;

use Config\Config;

/** Сhanges each received link from the list */

class URLTailLinksCleanLinks implements ICleanLinks
{

    /**
     * @param array $links
     * @return array
     */
    public function cleanLinks($links)
    {
        $link = array();
        foreach ($links as $urn) {
            $urn = urldecode(trim($urn));
            
            $link[] = Config::get('url') . Config::get('tail') . $urn;
        }
        if (!empty($link)) $link = array_values(array_unique($link));

        return $link;
    }
}