<?php


namespace Prepare;


use Config\Config;

/** prepare  data from hidemy.name for use. */

class PrHidemyName extends PrepInsertQuery implements IPrepareOutput
{
    /**
     * @param array $data
     * @return array
     */
    public function prepOutput($data)
    {
        /** deff count arrays
         * @var  integer $maxCount number of elements in the largest array  in $data
         */
        $maxCount = 0;
        $dt = array();

        foreach ($data as $a) {
            $c = count((array)$a);
            if ($maxCount < $c) $maxCount = $c;
        }
        /** prepare data   */
        for ($f = 0; $f < $maxCount; $f++) {
            $str = '';
            if (empty(@strip_tags($data[1][$f])) or empty(@strip_tags($data[2][$f]))) {
                break;
            } else  $str .= '\'' . strip_tags($data[1][$f]) . ':' . strip_tags($data[2][$f]) . '\', ';

            (empty($a = strip_tags($data[3][$f]))) ? $str .= '\'  \',' : $str .= '\'' . $a . '\', ';

            (strip_tags($data[4][$f]) == 'Высокая') ? $str .= '\'да\'' . ', ' : $str .= '\'нет\'' . ', ';

            if (empty(strip_tags($data[5][$f]))) $str .= '\'  \' ';
            else {
                /** deleting apostrophes*/
                $a = str_replace("'", '', strip_tags($data[5][$f]));
                $str .= '\'' . $a . '\' ';
            }

            $dt[] = $str;
        }
        return $dt;
    }

    public function prepInsert($data, $tabName = 'collect_proxy')
    {
        $query = '';
        if (empty($data)) {
            return $query;
        }

        $tab = $firstRow = 'INSERT INTO ' . $tabName . '(';

        for ($i = 1; $i < Config::get('tabFields'); $i++) {
            $tab .= 'field' . ($i) . ',';
        }
        $val = ' VALUES';
        for ($i = 0; $i < count($data); $i++) {
            $val .= '(' . $data[$i] . '),';
        }

        $tab = substr($tab, 0, -1);
        $val = substr($val, 0, -1);
        $query = $tab . ')' . $val . ';';

        return $query;
    }
}