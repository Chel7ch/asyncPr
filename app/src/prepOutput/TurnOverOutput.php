<?php

namespace Prepeare;

class TurnOverOutput  extends  PrepInsertQuery implements IPrepeareOutput
{

    public function prepOutput($data)
    {
        /** deff count arrays
         * @var  integer $maxCount number of elements in the largest array  in $data
         * @var integer $numbArray number of arrays in $data
         */

        $maxCount = 0;
        $numbArray = 0;
        $dt = array();
        foreach ($data as $a) {
            $numbArray++;
            $c = count((array)$a);
            if ($maxCount < $c) $maxCount = $c;
        }
        /** prepare data   */
        for ($f = 0; $f < $maxCount; $f++) {
            $str = '\'' . $data[0] . '\', ';
            for ($i = 1; $i < $numbArray; $i++) {

                if (empty(strip_tags($data[$i][$f]))) $str .= '\'  \' ';
                else {
                    /** deleting apostrophes*/
                    $a = str_replace("'", '', strip_tags($data[$i][$f]));
                    /** escaping single quotes*/
//                    $a = str_replace("'", '\\\'', strip_tags($data[$i][$f]));

                    $str .= '\'' . $a . '\' ';
                }
            }
            $dt[] = substr($str, 0, -1);
        }

        return $dt;
    }

}
