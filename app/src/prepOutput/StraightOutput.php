<?php

namespace Prepare;

class StraightOutput extends  PrepInsertQuery implements IPrepareOutput
{
    public function prepOutput($data)
    {
        $str = '';
        $dt[] = '\'' . $data[0] . '\'';
        for ($i = 1; $i < count($data); $i++) {
            for ($f = 1; $f < count($data[$i]); $f++) {
                $str .= '\'' . strip_tags($data[$i][$f]) . '\', ';
            }
            $dt[] = substr($str, 0, -2);
        }
        return $dt;
    }

}
