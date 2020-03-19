<?php

namespace FilterLinks;

class NoCleanLinks implements ICleanLinks
{
    public function cleanLinks($links)
    {
        return $links;
    }
}