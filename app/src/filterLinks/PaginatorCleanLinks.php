<?php

namespace FilterLinks;

class PaginatorCleanLinks implements ICleanLinks
{

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
            if (stristr($urn, TAIL)) {
                // создаем  URN
                $link[] = URL . $urn;
            }
        }
        if (!empty($link)) $link = array_values(array_unique($link));
        return $link;
    }
}