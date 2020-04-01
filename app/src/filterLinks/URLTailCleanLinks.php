<?php

namespace FilterLinks;

class URLTailCleanLinks implements ICleanLinks
{

    public function cleanLinks($links)
    {
        $link = array();
        foreach ($links as $urn) {
            $urn = urldecode(trim($urn));

            $link[] = URL . TAIL . $urn;
        }
        if (!empty($link)) $link = array_values(array_unique($link));

        return $link;
    }
}