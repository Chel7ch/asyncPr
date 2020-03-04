<?php

namespace Parser;

/** This is the kit of functions for prepare output */

abstract class PrepareOutput
{
    /**
     * prepare  data for use. In one row
     * @param array $data
     * @return array
     */
    public function straightOutput($data)
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
                if(empty(strip_tags($data[$i][$f]))) $str .= '\'  \' ';
                else{
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
    /**
     * prepare  insert query for DB
     * @param array $data
     * @param string $tabName
     * @return string
     */
    public function prepInsertDB($data)
    {
        $query = '';
        if(empty($data)){
            return $query;
        }

        $tab = 'INSERT INTO ' . TAB_NAME . '(links,';
        $val = ' VALUES';
        for ($i = 0; $i < TAB_FIELDS; $i++)
            $tab .= 'field' . ($i + 1) . ',';

        for ($i = 0; $i < count($data); $i++)
            $val .= '(' . $data[$i] . '),';

        $tab = substr($tab, 0, -1);
        $val = substr($val, 0, -1);
        $query = $tab . ')' . $val . ';';

        return $query;
    }

}
