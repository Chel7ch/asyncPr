<?php

namespace DB;

class FileExecute
{

    public function execInsert($data, $fName = PROJECT)
    {
        file_exists(PROJECT_DIR . '/logs') ?: mkdir(PROJECT_DIR . '/logs');

        $nameFile = PROJECT_DIR . '/logs/' . $fName . '.csv';
        print_r($data);
        $fd = fopen($nameFile, 'a');
        foreach ($data as $d) {
            $d = str_replace('\'', '', $d);
            fputs($fd, $d . PHP_EOL);
        }
        fclose($fd);
    }

    public function execSelect($sql)
    {
    }

    public function cleanTable($fName = PROJECT)
    {
        file_put_contents(PROJECT_DIR . '/logs/' . $fName . '.csv', '');
    }
}
