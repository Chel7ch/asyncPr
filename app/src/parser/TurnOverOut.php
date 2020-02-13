<?php

namespace Parser;

trait TurnOverOut{

    /**
     * prepare  data for use. Each element in apart row
     * @param array $data
     * @return array
     */
    public function turnOverOutput($data)
    {
        /** deff count arrays
         * @var  integer $maxCount number of elements in the largest array  in $data
         * @var integer $numbArray number of arrays in $data         */

        $maxCount = 0;
        $numbArray = 0;
        $dt =array();
        foreach ($data as $a) {
            $numbArray++;
            $c = count((array)$a);
            if ($maxCount < $c) $maxCount = $c;
        }
        /** prepare data   */
        for ($f = 0; $f < $maxCount; $f++) {
            $str = '\'' . $data[0] . '\', ';
            for ($i = 1; $i < $numbArray; $i++) {
                (empty($data[$i][$f])) ? $str .= '\' \',' : $str .= '\'' . $data[$i][$f] . '\', ';
            }
            $dt[] = substr($str, 0, -2);
        }

        return $dt;
    }
}
