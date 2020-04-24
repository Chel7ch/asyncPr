<?php

namespace Client;

use Config\Config;
use Proxy\CookingProxy;

trait LogErrorResponse
{
    public static $zeroUrl = array();

    public function errorResponse($response, $url, $proxy = '')
    {
        if ($response != 200) {
            if ($response == 0 and CookingProxy::$firstPage > 1) {
                CookingProxy::replace($proxy);

                if (!array_search($url, self::$zeroUrl)) {
                    self::$zeroUrl[] = $url;

                    $errReq = array($url);
                    $this->writeErrResp($errReq, Config::get('zeroErrRespFile'));
                }
            } elseif ($response != 0) {
                $errReq = array($response, $url);
                $this->writeErrResp($errReq, Config::get('errRespFile'));
            }
        }
    }

    public function writeErrResp($errReq, $nameFile)
    {
        file_exists(Config::get('logErrRespDir')) ?: mkdir(Config::get('logErrRespDir'));
        $fd = fopen($nameFile, 'a');
        fputcsv($fd, $errReq);
        fclose($fd);
    }

    public function readErrorURL($nameFile)//!!!!!!!!!!!!!
    {
        $list = array();

        if (file_exists($nameFile) && ($fp = fopen($nameFile, "r")) !== FALSE) {
            while (!feof($fp)) {
                $str = htmlentities(fgets($fp));
                if ($str != '') $list[] = strstr($str, 'http');
            }
            fclose($fp);

            file_put_contents($nameFile, '');

            if ($nameFile == Config::get('zeroErrRespFile')) self::$zeroUrl = array();

            $list = str_replace(array("\r", "\n"), "", $list);
            $list = array_values(array_unique(array_diff($list, array('', 0, null))));

            return $list;
        }
    }
}