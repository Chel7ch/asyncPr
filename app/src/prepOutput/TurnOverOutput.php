<?php

namespace Prepare;

use Config\Config;

class TurnOverOutput  extends  PrepInsertQuery implements IPrepareOutput
{

    public function prepOutput($data)
    {
        /** deff count arrays
         * @var  integer $maxCount number of elements in the largest array  in $data
         * @var integer $numbArray number of arrays in $data
         */
        $maxCount = 0;
        $numbArray = 0;
        $str ='';
        $dt = array();
        foreach ($data as $a) {
            $numbArray++;
            $c = count((array)$a);
            if ($maxCount < $c) $maxCount = $c;
        }
        /** prepare data   */
        for ($f = 0; $f < $maxCount; $f++) {
            (Config::get('outputWithUrl') == 1)? $str = '\'' . $data[0] . '\', ': $str ='' ;

            for ($i = 1; $i < $numbArray; $i++) {

                if (empty(@strip_tags($data[$i][$f]))) $str .= '\'  \', ';
                else {
                    /** deleting apostrophes*/
                    $a = str_replace("'", '', strip_tags($data[$i][$f]));
                    /** escaping single quotes*/
//                    $a = str_replace("'", '\\\'', strip_tags($data[$i][$f]));

                    $str .= '\'' . $a . '\', ';
                }
            }
            $dt[] = substr($str, 0, -2);
        }

        return $dt;
    }

}
