<?php

namespace Prepare;

use Config\Config;

/**   Get benefit data  and  prepare for use. One array to one row */
class StraightOutput extends  PrepInsertQuery implements IPrepareOutput
{
    /**
     * @param array $data
     * @return array
     */
    public function prepOutput($data)
    {
        $str = '';
        $dt = array();

        (Config::get('outputWithUrl') == 1)? $dt[] = '\'' . $data[0] . '\'': $str ='' ;
        for ($i = 1; $i < count($data); $i++) {
            for ($f = 1; $f < count($data[$i]); $f++) {
                $str .= '\'' . strip_tags($data[$i][$f]) . '\', ';
            }
            $dt[] = substr($str, 0, -2);
        }
        return $dt;
    }

}
