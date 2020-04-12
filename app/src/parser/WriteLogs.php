<?php

namespace Parser;

use Config\Config;

trait WriteLogs
{
    public function writelogs($data, $nameFile = 'namefile')
    {
        $nameFile = Config::get('logErrRespDir') . '/' . $nameFile . '.csv';

        file_exists(Config::get('logErrRespDir')) ?: mkdir(Config::get('logErrRespDir'));
        $fd = fopen($nameFile, 'a');
        foreach ($data as $d) {
            fputs($fd, $d . PHP_EOL);
        }
        fclose($fd);
    }

    public function readlogs($nameFile = 'namefile')
    {
        $nameFile = Config::get('logErrRespDir') . '/' . $nameFile . '.csv';

        $string = '';
        if (file_exists($nameFile) && ($fp = fopen($nameFile, "r")) !== FALSE) {
            while (!feof($fp)) {
                $str = fgets($fp);
                $string .= $str . ',';
            }
            fclose($fp);

            $string = str_replace(array("\r", "\n"), "", $string);
            $arr = explode(",", $string);
            $arr = array_values(array_unique(array_diff($arr, array('', 0, null))));

            return $arr;
        }
    }
}