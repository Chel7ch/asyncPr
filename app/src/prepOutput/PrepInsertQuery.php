<?php

namespace Prepeare;

abstract class PrepInsertQuery
{
//    public static $tabName = TAB_NAME ;

    public function prepInsert($data, $tabName = TAB_NAME )
    {
        $query = '';
        if(empty($data)){
            return $query;
        }

        if($tabName == TAB_NAME) $firstRow = 'INSERT INTO ' . TAB_NAME . '(links,';
        else $firstRow = 'INSERT INTO ' . $tabName . '(';

        $tab = $firstRow;

        for ($i = 0; $i < TAB_FIELDS; $i++) {
            $tab .= 'field' . ($i+1) . ',';
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
