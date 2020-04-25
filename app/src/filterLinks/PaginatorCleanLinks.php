<?php

namespace FilterLinks;

use Config\Config;

class PaginatorCleanLinks implements ICleanLinks
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

            // убираем якоря
            if (stristr($urn, '#')) {
                $urn = stristr($urn, '#', true);
            }

            // если пагинатор то берем
            if (stristr($urn, Config::get('tail'))) {
                // создаем  URN
                $link[] = Config::get('url') . $urn;
            }
        }
        if (!empty($link)) $link = array_values(array_unique($link));
        return $link;
    }
}