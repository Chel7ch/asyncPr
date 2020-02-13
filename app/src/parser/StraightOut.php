<?php

namespace Parser;

trait StraightOut{

    /**
     * prepare  data for use. In one row
     * @param array $data
     * @return array
     */
    public function straightOutput($data)
    {
        $str = '';
        echo '<pre>';
        $dt[] = '\'' . $data[0] . '\'';
        for ($i = 1; $i < count($data); $i++) {
            for ($f = 1; $f < count($data[$i]); $f++) {
                $str .= '\'' . $data[$i][$f] . '\', ';
            }
            $dt[] = '"' . substr($str, 0, -2) . '"';
        }
        return $dt;
    }
}
