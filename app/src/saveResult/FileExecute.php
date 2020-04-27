<?php

namespace DB;

use Config\Config;

class FileExecute
{
    public function execInsert($data, $fName = PROJECT)
    {
        file_exists(Config::get('logErrRespDir')) ?: mkdir(Config::get('logErrRespDir'));

        $nameFile = Config::get('logErrRespDir') . '/' . $fName . '.csv';
        $fd = fopen($nameFile, 'a');
        foreach ($data as $d) {
            $d = str_replace(array("\r", "\n",'\''), '', $d);

            fputs($fd, $d . PHP_EOL);
        }
        fclose($fd);
    }

    public function execSelect($nameFile = PROJECT)
    {
        $nameFile = Config::get('logErrRespDir') . '/' . $nameFile . '.csv';

        $str = array();
        if (file_exists($nameFile) && ($fp = fopen($nameFile, "r")) !== FALSE) {
            while (!feof($fp)) {
                $str[] = fgets($fp);
            }
            fclose($fp);

            $str = str_replace(array("\r", "\n"), "", $str);
            $str = array_diff($str, array('', 0, null));
            $str = array_unique($str);
        }

        return $str;
    }

    public function cleanFile($fName = PROJECT)
    {
        file_put_contents(Config::get('logErrRespDir') . '/' . $fName . '.csv', '');
    }

}
