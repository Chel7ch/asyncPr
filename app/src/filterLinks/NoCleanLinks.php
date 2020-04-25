<?php

namespace FilterLinks;

class NoCleanLinks implements ICleanLinks
{
    /**
     * @param array $links
     * @return array
     */
    public function cleanLinks($links)
    {
        return $links;
    }
}