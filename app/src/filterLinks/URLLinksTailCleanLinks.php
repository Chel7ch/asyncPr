<?php
namespace FilterLinks;

use Config\Config;

class URLLinksTailCleanLinks implements ICleanLinks
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

            $link[] = Config::get('url') . $urn. Config::get('tail');
        }
        if (!empty($link)) $link = array_values(array_unique($link));

        return $link;
    }
}
